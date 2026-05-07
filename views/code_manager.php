<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2"><i class="fas fa-barcode"></i> 防重码库管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCodeManagerModal">
            <i class="fas fa-plus"></i> 手动占用新码
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                已生成的唯一查询码列表
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center">#</th>
                                <th>查询码 (Code)</th>
                                <th width="150" class="text-center">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usedCodesData)): ?>
                                <?php foreach ($usedCodesData as $index => $row): ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $index + 1 ?></td>
                                        <td class="fw-bold font-monospace text-primary"><?= htmlspecialchars($row['code']) ?></td>
                                        <td class="text-center">
                                            <a href="admin.php?action=used_codes_delete&code=<?= urlencode($row['code']) ?>" class="btn btn-sm btn-outline-danger btn-delete" title="释放此码">
                                                <i class="fas fa-trash-alt"></i> 释放
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">暂无任何已被占用的查询码。</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCodeManagerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header border-bottom-0">
        <h6 class="modal-title fw-bold">手动录入占用码</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="admin.php?action=used_codes_save" method="POST">
          <div class="modal-body bg-light">
            <div class="mb-2">
                <label class="form-label">防重查询码</label>
                <input type="text" class="form-control" name="verification_code" required placeholder="如：VIP666">
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">保存入库</button>
          </div>
      </form>
    </div>
  </div>
</div>
