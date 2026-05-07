<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>验证码管理系统 Pro</title>
    <link href="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar d-none d-md-block">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 text-white min-vh-100">
                <a href="admin.php?action=dashboard" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none border-bottom border-secondary w-100">
                    <span class="fs-5 d-none d-sm-inline fw-bold"><i class="fas fa-envelope-open-text me-2"></i> 接码系统 Pro</span>
                </a>
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100 mt-3" id="menu">
                    <li class="nav-item w-100">
                        <a href="admin.php?action=dashboard" class="nav-link text-white <?= ($_GET['action'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">
                            <i class="fas fa-home"></i> <span class="d-none d-sm-inline">控制台</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a href="admin.php?action=phonenumber" class="nav-link text-white <?= ($_GET['action'] ?? '') === 'phonenumber' ? 'active' : '' ?>">
                            <i class="fas fa-phone-alt"></i> <span class="d-none d-sm-inline">号码库管理</span>
                        </a>
                    </li>
                    <li class="nav-item w-100">
                        <a href="admin.php?action=verification_code" class="nav-link text-white <?= ($_GET['action'] ?? '') === 'verification_code' ? 'active' : '' ?>">
                            <i class="fas fa-key"></i> <span class="d-none d-sm-inline">接码业务管理</span>
                        </a>
                    </li>
                    </ul>
                <hr>
                <div class="pb-4 w-100">
                    <a href="admin.php?action=logout" class="nav-link text-danger w-100">
                        <i class="fas fa-sign-out-alt"></i> <span class="d-none d-sm-inline">安全退出</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col d-flex flex-column h-sm-100">
            <nav class="navbar navbar-expand-lg navbar-light topbar d-md-none px-3">
                <a class="navbar-brand fw-bold" href="#">接码系统 Pro</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mobileMenu">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="admin.php?action=dashboard">控制台</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin.php?action=phonenumber">号码库</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin.php?action=verification_code">业务管理</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="admin.php?action=logout">退出</a></li>
                    </ul>
                </div>
            </nav>

            <main class="flex-grow-1 p-4 overflow-auto">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= $page_content ?? '' ?>
                
            </main>
        </div>
    </div>
</div>

<script src="https://cdn.staticfile.org/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
