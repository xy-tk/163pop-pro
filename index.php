<?php
// 检查是否安装
if (!file_exists(__DIR__ . '/install.lock')) {
    header('Content-Type: text/html; charset=utf-8');
    die('<h3>系统未安装，请访问 <a href="install.php">install.php</a></h3>');
}
require_once __DIR__ . '/db.php'; // 引入数据库
$enableHtmlOutput = true;
$fontSize = "15px";
$mobileFontSize = "17px";
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try {
    $code = ltrim($_GET['code'] ?? '', '/');
    if (empty($code)) throw new Exception('链接错误！！！');

    // 直接从数据库通过查询码寻找配置
    $stmt = $pdo->prepare("SELECT * FROM verification_codes WHERE code = ? AND is_expired = 0");
    $stmt->execute([$code]);
    $row = $stmt->fetch();
    if (!$row) throw new Exception('查询码错误或已过期！！！');

    // 解析 JSON 字段
    $row['match_sender'] = json_decode($row['match_sender'], true) ?? [];
    $row['match_keywords'] = json_decode($row['match_keywords'], true) ?? [];

    // ====== 下方保留您原有的 IMAP 收信逻辑代码 ======
    // $inbox = imap_open(...); 
    // imap_search(...);
    // ...
    // =================================================

    $final_result = "模拟提取的验证码内容"; // 替换为真实的提取结果变量

    // 输出逻辑
    header('Content-Type: text/html; charset=utf-8');
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    // 引入常用 UI 库以备格式化内容
    echo "<link href='https://cdn.staticfile.org/twitter-bootstrap/5.3.0/css/bootstrap.min.css' rel="stylesheet">";
    echo "<style>.content-output { font-size: {$fontSize}; font-family: sans-serif; white-space: pre-wrap; margin: 20px; } @media (max-width: 670px) { .content-output { font-size: {$mobileFontSize}; } }</style>";
    
    if ($final_result !== null) {
        echo "<pre class='content-output border p-3 bg-light rounded'>{$final_result}</pre>";
    } else {
        echo "<div class='content-output text-danger'>☹ 没有新短信！！！</div>";
    }

} catch (Exception $e) {
    header('Content-Type: text/html; charset=utf-8');
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<div style='margin:20px; color:red;'>☹ " . htmlspecialchars($e->getMessage()) . "</div>";
}