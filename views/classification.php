<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">业务分类管理</h1>
</div>

<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body">
        <form action="admin.php?action=classification_save" method="POST">
            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-plus-circle"></i> 添加新分类</h6>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label class="form-label">分类名称</label>
                    <input type="text" class="form-control" name="category_name" required placeholder="如：淘宝全系">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">匹配关键词 (英文逗号分隔)</label>
                    <input type="text" class="form-control" name="match_keywords" required placeholder="如：验证码,校验码">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> 添加</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th width="200">分类名称</th>
                        <th>提取关键词规则</th>
                        <th width="100" class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($classifications)): ?>
                        <?php foreach ($classifications as $index => $class): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $index + 1 ?></td>
                                <td><span class="badge bg-info text-dark px-3 py-2 fs-6"><?= htmlspecialchars($class['category_name']) ?></span></td>
                                <td class="text-secondary"><?= htmlspecialchars($class['match_keywords']) ?></td>
                                <td class="text-center">
                                    <a href="admin.php?action=classification_delete&id=<?= $class['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" onclick="return confirm('确定删除吗？');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">暂无分类数据，请在上方添加。</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
