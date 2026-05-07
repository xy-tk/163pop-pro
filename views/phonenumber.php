<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2">号码库管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addPhoneModal">
            <i class="fas fa-plus"></i> 新增号码
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
                        <th>手机号码</th>
                        <th>邮箱账号 (User)</th>
                        <th>服务器 (Host:Port)</th>
                        <th>匹配发件人</th>
                        <th width="150" class="text-center">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($phoneNumbers)): ?>
                        <?php foreach ($phoneNumbers as $index => $phone): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $index + 1 ?></td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($phone['phonenumber']) ?></td>
                                <td><?= htmlspecialchars($phone['user']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($phone['host'] . ':' . $phone['port']) ?></span></td>
                                <td><?= htmlspecialchars($phone['match_sender'] ?: '无限制') ?></td>
                                <td class="text-center">
                                    <a href="admin.php?action=phonenumber_delete&id=<?= $phone['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" title="删除">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">暂无号码数据，请点击右上角添加。</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addPhoneModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-bottom-0">
        <h5 class="modal-title fw-bold">录入新号码配置</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="admin.php?action=phonenumber_save" method="POST">
          <div class="modal-body bg-light">
            <div class="mb-3">
                <label class="form-label">手机号码</label>
                <input type="text" class="form-control" name="phonenumber" required placeholder="如：13800138000">
            </div>
            <div class="row">
                <div class="col-8 mb-3">
                    <label class="form-label">IMAP/POP3 地址</label>
                    <input type="text" class="form-control" name="host" required placeholder="如：pop.163.com">
                </div>
                <div class="col-4 mb-3">
                    <label class="form-label">端口</label>
                    <input type="text" class="form-control" name="port" value="995" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">邮箱账号</label>
                <input type="email" class="form-control" name="user" required>
            </div>
            <div class="mb-3">
                <label class="form-label">邮箱授权码</label>
                <input type="text" class="form-control" name="pass" required>
            </div>
            <div class="mb-2">
                <label class="form-label">限定发件人 (选填)</label>
                <input type="text" class="form-control" name="match_sender" placeholder="如：notice@taobao.com">
                <div class="form-text text-muted">如果填写，则仅抓取该发件人的邮件</div>
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary px-4">保存</button>
          </div>
      </form>
    </div>
  </div>
</div>
