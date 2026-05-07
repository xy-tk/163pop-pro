<?php
// /admin.php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// 1. 安全拦截
if (!file_exists(__DIR__ . '/install.lock')) {
    header('Location: install.php');
    exit;
}

require_once __DIR__ . '/db.php';

// 自动加载类
spl_autoload_register(function ($className) {
    $file = __DIR__ . "/classes/{$className}.php";
    if (file_exists($file)) require_once $file;
});

// 2. 路由映射表
$allowedActions = [
    'login'             => 'views/login.php',
    'dashboard'         => 'views/dashboard.php',
    'phonenumber'       => 'views/phonenumber.php',
    'classification'    => 'views/classification.php',
    'verification_code' => 'views/verification_code.php',
    'code_manager'      => 'views/code_manager.php',
    'expired_phones'    => 'views/expired_phones.php',
    'change_password'   => 'views/change_password.php',
];

$action = $_GET['action'] ?? 'dashboard';

// 3. 登录与注销
if ($action === 'logout') {
    Auth::doLogout();
    header('Location: admin.php?action=login'); exit;
}

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (Auth::doLogin($_POST['username'], $_POST['password'])) {
            header('Location: admin.php?action=dashboard'); exit;
        } else {
            $_SESSION['error_message'] = "用户名或密码错误！";
        }
    }
    require 'views/login.php'; exit;
}

if (!Auth::checkLogin()) {
    header('Location: admin.php?action=login'); exit;
}

// ==========================================
// 4. 数据处理逻辑 (CRUD - 增删改动作处理)
// ==========================================

// --- 【0】管理员账号修改（适配哈希加密） ---
if ($action === 'change_password_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_username'])) {
        // 对新密码进行加密
        $hashed_password = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);
        
        $pdo->exec("TRUNCATE TABLE admin_users");
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([trim($_POST['new_username']), $hashed_password]);
        
        $_SESSION['success_message'] = "管理员账号密码修改成功！下次登录生效。";
    }
    header('Location: admin.php?action=change_password'); exit;
}

// --- 【1】号码库批量保存 ---
if ($action === 'phonenumber_bulk_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_data'])) {
        $lines = explode("\n", $_POST['bulk_data']);
        $count = 0;
        foreach ($lines as $line) {
            $line = trim($line); if (empty($line)) continue;
            // 支持 主机---端口---账号---密码---发件人---电话
            $parts = preg_split('/\s*---\s*|\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) >= 6) {
                $stmt = $pdo->prepare("INSERT IGNORE INTO phonenumbers (host, port, user, pass, match_sender, phonenumber) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$parts[0], $parts[1], $parts[2], $parts[3], $parts[4], $parts[5]]);
                $count++;
            }
        }
        $_SESSION['success_message'] = "成功导入 {$count} 条号码。";
    }
    header('Location: admin.php?action=phonenumber'); exit;
}

// --- 【2】分类保存 ---
if ($action === 'classification_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category_name'])) {
        $stmt = $pdo->prepare("INSERT INTO classifications (category_name, match_keywords) VALUES (?, ?)");
        $stmt->execute([trim($_POST['category_name']), trim($_POST['match_keywords'])]);
        $_SESSION['success_message'] = "分类保存成功。";
    }
    header('Location: admin.php?action=classification'); exit;
}

// --- 【3】接码业务批量保存 ---
if ($action === 'verification_code_bulk_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_data'])) {
        $lines = explode("\n", $_POST['bulk_data']);
        $category = $_POST['category_name'];
        $days = (float)$_POST['days_to_expire'];
        $user_content = $_POST['user_content'];
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line); if (empty($line)) continue;
            $parts = preg_split('/\s*---\s*|\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $phoneNum = $parts[0];
            $customCode = $parts[1] ?? substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);

            $stmtP = $pdo->prepare("SELECT * FROM phonenumbers WHERE phonenumber = ?");
            $stmtP->execute([$phoneNum]);
            $p = $stmtP->fetch();
            
            $stmtC = $pdo->prepare("SELECT * FROM classifications WHERE category_name = ?");
            $stmtC->execute([$category]);
            $c = $stmtC->fetch();

            if ($p && $c) {
                $mk = json_encode(array_map('trim', explode(',', $c['match_keywords'])), JSON_UNESCAPED_UNICODE);
                $ms = json_encode([$p['match_sender']], JSON_UNESCAPED_UNICODE);
                $cb = json_encode([$phoneNum . '---' . $user_content . '/' . $customCode], JSON_UNESCAPED_UNICODE);
                
                $stmt = $pdo->prepare("INSERT IGNORE INTO verification_codes (code, category, host, port, user, pass, match_keywords, match_sender, releasedate, expirationtime, combination, is_expired) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
                $stmt->execute([$customCode, $category, $p['host'], $p['port'], $p['user'], $p['pass'], $mk, $ms, date('Y-m-d H:i:s'), $days.'天', $cb]);
                
                $pdo->prepare("INSERT IGNORE INTO used_codes (code) VALUES (?)")->execute([$customCode]);
                $count++;
            }
        }
        $_SESSION['success_message'] = "成功分配 {$count} 条接码任务。";
    }
    header('Location: admin.php?action=verification_code'); exit;
}

