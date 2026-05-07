<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>接码系统管理后台</title>
    <link href="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; font-family: 'Microsoft YaHei', sans-serif; }
        .sidebar { min-height: 100vh; background: #4e73df; background-image: linear-gradient(180deg,#4e73df 10%,#224abe 100%); color: #fff; }
        .sidebar .nav-link { color: rgba(255,255,255,.8); padding: 1rem; border-bottom: 1px solid rgba(255,255,255,.1); transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .main-content { padding: 20px; }
        .card { border: none; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15); }
        .table thead { background: #f8f9fc; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar px-0 shadow">
            <div class="p-3 text-center border-bottom border-white-50 mb-2">
                <h5 class="fw-bold mb-0"><i class="fas fa-sms"></i> 接码系统 Pro</h5>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link <?= $action=='dashboard'?'active':'' ?>" href="admin.php?action=dashboard"><i class="fas fa-tachometer-alt"></i> 控制台</a></li>
                <li class="nav-item"><a class="nav-link <?= $action=='phonenumber'?'active':'' ?>" href="admin.php?action=phonenumber"><i class="fas fa-mobile-alt"></i> 号码库管理</a></li>
                <li class="nav-item"><a class="nav-link <?= $action=='classification'?'active':'' ?>" href="admin.php?action=classification"><i class="fas fa-list"></i> 业务分类管理</a></li>
                <li class="nav-item"><a class="nav-link <?= $action=='verification_code'?'active':'' ?>" href="admin.php?action=verification_code"><i class="fas fa-tasks"></i> 接码业务管理</a></li>
                <li class="nav-item"><a class="nav-link <?= $action=='code_manager'?'active':'' ?>" href="admin.php?action=code_manager"><i class="fas fa-key"></i> 查询码管理</a></li>
                <li class="nav-item"><a class="nav-link <?= $action=='expired_phones'?'active':'' ?>" href="admin.php?action=expired_phones"><i class="fas fa-history"></i> 已过期记录</a></li>
                <li class="nav-item mt-4"><a class="nav-link <?= $action=='change_password'?'active':'' ?>" href="admin.php?action=change_password"><i class="fas fa-user-cog"></i> 账号设置</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="admin.php?action=logout"><i class="fas fa-sign-out-alt"></i> 安全退出</a></li>
            </ul>
        </nav>

        <main class="col-md-10 main-content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <?= $page_content ?>
        </main>
    </div>
</div>
<script src="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // 全选功能
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
</body>
</html>
