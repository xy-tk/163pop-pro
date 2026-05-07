<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">号码库管理</h1>
</div>

<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body">
        <form action="admin.php?action=phonenumber_bulk_save" method="POST">
            <div class="mb-2">
                <label class="form-label fw-bold text-primary"><i class="fas fa-plus-circle"></i> 批量导入号码数据</label>
                <textarea class="form-control" name="bulk_data" rows="4" required placeholder="格式：服务器---端口---账号---密码---匹配发件人---手机号
例如：pop.163.com---995---abc@163.com---password123------13800138000
(注意：即使没有发件人限制，也要保留分隔符或者用空格隔开)"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 批量保存入库</button>
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
                        <th>服务器 (Host)</th>
                        <th>端口 (Port)</th>
                        <th>邮箱账号 (User)</th>
                        <th>密码 (Pass)</th>
                        <th>匹配发件人</th>
                        <th>手机号码</th>
                        <th width="100" class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($phoneNumbers)): ?>
                        <?php foreach ($phoneNumbers as $index => $phone): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($phone['host']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($phone['port']) ?></span></td>
                                <td><?= htmlspecialchars($phone['user']) ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($phone['pass']) ?></small></td>
                                <td><?= htmlspecialchars($phone['match_sender'] ?: '无限制') ?></td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($phone['phonenumber']) ?></td>
                                <td class="text-center">
                                    <a href="admin.php?action=phonenumber_delete&id=<?= $phone['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" onclick="return confirm('确定删除吗？');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">暂无号码数据，请在上方批量添加。</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