// --- 【4】业务批量删除 ---
if ($action === 'verification_code_bulk_delete') {
    if (!empty($_POST['selected_items']) && is_array($_POST['selected_items'])) {
        $placeholders = implode(',', array_fill(0, count($_POST['selected_items']), '?'));
        $stmt = $pdo->prepare("DELETE FROM verification_codes WHERE code IN ($placeholders)");
        $stmt->execute($_POST['selected_items']);
        $_SESSION['success_message'] = "批量删除成功。";
    }
    header('Location: admin.php?action=verification_code'); exit;
}

// --- 【5】通用删除逻辑映射 ---
$del_map = [
    'phonenumber_delete' => ['t' => 'phonenumbers', 'k' => 'id', 'r' => 'phonenumber'],
    'classification_delete' => ['t' => 'classifications', 'k' => 'id', 'r' => 'classification'],
    'verification_code_delete' => ['t' => 'verification_codes', 'k' => 'code', 'r' => 'verification_code'],
    'used_codes_delete' => ['t' => 'used_codes', 'k' => 'code', 'r' => 'code_manager'],
    'expired_phones_delete' => ['t' => 'verification_codes', 'k' => 'code', 'r' => 'expired_phones']
];

if (isset($del_map[$action])) {
    $cfg = $del_map[$action];
    $val = $_GET['id'] ?? $_GET['code'];
    if ($val) {
        $pdo->prepare("DELETE FROM {$cfg['t']} WHERE {$cfg['k']} = ?")->execute([$val]);
        $_SESSION['success_message'] = "删除成功。";
    }
    header("Location: admin.php?action={$cfg['r']}"); exit;
}

// 过期清理
if ($action === 'expired_phones_clear_all') {
    $pdo->query("DELETE FROM verification_codes WHERE is_expired = 1");
    $_SESSION['success_message'] = "已清空所有过期记录。";
    header('Location: admin.php?action=expired_phones'); exit;
}

// --- 【6】防重码占用保存 ---
if ($action === 'used_codes_save') {
    if (!empty($_POST['verification_code'])) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO used_codes (code) VALUES (?)");
        $stmt->execute([trim($_POST['verification_code'])]);
        $_SESSION['success_message'] = "码已加入防重库。";
    }
    header('Location: admin.php?action=code_manager'); exit;
}

// 自动过期状态更新
$pdo->query("UPDATE verification_codes SET is_expired = 1 WHERE is_expired = 0 AND DATE_ADD(releasedate, INTERVAL CAST(REPLACE(expirationtime, '天', '') AS DECIMAL(10,2)) DAY) < NOW()");

// ==========================================
// 5. 视图数据准备与页面加载
// ==========================================
if ($action === 'dashboard') {
    $dash_phones = $pdo->query("SELECT COUNT(*) FROM phonenumbers")->fetchColumn();
    $dash_active = $pdo->query("SELECT COUNT(*) FROM verification_codes WHERE is_expired = 0")->fetchColumn();
    $dash_expired = $pdo->query("SELECT COUNT(*) FROM verification_codes WHERE is_expired = 1")->fetchColumn();
} elseif ($action === 'phonenumber') {
    $phoneNumbers = $pdo->query("SELECT * FROM phonenumbers ORDER BY id DESC")->fetchAll();
} elseif ($action === 'classification') {
    $classifications = $pdo->query("SELECT * FROM classifications ORDER BY id DESC")->fetchAll();
} elseif ($action === 'verification_code') {
    $verificationData = $pdo->query("SELECT * FROM verification_codes WHERE is_expired = 0 ORDER BY releasedate DESC")->fetchAll();
    $phoneNumbers = $pdo->query("SELECT phonenumber FROM phonenumbers ORDER BY id DESC")->fetchAll();
    $classifications = $pdo->query("SELECT category_name FROM classifications ORDER BY id DESC")->fetchAll();
} elseif ($action === 'code_manager') {
    $usedCodesData = $pdo->query("SELECT * FROM used_codes ORDER BY code ASC")->fetchAll();
} elseif ($action === 'expired_phones') {
    $expiredData = $pdo->query("SELECT * FROM verification_codes WHERE is_expired = 1 ORDER BY releasedate DESC")->fetchAll();
}

if (array_key_exists($action, $allowedActions)) {
    ob_start();
    require $allowedActions[$action];
    $page_content = ob_get_clean();
    require 'views/layout.php';
} else {
    header('Location: admin.php?action=dashboard'); exit;
}
