<?php
// /index.php

// 检查是否安装
if (!file_exists(__DIR__ . '/install.lock')) {
    header('Content-Type: text/html; charset=utf-8');
    die('<h3>系统未安装，请访问 <a href="install.php">install.php</a></h3>');
}
require_once __DIR__ . '/db.php';

// 模式一：HTML带字体样式输出 (设置为 true)
// 模式二：纯文本输出 (设置为 false)
$enableHtmlOutput = true;
$fontSize = "15px";
$mobileFontSize = "17px";

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 提取内容的辅助函数
function apply_custom_extraction_rules($content) {
    $start_markers = ['voice.google.com> ', 'View in browser'];
    $end_markers = ['To respond to this message, launch','To respond to this text message','YOUR ACCOUNT <https','如要回复此消息', '要回复此短信，','请回复此电子邮件或访问','您的账号 <https://voice.'];
    $found_start_marker = null; $found_start_pos = -1; $found_end_marker = null; $found_end_pos = -1;
    foreach ($start_markers as $marker) { $pos = strpos($content, $marker); if ($pos !== false) { $found_start_marker = $marker; $found_start_pos = $pos; break; } }
    foreach ($end_markers as $marker) { $pos = strpos($content, $marker); if ($pos !== false) { $found_end_marker = $marker; $found_end_pos = $pos; break; } }
    if ($found_start_marker !== null && $found_end_marker !== null) { if ($found_end_pos > $found_start_pos) { $slice_start = $found_start_pos + strlen($found_start_marker); $slice_length = $found_end_pos - $slice_start; return substr($content, $slice_start, $slice_length); } }
    if ($found_start_marker !== null && $found_end_marker === null) { $slice_start = $found_start_pos + strlen($found_start_marker); return substr($content, $slice_start); }
    if ($found_start_marker === null && $found_end_marker !== null) { return substr($content, 0, $found_end_pos); }
    return $content;
}

// 寻找最佳邮件内容的辅助函数
function find_best_part_structure($structure, $mime_type, &$found_html = null) {
    if (isset($structure->parts)) {
        foreach ($structure->parts as $index => $part) {
            $part->part_number = isset($structure->part_number) ? $structure->part_number . '.' . ($index + 1) : ($index + 1);
            if ($part->type == 0 && strtoupper($part->subtype) == 'PLAIN' && $mime_type == 'text/plain') return $part;
            if (isset($part->parts)) { $found = find_best_part_structure($part, $mime_type, $found_html); if ($found) return $found; }
            if ($part->type == 0 && strtoupper($part->subtype) == 'HTML' && $found_html === null) $found_html = $part;
        }
    }
    if ($mime_type == 'text/html' && $found_html !== null) return $found_html;
    if ($structure->type == 0 && strtoupper($structure->subtype) == 'PLAIN' && $mime_type == 'text/plain') { if (!isset($structure->part_number)) $structure->part_number = 1; return $structure; }
    if ($structure->type == 0 && strtoupper($structure->subtype) == 'HTML' && $mime_type == 'text/html') { if (!isset($structure->part_number)) $structure->part_number = 1; return $structure; }
    return null;
}

