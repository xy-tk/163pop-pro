<?php
session_start();

// 检查是否安装
if (!file_exists(__DIR__ . '/install.lock')) {
    header('Location: install.php');
    exit;
}
require_once __DIR__ . '/db.php'; // 全局载入数据库连接

// 自动加载类
spl_autoload_register(function ($className) {
    $file = __DIR__ . "/classes/{$className}.php";
    if (file_exists($file)) require_once $file;
});

$allowedActions = [
    'login' => 'views/login.php',
    'dashboard' => 'views/dashboard.php',
    'phonenumber' => 'views/phonenumber.php',
    // 其他页面映射...
    'logout' => null,
];

$action = $_GET['action'] ?? 'dashboard';

// 注销逻辑
if ($action === 'logout') {
    Auth::doLogout();
    header('Location: admin.php?action=login');
    exit;
}

// 登录验证逻辑
if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (Auth::doLogin($_POST['username'], $_POST['password'])) {
            header('Location: admin.php');
            exit;
        } else {
            $error_message = "用户名或密码错误。";
        }
    }
    require 'views/login.php';
    exit;
}

// 全局拦截：未登录踢回登录页
if (!Auth::checkLogin()) {
    header('Location: admin.php?action=login');
    exit;
}

// ====== 这里放置后台各种增删改查的数据库逻辑 ======
// 例如: if ($action === 'phonenumber_delete') { $pdo->prepare(...); }
// ===================================================

if (array_key_exists($action, $allowedActions) && $allowedActions[$action] !== null) {
    require $allowedActions[$action]; // 加载对应的视图文件 (它会在内部 include layout.php)
} else {
    header('Location: admin.php?action=dashboard');
    exit;
}