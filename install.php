<?php
header('Content-Type: text/html; charset=utf-8');

// 安全拦截：防止重复安装
if (file_exists(__DIR__ . '/install.lock')) {
    die('<h2>系统已安装！</h2><p>如需重装，请删除根目录的 install.lock 并清空数据库。</p><a href="admin.php">进入后台</a>');
}

$error_msg = ''; $success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = trim($_POST['db_host']);
    $db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $admin_user = trim($_POST['admin_user']);
    $admin_pass = trim($_POST['admin_pass']);

    try {
        $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        // 生成配置文件
        $db_config = "<?php\n\$pdo = new PDO('mysql:host={$db_host};dbname={$db_name};charset=utf8mb4', '{$db_user}', '{$db_pass}', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);\n?>";
        file_put_contents(__DIR__ . '/db.php', $db_config);

        // 创建核心表结构
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `phonenumbers` (`id` int(11) NOT NULL AUTO_INCREMENT, `phonenumber` varchar(50) NOT NULL, `host` varchar(100) NOT NULL, `port` varchar(10) DEFAULT '995', `user` varchar(100) NOT NULL, `pass` varchar(100) NOT NULL, `match_sender` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `phonenumber` (`phonenumber`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS `verification_codes` (`code` varchar(50) NOT NULL, `category` varchar(50) NOT NULL, `host` varchar(100) NOT NULL, `port` varchar(10) DEFAULT '995', `user` varchar(100) NOT NULL, `pass` varchar(100) NOT NULL, `match_keywords` json DEFAULT NULL, `match_sender` json DEFAULT NULL, `releasedate` datetime NOT NULL, `expirationtime` varchar(20) NOT NULL, `combination` json DEFAULT NULL, `is_expired` tinyint(1) DEFAULT '0', PRIMARY KEY (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            CREATE TABLE IF NOT EXISTS `admin_users` (`username` varchar(50) NOT NULL, `password` varchar(255) NOT NULL, PRIMARY KEY (`username`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            TRUNCATE TABLE `admin_users`;
        ");
        
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->execute([$admin_user, $admin_pass]);
        file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s'));
        $success_msg = "安装成功！管理员账号已生效。";
    } catch (Exception $e) {
        $error_msg = "安装失败: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统安装向导</title>
    <link href="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">🚀 系统安装向导</h3>
                <?php if ($error_msg): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>
                <?php if ($success_msg): ?>
                    <div class="alert alert-success text-center">
                        <?= $success_msg ?><br><br>
                        <a href="admin.php" class="btn btn-primary w-100">进入后台管理</a>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <h6 class="text-muted">1. 数据库配置 (VPS面板提供)</h6>
                        <input type="text" name="db_host" class="form-control mb-2" value="127.0.0.1" placeholder="数据库地址">
                        <input type="text" name="db_name" class="form-control mb-2" required placeholder="数据库名 (例如: xyfk)">
                        <input type="text" name="db_user" class="form-control mb-2" required placeholder="数据库用户名">
                        <input type="password" name="db_pass" class="form-control mb-4" required placeholder="数据库密码">
                        
                        <h6 class="text-muted">2. 管理员账号配置</h6>
                        <input type="text" name="admin_user" class="form-control mb-2" value="admin" required placeholder="后台登录账号">
                        <input type="text" name="admin_pass" class="form-control mb-4" value="123456" required placeholder="后台登录密码">
                        
                        <button type="submit" class="btn btn-primary w-100 text-white">开始部署</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>