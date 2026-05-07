<?php
// /admin.php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (!file_exists(__DIR__ . '/install.lock')) {
    header('Location: install.php');
    exit;
}

require_once __DIR__ . '/db.php';
spl_autoload_register(function ($className) {
    $file = __DIR__ . "/classes/{$className}.php";
    if (file_exists($file)) require_once $file;
});

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

if ($action === 'logout') {
    Auth::doLogout();
    header('Location: admin.php?action=login');
    exit;
}

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (Auth::doLogin($_POST['username'], $_POST['password'])) {
            header('Location: admin.php?action=dashboard');
            exit;
        } else {
            $_SESSION['error_message'] = "用户名或密码错误！";
        }
    }
    require 'views/login.php'; 
    exit;
}

if (!Auth::checkLogin()) {
    header('Location: admin.php?action=login');
    exit;
}

// ==========================================
// 数据处理逻辑 (回归原版：直接页面提交)
// ==========================================

// --- 管理员修改 ---
if ($action === 'change_password_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_username'])) {
        $pdo->exec("TRUNCATE TABLE admin_users");
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([trim($_POST['new_username']), trim($_POST['new_password'])]);
        $_SESSION['success_message'] = "账号密码修改成功！";
    }
    header('Location: admin.php?action=change_password'); exit;
}

// --- 号码库批量保存 ---
if ($action === 'phonenumber_bulk_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_data'])) {
        $lines = explode("\n", $_POST['bulk_data']);
        $count = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            // 支持 主机---端口---账号---密码---发件人---电话 或 空格分隔
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

// --- 分类保存 ---
if ($action === 'classification_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category_name'])) {
        $stmt = $pdo->prepare("INSERT INTO classifications (category_name, match_keywords) VALUES (?, ?)");
        $stmt->execute([trim($_POST['category_name']), trim($_POST['match_keywords'])]);
        $_SESSION['success_message'] = "分类保存成功。";
    }
    header('Location: admin.php?action=classification'); exit;
}

// --- 接码任务批量保存 ---
if ($action === 'verification_code_bulk_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['bulk_data'])) {
        $lines = explode("\n", $_POST['bulk_data']);
        $category = $_POST['category_name'];
        $days = (float)$_POST['days_to_expire'];
        $user_content = $_POST['user_content'];
        $count = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
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

// --- 各种删除动作 ---
$delete_actions = [
    'phonenumber_delete' => ['table' => 'phonenumbers', 'key' => 'id', 'ref' => 'phonenumber'],
    'classification_delete' => ['table' => 'classifications', 'key' => 'id', 'ref' => 'classification'],
    'verification_code_delete' => ['table' => 'verification_codes', 'key' => 'code', 'ref' => 'verification_code'],
    'used_codes_delete' => ['table' => 'used_codes', 'key' => 'code', 'ref' => 'code_manager'],
    'expired_phones_delete' => ['table' => 'verification_codes', 'key' => 'code', 'ref' => 'expired_phones']
];

if (isset($delete_actions[$action])) {
    $config = $delete_actions[$action];
    $val = $_GET['id'] ?? $_GET['code'];
    if ($val) {
        $stmt = $pdo->prepare("DELETE FROM {$config['table']} WHERE {$config['key']} = ?");
        $stmt->execute([$val]);
        $_SESSION['success_message'] = "删除成功。";
    }
    header("Location: admin.php?action={$config['ref']}"); exit;
}

// 自动过期处理
$pdo->query("UPDATE verification_codes SET is_expired = 1 WHERE is_expired = 0 AND DATE_ADD(releasedate, INTERVAL CAST(REPLACE(expirationtime, '天', '') AS DECIMAL(10,2)) DAY) < NOW()");

// 数据加载
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
    $phoneNumbers = $pdo->query("SELECT phonenumber FROM phonenumbers")->fetchAll();
    $classifications = $pdo->query("SELECT category_name FROM classifications")->fetchAll();
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
