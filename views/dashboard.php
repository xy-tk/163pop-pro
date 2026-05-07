<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2">系统控制台</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 bg-primary text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-uppercase text-white-50">号码库总数</h6>
                <h2 class="display-5 fw-bold mb-0"><?= $dash_phones ?? 0 ?></h2>
                <div class="mt-3"><a href="admin.php?action=phonenumber" class="text-white text-decoration-none">管理号码 <i class="fas fa-arrow-right"></i></a></div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 bg-success text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-uppercase text-white-50">运行中业务码</h6>
                <h2 class="display-5 fw-bold mb-0"><?= $dash_active_codes ?? 0 ?></h2>
                <div class="mt-3"><a href="admin.php?action=verification_code" class="text-white text-decoration-none">管理业务 <i class="fas fa-arrow-right"></i></a></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 bg-secondary text-white shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-uppercase text-white-50">已过期业务码</h6>
                <h2 class="display-5 fw-bold mb-0"><?= $dash_expired_codes ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>