try {
    $code = ltrim($_GET['code'] ?? '', '/');
    if (empty($code)) throw new Exception('链接错误！！！');

    // 1. 从数据库查询码配置
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM verification_codes WHERE code = ? AND is_expired = 0");
    $stmt->execute([$code]);
    $row = $stmt->fetch();
    
    if (!$row) throw new Exception('查询码错误或已过期，请联系商家！！！');

    // 解析 JSON 字段
    $match_sender = json_decode($row['match_sender'], true) ?? [];
    $match_keywords = json_decode($row['match_keywords'], true) ?? [];

    // 2. 根据端口号动态判断使用IMAP还是POP3协议
    if ($row['port'] == 993) {
        $protocol = "/imap/ssl/novalidate-cert";
    } elseif ($row['port'] == 995) {
        $protocol = "/pop3/ssl/novalidate-cert";
    } else {
        $protocol = "/pop3/notls";
    }
  
    $mailbox_string = "{{$row['host']}:{$row['port']}{$protocol}}INBOX";
    $inbox = imap_open($mailbox_string, $row['user'], $row['pass']);
    if ($inbox === false) throw new Exception("邮箱服务连接失败或调试数据错误！！！");

    // 3. 抓取邮件逻辑
    $emails = imap_search($inbox, 'ALL');
    $final_result = null;

    if ($emails) {
        rsort($emails);
        $emails_to_check = array_slice($emails, 0, 3); // 检查最新的3封

        foreach ($emails_to_check as $msg_number) {
            $header = imap_headerinfo($inbox, $msg_number);

            // 匹配发件人
            if (!empty($match_sender)) {
                $sender_match = false;
                $addresses_to_check = $header->fromaddress ?? '';
                $raw_header = imap_fetchheader($inbox, $msg_number);
                if ($raw_header && preg_match('/^X-Forwarded-For:\s*(.*)$/im', $raw_header, $matches)) {
                    $addresses_to_check .= ' ' . trim($matches[1]);
                }
                foreach ($match_sender as $sender_keyword) {
                    if (!empty($sender_keyword) && stripos($addresses_to_check, $sender_keyword) !== false) {
                        $sender_match = true; break;
                    }
                }
                if (!$sender_match) continue;
            }

            // 处理时间
            $date_string = $header->date;
            try {
                $datetime = new DateTime($date_string);
                $datetime->setTimezone(new DateTimeZone('Asia/Shanghai'));
                $formatted_date = $datetime->format('Y-m-d H:i:s');
            } catch (Exception $e) {
                $formatted_date = (new DateTime('now', new DateTimeZone('Asia/Shanghai')))->format('Y-m-d H:i:s');
            }
            
            // 处理邮件正文解码
            $structure = imap_fetchstructure($inbox, $msg_number);
            $body = ''; $is_html = false;
            $part_structure = find_best_part_structure($structure, 'text/plain');
            if (!$part_structure) { $part_structure = find_best_part_structure($structure, 'text/html'); if ($part_structure) $is_html = true; }

            if ($part_structure) {
                $part_number = $part_structure->part_number;
                $body = imap_fetchbody($inbox, $msg_number, $part_number);
                if ($part_structure->encoding == 3) { $body = base64_decode($body); } elseif ($part_structure->encoding == 4) { $body = quoted_printable_decode($body); }
                $charset = 'UTF-8';
                if (!empty($part_structure->parameters)) { foreach ($part_structure->parameters as $param) { if (strtoupper($param->attribute) == 'CHARSET') { $charset = $param->value; break; } } }
                $body = iconv(strtoupper($charset), 'UTF-8//IGNORE', $body);
                if ($is_html && is_string($body)) {
                    $body = preg_replace(['/<style.*?<\/style>/is', '/<script.*?<\/script>/is'], '', $body);
                    $body = str_ireplace(['<br>','<br/>','</p>','</div>','</tr>','</li>'], "\n", $body);
                    $body = strip_tags($body);
                    $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5);
                }
            }
            
            $content = preg_replace('/\s+/', ' ', $body ?? '');
            $content = apply_custom_extraction_rules($content);
            $final_string = $formatted_date . ' | ' . trim($content);

            // 匹配关键词
            if (!empty($match_keywords)) {
                $keyword_match = false;
                if (!empty(trim(explode('|', $final_string, 2)[1] ?? ''))) {
                    foreach ($match_keywords as $keyword) {
                        if (!empty($keyword) && strpos($final_string, $keyword) !== false) {
                            $keyword_match = true; break;
                        }
                    }
                }
                if (!$keyword_match) continue;
            }
            
            $final_result = $final_string;
            break; // 找到符合条件的邮件就退出循环
        }
    }

    imap_close($inbox);
    
    // 4. 输出结果
    if ($enableHtmlOutput) {
        header('Content-Type: text/html; charset=utf-8');
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<style>
            body { background-color: #f8f9fa; font-family: 'Microsoft YaHei', sans-serif; padding: 20px; }
            .content-output { background: #fff; border: 1px solid #e0e0e0; border-left: 4px solid #0d6efd; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-size: {$fontSize}; font-weight: 500; word-wrap: break-word; white-space: pre-wrap; margin: 0 auto; max-width: 800px; }
            @media (max-width: 670px) { .content-output { font-size: {$mobileFontSize}; } }
        </style>";
        
        if ($final_result !== null) {
            echo "<pre class='content-output'>" . htmlspecialchars($final_result) . "</pre>";
        } else {
            echo "<div class='content-output' style='border-left-color: #dc3545; color: #dc3545;'>☹ 没有新短信或验证码！！！</div>";
        }
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo $final_result !== null ? $final_result : "☹ 没有新短信！！！";
    }
    exit;

} catch (Exception $e) {
    if ($enableHtmlOutput) {
        header('Content-Type: text/html; charset=utf-8');
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<style>.content-output { font-size: {$fontSize}; padding: 15px; border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24; border-radius: 6px; font-family: sans-serif; max-width: 800px; margin: 20px auto; } @media (max-width: 670px) { .content-output { font-size: {$mobileFontSize}; } }</style>";
        echo "<div class='content-output'>☹ " . htmlspecialchars($e->getMessage()) . "</div>";
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo $e->getMessage();
    }
    exit;
}
