<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">接码业务管理</h1>
</div>

<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body">
        <form action="admin.php?action=verification_code_bulk_save" method="POST">
            <h6 class="fw-bold text-success mb-3"><i class="fas fa-plus-circle"></i> 批量分配接码任务</h6>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">选择分类 <span class="text-danger">*</span></label>
                    <select class="form-select" name="category_name" required>
                        <option value="">-- 选择业务分类 --</option>
                        <?php if(!empty($classifications)) foreach ($classifications as $c): ?>
                            <option value="<?= htmlspecialchars($c['category_name']) ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">有效期 (天数)</label>
                    <input type="number" step="0.1" class="form-control" name="days_to_expire" value="30" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">用户备注 (User)</label>
                    <input type="text" class="form-control" name="user_content" placeholder="选填，如：张三">
                </div>
            </div>
            <div class="mb-2 mt-2">
                <label class="form-label">录入手机号与自定义码 (一行一个)</label>
                <textarea class="form-control" name="bulk_data" rows="4" required placeholder="格式：手机号---自定义查询码 (如果不填查询码，系统会自动生成)
例如：
13800138000---VIP888
13900139000"></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> 开始批量分配</button>
        </form>
    </div>
</div>

<form action="admin.php?action=verification_code_bulk_delete" method="POST" id="bulkForm">
    <div class="mb-2">
        <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('确定要删除选中的记录吗？');">
            <i class="fas fa-trash-alt"></i> 删除选中的任务
        </button>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 0.9rem;">
                    <thead class="table-light">
                        <tr>
                            <th width="40" class="text-center">
                                <input class="form-check-input" type="checkbox" id="selectAll" title="全选">
                            </th>
                            <th>查询码</th>
                            <th>分类</th>
                            <th>电话号</th>
                            <th>User</th>
                            <th>关键词</th>
                            <th>上架日期</th>
                            <th>到期时间</th>
                            <th>剩余时间</th>
                            <th>组合内容</th>
                            <th class="text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($verificationData)): ?>
                            <?php foreach ($verificationData as $row): 
                                $comboArr = json_decode($row['combination'], true) ?? [];
                                $comboStr = $comboArr[0] ?? '';
                                $phoneAssigned = explode('---', $comboStr)[0] ?? '未知号码';

                                $keywordsArr = json_decode($row['match_keywords'], true) ?? [];
                                $keywordsStr = implode(', ', $keywordsArr);

                                $releaseTime = strtotime($row['releasedate']);
                                $daysStr = str_replace('天', '', $row['expirationtime']);
                                $expireTime = $releaseTime + ((float)$daysStr * 86400);
                                $remainSeconds = $expireTime - time();

                                if ($remainSeconds > 0) {
                                    $r_days = floor($remainSeconds / 86400);
                                    $r_hours = floor(($remainSeconds % 86400) / 3600);
                                    $remainDisplay = "<span class='text-success fw-bold'>{$r_days}天{$r_hours}小时</span>";
                                } else {
                                    $remainDisplay = "<span class='text-danger fw-bold'>已过期</span>";
                                }
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input item-checkbox" type="checkbox" name="selected_items[]" value="<?= htmlspecialchars($row['code']) ?>">
                                    </td>
                                    <td class="fw-bold text-success fs-6">
                                        <?= htmlspecialchars($row['code']) ?>
                                    </td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['category']) ?></span></td>
                                    <td class="text-primary fw-bold"><?= htmlspecialchars($phoneAssigned) ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($row['user']) ?></small></td>
                                    <td><small class="text-secondary"><?= htmlspecialchars($keywordsStr) ?></small></td>
                                    <td><small><?= htmlspecialchars($row['releasedate']) ?></small></td>
                                    <td><span class="text-danger"><small><?= htmlspecialchars($row['expirationtime']) ?></small></span></td>
                                    <td><?= $remainDisplay ?></td>
                                    <td><small class="text-muted" style="max-width: 150px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($comboStr) ?>">
                                        <?= htmlspecialchars($comboStr) ?>
                                    </small></td>
                                    <td class="text-center">
                                        <a href="admin.php?action=verification_code_delete&code=<?= urlencode($row['code']) ?>" class="btn btn-sm btn-outline-danger btn-delete" title="删除" onclick="return confirm('确定删除吗？');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="11" class="text-center py-5 text-muted">暂无接码业务分配记录。</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
