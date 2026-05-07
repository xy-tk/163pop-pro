<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">查询码管理</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body">
                <form action="admin.php?action=used_codes_save" method="POST">
                    <label class="form-label fw-bold"><i class="fas fa-plus-circle"></i> 手动占用防重码</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="verification_code" required placeholder="如：VIP666">
                        <button type="submit" class="btn btn-primary">加入防重库</button>
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
                                <th width="60" class="text-center">#</th>
                                <th>查询码 (Code)</th>
                                <th width="100" class="text-center">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usedCodesData)): ?>
                                <?php foreach ($usedCodesData as $index => $row): ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $index + 1 ?></td>
                                        <td class="fw-bold font-monospace text-primary"><?= htmlspecialchars($row['code']) ?></td>
                                        <td class="text-center">
                                            <a href="admin.php?action=used_codes_delete&code=<?= urlencode($row['code']) ?>" class="btn btn-sm btn-outline-danger btn-delete" onclick="return confirm('释放此码？');">
                                                <i class="fas fa-trash-alt"></i>
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
