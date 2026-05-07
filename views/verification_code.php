<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2">接码业务管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addCodeModal">
            <i class="fas fa-plus"></i> 分配新接码任务
        </button>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th>查询码 (用户凭证)</th>
                        <th>所属分类</th>
                        <th>绑定手机号</th>
                        <th>过期时间</th>
                        <th>状态</th>
                        <th class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($verificationData)): ?>
                        <?php foreach ($verificationData as $row): 
                            // 解析 combination 取出绑定的手机号 (格式为: 手机号---内容/查询码)
                            $comboArr = json_decode($row['combination'], true);
                            $comboStr = $comboArr[0] ?? '';
                            $phoneAssigned = explode('---', $comboStr)[0] ?? '未知号码';
                        ?>
                            <tr>
                                <td class="fw-bold text-success fs-6">
                                    <?= htmlspecialchars($row['code']) ?>
                                    <i class="fas fa-copy ms-2 text-muted btn-copy" data-clipboard-text="<?= htmlspecialchars($row['code']) ?>" title="点击复制"></i>
                                </td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['category']) ?></span></td>
                                <td class="text-primary fw-bold"><?= htmlspecialchars($phoneAssigned) ?></td>
                                <td>
                                    <small class="text-muted d-block">发布: <?= htmlspecialchars($row['releasedate']) ?></small>
                                    <small class="text-danger">时效: <?= htmlspecialchars($row['expirationtime']) ?></small>
                                </td>
                                <td>
                                    <?php if ($row['is_expired']): ?>
                                        <span class="badge bg-secondary">已过期</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">运行中</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-copy" data-clipboard-text="<?= "http://".$_SERVER['HTTP_HOST']."/index.php?code=".urlencode($row['code']) ?>">
                                        <i class="fas fa-link"></i> 复制链接
                                    </button>
                                    <a href="admin.php?action=verification_code_delete&code=<?= urlencode($row['code']) ?>" class="btn btn-sm btn-outline-danger btn-delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">暂无接码业务分配记录。</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addCodeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title fw-bold text-success">分配新接码任务</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="admin.php?action=verification_code_save" method="POST">
          <div class="modal-body bg-light">
            <div class="mb-3">
                <label class="form-label fw-bold">自定义查询码 (留空则自动生成)</label>
                <input type="text" class="form-control" name="verification_code" placeholder="输入自定义凭证码，如：VIP888">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">选择绑定手机号 <span class="text-danger">*</span></label>
                <select class="form-select" name="phonenumber" required>
                    <option value="">-- 请选择系统现存号码 --</option>
                    <?php if(!empty($phoneNumbers)) foreach ($phoneNumbers as $p): ?>
                        <option value="<?= htmlspecialchars($p['phonenumber']) ?>"><?= htmlspecialchars($p['phonenumber']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">选择接码项目 (分类) <span class="text-danger">*</span></label>
                <select class="form-select" name="category_name" required>
                    <option value="">-- 请选择业务分类 --</option>
                    <?php if(!empty($classifications)) foreach ($classifications as $c): ?>
                        <option value="<?= htmlspecialchars($c['category_name']) ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">有效期 (天数)</label>
                    <input type="number" step="0.1" class="form-control" name="days_to_expire" value="30" required>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-bold">用户备注</label>
                    <input type="text" class="form-control" name="user_content" placeholder="选填，如：张三">
                </div>
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-success px-4">确认分配</button>
          </div>
      </form>
    </div>
  </div>
</div>
