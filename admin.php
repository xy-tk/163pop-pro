<?php
// /admin.php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// ==========================================
// 1. 安全拦截与基础依赖
// ==========================================
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

// ==========================================
// 2. 路由映射表 (安全白名单)
// ==========================================
$allowedActions = [
    'login'             => 'views/login.php',
    'dashboard'         => 'views/dashboard.php',
    'phonenumber'       => 'views/phonenumber.php',
    'classification'    => 'views/classification.php',
    'verification_code' => 'views/verification_code.php',
    'code_manager'      => 'views/code_manager.php',
    'expired_phones'    => 'views/expired_phones.php',
];

$action = $_GET['action'] ?? 'dashboard';

// ==========================================
// 3. 登录与注销逻辑
// ==========================================
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
    exit; // 登录页面独立渲染，直接退出
}

// 全局拦截：未登录踢回登录页
if (!Auth::checkLogin()) {
    header('Location: admin.php?action=login');
    exit;
}

// ==========================================
// 4. 数据处理逻辑 (CRUD - 增删改动作处理)
// ==========================================

// --- 【1】号码库处理逻辑 ---
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
    header('Location: admin.php?action=phonenumber'); exit;
}
if ($action === 'phonenumber_delete') {
    if (!empty($_GET['id'])) {
        $stmt = $pdo->prepare("DELETE FROM phonenumbers WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['success_message'] = "号码删除成功！";
    }
    header('Location: admin.php?action=phonenumber'); exit;
}

// --- 【2】分类规则处理逻辑 ---
if ($action === 'classification_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category_name'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO classifications (category_name, match_keywords) VALUES (?, ?)");
            $stmt->execute([trim($_POST['category_name']), trim($_POST['match_keywords'])]);
            $_SESSION['success_message'] = "分类创建成功！";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "添加失败！";
        }
    }
    header('Location: admin.php?action=classification'); exit;
}
if ($action === 'classification_delete') {
    if (!empty($_GET['id'])) {
        $stmt = $pdo->prepare("DELETE FROM classifications WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['success_message'] = "分类删除成功！";
    }
    header('Location: admin.php?action=classification'); exit;
}

// --- 【3】接码业务任务处理逻辑 ---
if ($action === 'verification_code_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code = trim($_POST['verification_code']);
        if (empty($code)) {
            // 如果为空，自动生成 10 位大写字母+数字的查询码
            $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        }
        
        $phone = $_POST['phonenumber'];
        $category = $_POST['category_name'];
        $days = (float)$_POST['days_to_expire'];
        $user_content = $_POST['user_content'] ?? '';

        // 分别查询被选中的号码配置和分类配置
        $stmtPhone = $pdo->prepare("SELECT * FROM phonenumbers WHERE phonenumber = ?");
        $stmtPhone->execute([$phone]);
        $phoneData = $stmtPhone->fetch();

        $stmtCat = $pdo->prepare("SELECT * FROM classifications WHERE category_name = ?");
        $stmtCat->execute([$category]);
        $catData = $stmtCat->fetch();

        if ($phoneData && $catData) {
            try {
                // 处理格式化
                $match_keywords = json_encode(array_map('trim', explode(',', $catData['match_keywords'])), JSON_UNESCAPED_UNICODE);
                $match_sender = json_encode([$phoneData['match_sender']], JSON_UNESCAPED_UNICODE);
                $combination = json_encode([$phone . '---' . $user_content . '/' . $code], JSON_UNESCAPED_UNICODE);
                $releaseDate = date('Y-m-d H:i:s');
                $expTime = $days . '天';

                $stmt = $pdo->prepare("INSERT INTO verification_codes (code, category, host, port, user, pass, match_keywords, match_sender, releasedate, expirationtime, combination, is_expired) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
                $stmt->execute([$code, $category, $phoneData['host'], $phoneData['port'], $phoneData['user'], $phoneData['pass'], $match_keywords, $match_sender, $releaseDate, $expTime, $combination]);
                
                // 同时把生成的码放入防重码库
                $pdo->prepare("INSERT IGNORE INTO used_codes (code) VALUES (?)")->execute([$code]);
                $_SESSION['success_message'] = '任务分配成功！查询码为：' . htmlspecialchars($code);
            } catch (PDOException $e) {
                $_SESSION['error_message'] = '分配失败，查询码可能已存在！';
            }
        } else {
            $_SESSION['error_message'] = '系统错误：未找到所选的手机号或分类配置！';
        }
    }
    header('Location: admin.php?action=verification_code'); exit;
}
if ($action === 'verification_code_delete') {
    if (!empty($_GET['code'])) {
        $stmt = $pdo->prepare("DELETE FROM verification_codes WHERE code = ?");
        $stmt->execute([$_GET['code']]);
        $_SESSION['success_message'] = "任务删除成功！";
    }
    header('Location: admin.php?action=verification_code'); exit;
}

// --- 【4】防重码库处理逻辑 ---
if ($action === 'used_codes_save') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['verification_code'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO used_codes (code) VALUES (?)");
            $stmt->execute([trim($_POST['verification_code'])]);
            $_SESSION['success_message'] = "占用码入库成功！";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "该码已在库中！";
        }
    }
    header('Location: admin.php?action=code_manager'); exit;
}
if ($action === 'used_codes_delete') {
    if (!empty($_GET['code'])) {
        $stmt = $pdo->prepare("DELETE FROM used_codes WHERE code = ?");
        $stmt->execute([$_GET['code']]);
        $_SESSION['success_message'] = "码已释放！";
    }
    header('Location: admin.php?action=code_manager'); exit;
}

