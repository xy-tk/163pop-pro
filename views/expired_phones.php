<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 text-secondary"><i class="fas fa-history"></i> 过期业务记录</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="admin.php?action=expired_phones_clear_all" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('确定要清空所有过期记录吗？此操作不可逆！')">
            <i class="fas fa-trash-alt"></i> 清空所有过期记录
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th>查询码</th>
                        <th>所属分类</th>
                        <th>绑定手机号</th>
                        <th>时效记录</th>
                        <th>状态</th>
                        <th class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($expiredData)): ?>
                        <?php foreach ($expiredData as $row): 
                            $comboArr = json_decode($row['combination'], true);
                            $phoneAssigned = explode('---', $comboArr[0] ?? '')[0] ?? '未知号码';
                        ?>
                            <tr>
                                <td class="text-secondary text-decoration-line-through"><?= htmlspecialchars($row['code']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['category']) ?></span></td>
                                <td class="text-muted"><?= htmlspecialchars($phoneAssigned) ?></td>
                                <td>
                                    <small class="text-muted d-block">发布: <?= htmlspecialchars($row['releasedate']) ?></small>
                                    <small class="text-muted">原定: <?= htmlspecialchars($row['expirationtime']) ?></small>
                                </td>
                                <td><span class="badge bg-danger">已过期</span></td>
                                <td class="text-center">
                                    <a href="admin.php?action=expired_phones_delete&code=<?= urlencode($row['code']) ?>" class="btn btn-sm btn-outline-danger btn-delete" title="彻底删除">
                                        <i class="fas fa-times"></i> 删除
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">目前没有任何过期的业务记录。</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
