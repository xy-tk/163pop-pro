<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2">业务分类管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addClassificationModal">
            <i class="fas fa-plus"></i> 新增分类
        </button>
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
                        <th>提取关键词规则 (英文逗号分隔)</th>
                        <th width="150" class="text-center">操作</th>
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
                                    <a href="admin.php?action=classification_delete&id=<?= $class['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" title="删除">
                                        <i class="fas fa-trash-alt"></i> 删除
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">暂无分类数据，请点击右上角添加。</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addClassificationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title fw-bold">录入新分类规则</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="admin.php?action=classification_save" method="POST">
          <div class="modal-body bg-light">
            <div class="mb-3">
                <label class="form-label fw-bold">分类名称</label>
                <input type="text" class="form-control" name="category_name" required placeholder="如：淘宝全系、抖音等">
            </div>
            <div class="mb-2">
                <label class="form-label fw-bold">匹配关键词</label>
                <textarea class="form-control" name="match_keywords" rows="3" required placeholder="如：验证码,校验码,动态码"></textarea>
                <div class="form-text text-muted">多个关键词请用<strong class="text-danger">英文逗号 (,)</strong>分隔。只有邮件中包含这些词才会被抓取。</div>
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary px-4">保存分类</button>
          </div>
      </form>
    </div>
  </div>
</div>
