<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统登录 - 接码后台管理</title>
    <link href="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { border: none; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); width: 100%; max-width: 400px; padding: 2rem; background: #fff; }
        .login-card .brand { font-size: 1.5rem; font-weight: bold; color: #0d6efd; text-align: center; margin-bottom: 2rem; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand"><i class="fas fa-shield-alt"></i> 接码系统 Pro</div>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger py-2">
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="admin.php?action=login">
        <div class="mb-3">
            <label class="form-label text-muted">管理员账号</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                <input type="text" name="username" class="form-control" required placeholder="输入账号">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label text-muted">管理员密码</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" required placeholder="输入密码">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">登 录</button>
    </form>
</div>

</body>
</html>