// --- 【5】过期记录处理逻辑 ---
if ($action === 'expired_phones_delete') {
    if (!empty($_GET['code'])) {
        $stmt = $pdo->prepare("DELETE FROM verification_codes WHERE code = ?");
        $stmt->execute([$_GET['code']]);
        $_SESSION['success_message'] = "过期记录彻底删除成功！";
    }
    header('Location: admin.php?action=expired_phones'); exit;
}
if ($action === 'expired_phones_clear_all') {
    $pdo->query("DELETE FROM verification_codes WHERE is_expired = 1");
    $_SESSION['success_message'] = "所有过期记录已清空！";
    header('Location: admin.php?action=expired_phones'); exit;
}

// ==========================================
// 5. 视图数据准备与页面渲染加载区
// ==========================================

// 每次加载前，检查并自动将过期时间到达的任务标记为过期
$pdo->query("UPDATE verification_codes SET is_expired = 1 WHERE is_expired = 0 AND DATE_ADD(releasedate, INTERVAL CAST(REPLACE(expirationtime, '天', '') AS DECIMAL(10,2)) DAY) < NOW()");

if ($action === 'dashboard') {
    $dash_phones = $pdo->query("SELECT COUNT(*) FROM phonenumbers")->fetchColumn();
    $dash_active_codes = $pdo->query("SELECT COUNT(*) FROM verification_codes WHERE is_expired = 0")->fetchColumn();
    $dash_expired_codes = $pdo->query("SELECT COUNT(*) FROM verification_codes WHERE is_expired = 1")->fetchColumn();
} 
elseif ($action === 'phonenumber') {
    $phoneNumbers = $pdo->query("SELECT * FROM phonenumbers ORDER BY id DESC")->fetchAll();
} 
elseif ($action === 'classification') {
    $classifications = $pdo->query("SELECT * FROM classifications ORDER BY id DESC")->fetchAll();
} 
elseif ($action === 'verification_code') {
    $verificationData = $pdo->query("SELECT * FROM verification_codes WHERE is_expired = 0 ORDER BY releasedate DESC")->fetchAll();
    $phoneNumbers = $pdo->query("SELECT phonenumber FROM phonenumbers ORDER BY id DESC")->fetchAll();
    $classifications = $pdo->query("SELECT category_name FROM classifications ORDER BY id DESC")->fetchAll();
}
elseif ($action === 'code_manager') {
    $usedCodesData = $pdo->query("SELECT * FROM used_codes ORDER BY code ASC")->fetchAll();
}
elseif ($action === 'expired_phones') {
    $expiredData = $pdo->query("SELECT * FROM verification_codes WHERE is_expired = 1 ORDER BY releasedate DESC")->fetchAll();
}

// ==========================================
// 6. 最终输出拼装
// ==========================================
if (array_key_exists($action, $allowedActions) && $allowedActions[$action]) {
    // 开启输出缓冲区，提取子页面的 HTML 内容
    ob_start();
    require $allowedActions[$action];
    $page_content = ob_get_clean();
    
    // 将子页面内容注入到全局骨架中
    require 'views/layout.php';
} else {
    header('Location: admin.php?action=dashboard');
    exit;
}
?>
