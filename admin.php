<?php
// /admin.php
session_start();

// 1. 安全拦截
if (!file_exists(__DIR__ . '/install.lock')) {
    header('Location: install.php');
    exit;
}

// 2. 引入数据库与类
require_once __DIR__ . '/db.php';
spl_autoload_register(function ($className) {
    $file = __DIR__ . "/classes/{$className}.php";
    if (file_exists($file)) require_once $file;
});

// 3. 路由映射表
$allowedActions = [
    'login' => 'views/login.php',
    'dashboard' => 'views/dashboard.php',
    'phonenumber' => 'views/phonenumber.php',
    'verification_code' => 'views/verification_code.php',
];

$action = $_GET['action'] ?? 'dashboard';

// 4. 退出登录逻辑
if ($action === 'logout') {
    Auth::doLogout();
    header('Location: admin.php?action=login');
    exit;
}

// 5. 登录页面逻辑
if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (Auth::doLogin($_POST['username'], $_POST['password'])) {
            header('Location: admin.php?action=dashboard');
            exit;
        } else {
            $_SESSION['error_message'] = "用户名或密码错误！";
        }
    }
    require 'views/login.php'; // 登录页不需要 layout 骨架，直接加载
    exit;
}

// ==========================================
// 全局拦截：未登录踢回登录页
// ==========================================
if (!Auth::checkLogin()) {
    header('Location: admin.php?action=login');
    exit;
}

// ==========================================
// 数据增删改查 (CRUD) 业务逻辑处理区
// ==========================================

// --- [号码库管理逻辑] ---
if ($action === 'phonenumber_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['phonenumber'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO phonenumbers (phonenumber, host, port, user, pass, match_sender) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($_POST['phonenumber']), trim($_POST['host']), 
                trim($_POST['port'] ?? '995'), trim($_POST['user']), 
                trim($_POST['pass']), trim($_POST['match_sender'] ?? '')
            ]);
            $_SESSION['success_message'] = "号码添加成功！";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "添加失败，该号码可能已存在！";
        }
    }
    header('Location: admin.php?action=phonenumber');
    exit;
}

if ($action === 'phonenumber_delete') {
    if (!empty($_GET['id'])) {
        $stmt = $pdo->prepare("DELETE FROM phonenumbers WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['success_message'] = "号码删除成功！";
    }
    header('Location: admin.php?action=phonenumber');
    exit;
}

// ==========================================
// 视图数据准备与页面渲染加载区
// ==========================================

// 为不同页面准备数据
if ($action === 'dashboard') {
    // 统计总号码数
    $dash_phones = $pdo->query("SELECT COUNT(*) FROM phonenumbers")->fetchColumn();
    // 统计业务码数
    $dash_active_codes = $pdo->query("SELECT COUNT(*) FROM verification_codes WHERE is_expired = 0")->fetchColumn();
    $dash_expired_codes = $pdo->query("SELECT COUNT(*) FROM verification_codes WHERE is_expired = 1")->fetchColumn();
} 
elseif ($action === 'phonenumber') {
    // 拉取所有号码数据供表格显示
    $phoneNumbers = $pdo->query("SELECT * FROM phonenumbers ORDER BY id DESC")->fetchAll();
} 
elseif ($action === 'verification_code') {
    // 拉取业务数据
    $verificationData = $pdo->query("SELECT * FROM verification_codes ORDER BY releasedate DESC")->fetchAll();
}

// 渲染视图体系 (如果动作存在于映射表中)
if (array_key_exists($action, $allowedActions)) {
    // 使用输出缓冲区提取子页面的 HTML
    ob_start();
    require $allowedActions[$action];
    $page_content = ob_get_clean();
    
    // 将子页面 HTML 注入到全局骨架中
    require 'views/layout.php';
} else {
    header('Location: admin.php?action=dashboard');
    exit;
}
?>
