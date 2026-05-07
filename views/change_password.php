<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="fas fa-user-shield"></i> 管理员账号设置</h1>
</div>

<div class="row">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 bg-light">
                <form action="admin.php?action=change_password_save" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">新的后台账号</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="new_username" required placeholder="请输入新账号">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">新的后台密码</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="text" class="form-control" name="new_password" required placeholder="请输入新密码">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold" onclick="return confirm('确认要修改后台登录账号和密码吗？修改后下次登录将生效。')">
                        <i class="fas fa-save"></i> 保存新账号密码
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
