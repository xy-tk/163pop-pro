<?php
// 1. 先处理 PHP 逻辑
$classifications = getClassificationData();
$phoneNumbers = getPhoneNumberData();
// 处理消息提示...
if (isset($_SESSION['error_message'])) {
    $error_msg = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    $success_msg = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
// 处理批量添加的回显数据...
$retained_bulk_data = isset($_SESSION['bulk_data_to_retain']) ? htmlspecialchars($_SESSION['bulk_data_to_retain']) : '';
unset($_SESSION['bulk_data_to_retain']);

$retained_bulk_category = isset($_SESSION['bulk_category_to_retain']) ? $_SESSION['bulk_category_to_retain'] : '';
unset($_SESSION['bulk_category_to_retain']);

$retained_bulk_days = isset($_SESSION['bulk_days_to_expire_to_retain']) ? htmlspecialchars($_SESSION['bulk_days_to_expire_to_retain']) : '1';
unset($_SESSION['bulk_days_to_expire_to_retain']);

$retained_bulk_user_content = isset($_SESSION['bulk_user_content_to_retain']) ? htmlspecialchars($_SESSION['bulk_user_content_to_retain']) : '';
unset($_SESSION['bulk_user_content_to_retain']);

// 处理成功弹窗数据
$success_details = null;
if (isset($_SESSION['bulk_add_success_details'])) {
    $success_details = $_SESSION['bulk_add_success_details'];
    unset($_SESSION['bulk_add_success_details']);
}
?>

<style>
    /* 核心布局调整：水平排列 */
    .dhgl, .dhg2 { 
        display: flex; 
        flex-wrap: wrap; 
        align-items: flex-end; 
    }

    /* 表单项 */
    .form-item { 
        display: flex; 
        flex-direction: column; 
        /* 使用 margin 替代 gap 解决兼容性问题 */
        margin: 0 10px 10px 0;
    }

    label { 
        font-weight: bold; 
        min-width: auto; 
        margin-bottom: 5px;
        font-size: 12px; 
        color: #333; /* 明确颜色 */
    }
    
    .form-item input[type="text"], 
    .form-item select {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        min-width: 120px; 
        height: 32px;
        box-sizing: border-box;
        font-size: 13px;
    }

    /* 主提交按钮 */
    #submitBtn, #bulkSubmitBtn {
        padding: 0 15px;
        background-color: #409EFF;
        color: white;
        border: 1px solid #409EFF;
        border-radius: 4px;
        cursor: pointer;
        height: 32px; 
        line-height: 30px;
        margin-bottom: 0; 
        font-size: 13px;
    }
    #submitBtn:hover, #bulkSubmitBtn:hover {
        background-color: #66b1ff;
        border-color: #66b1ff;
    }

    /* 工具栏样式 */
    .toolbar { 
        margin-bottom: 15px; 
        display: flex; 
        flex-wrap: wrap; 
        align-items: center; 
    }
    .toolbar > * {
        margin-right: 8px;
        margin-bottom: 8px;
    }
    
    /* 通用按钮样式 */
    .toolbar button, .toolbar a, .search-form button, .search-form a {
        display: inline-block;
        padding: 5px 12px;
        font-size: 13px;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        line-height: 1.5;
        color: #fff;
    }

    .btn-primary, .sync-button, .bulk-button { background-color: #409EFF; border-color: #409EFF; color: #fff !important; }
    .btn-primary:hover, .sync-button:hover, .bulk-button:hover { background-color: #66b1ff; border-color: #66b1ff; }

    .btn-danger { background-color: #F56C6C; border-color: #F56C6C; color: #fff !important; }
    .btn-danger:hover { background-color: #f78989; border-color: #f78989; }

    .btn-warning { background-color: #E6A23C; border-color: #E6A23C; color: #fff !important; }
    .btn-warning:hover { background-color: #ebb563; border-color: #ebb563; }

    .btn-success { background-color: #67C23A; border-color: #67C23A; color: #fff !important; }
    .btn-success:hover { background-color: #85ce61; border-color: #85ce61; }

    .btn-info { background-color: #909399; border-color: #909399; color: #fff !important; }
    .btn-info:hover { background-color: #a6a9ad; border-color: #a6a9ad; }
    
    .btn-default { background-color: #fff; border-color: #dcdfe6; color: #606266 !important; }
    .btn-default:hover { background-color: #ecf5ff; color: #409EFF !important; border-color: #c6e2ff; }

    .search-form input[type="text"] {
        padding: 5px 10px;
        border: 1px solid #dcdfe6;
        border-radius: 4px;
        height: 32px;
        box-sizing: border-box;
        font-size: 13px;
    }

    /* 标题统一样式，防止 FOUC */
    h2 { 
        color: #333; 
        margin-top: 20px; 
        margin-bottom: 15px; 
        font-size: 18px; 
        font-weight: bold;
        line-height: 1.2;
    }
    
    /* 表格样式 */
    .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; background: #fff; }
    .data-table th, .data-table td { border: 1px solid #ebeef5; padding: 10px; text-align: left; word-break: break-all; color: #606266; }
    .data-table th { background-color: #f5f7fa; font-weight: bold; color: #909399; }
    .data-table tbody tr:hover { background-color: #f5f7fa; }
    .data-table th[data-sort] { cursor: pointer; }
    .data-table th .sort-icon { display: inline-block; width: 0; height: 0; margin-left: 5px; border-left: 5px solid transparent; border-right: 5px solid transparent; opacity: 0.5; }
    .data-table th[data-sort-dir="asc"] .sort-icon { border-bottom: 5px solid #000; opacity: 1; }
    .data-table th[data-sort-dir="desc"] .sort-icon { border-top: 5px solid #000; opacity: 1; }

    /* 操作按钮 */
    .action-cell { white-space: nowrap; }
    .edit-btn, .delete-btn {
        display: inline-block;
        padding: 2px 8px;
        font-size: 12px;
        border-radius: 3px;
        text-decoration: none;
        margin-right: 5px;
        color: #fff !important;
    }
    .edit-btn { background-color: #409EFF; border: none; cursor: pointer; }
    .edit-btn:hover { background-color: #66b1ff; }
    .delete-btn { background-color: #F56C6C; }
    .delete-btn:hover { background-color: #f78989; }

    .expired-row { background-color: #fafafa; opacity: 0.7; }
    .expired-badge { color: #F56C6C; font-size: 0.8em; margin-left: 5px; border: 1px solid #F56C6C; padding: 0 2px; border-radius: 2px; }
    
    .data-table-copy td:not(.no-copy) { cursor: pointer; }
    .copy-feedback { background-color: #f0f9eb !important; transition: background-color 0.1s ease-in-out; }
    
    /* 分页 */
    .pagination-controls { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 10px; background-color: #fff; border: 1px solid #ebeef5; border-radius: 4px; }
    .pagination-controls button, .pagination-controls select { padding: 5px 10px; margin: 0 2px; border: 1px solid #dcdfe6; border-radius: 3px; background-color: #fff; cursor: pointer; color: #606266; }
    .pagination-controls button:disabled { cursor: not-allowed; opacity: 0.5; color: #c0c4cc; }
    .pagination-controls .page-numbers button.active { background-color: #409EFF; color: white; border-color: #409EFF; }
    .pagination-controls .page-numbers .ellipsis { border: none; background: none; padding: 8px 0; }
    .pagination-controls .page-info { font-size: 14px; color: #555; }
    
    /* Modal */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1001; }
    .modal-content { background-color: #fff; padding: 20px; border-radius: 8px; width: 60%; max-width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; }
    .modal-close-btn { position: absolute; top: 10px; right: 15px; font-size: 24px; font-weight: bold; cursor: pointer; }
    .modal-body { max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin-top: 10px; white-space: pre-wrap; word-break: break-all; }
    .modal-footer { margin-top: 20px; text-align: right; }
</style>

<?php if (isset($error_msg)) echo '<p style="color: red; font-weight: bold;">' . $error_msg . '</p>'; ?>
<?php if (isset($success_msg)) echo '<p style="color: green; font-weight: bold;">' . $success_msg . '</p>'; ?>

<div id="success-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        <h3 id="modal-title" style="margin-top:0;">批量添加成功</h3>
        <div id="modal-body" class="modal-body"></div>
        <div class="modal-footer">
            <button id="modal-copy-btn" class="btn-primary">一键复制所有</button>
            <button id="modal-save-btn" class="btn-success">保存为TXT文件</button>
        </div>
    </div>
</div>

<h2>添加/编辑接码数据</h2>
<form id="verificationForm" method="post" action="admin.php?action=verification_code_save">
    <input type="hidden" name="original_code" id="originalCode">
    <input type="hidden" name="original_category" id="originalCategory">
    <div class="dhgl">
        <div class="form-item">
            <label for="category_name">选择分类:</label>
            <select id="category_name" name="category_name" required>
                <option value="">请选择一个分类</option>
                <?php foreach ($classifications as $classification): ?>
                    <option value="<?php echo htmlspecialchars($classification['id'] ?? ''); ?>"><?php echo htmlspecialchars($classification['category_name'] ?? ''); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-item">
            <label for="phonenumber">电话号:</label>
            <input type="text" id="phonenumber" name="phonenumber" required>
        </div>
        <div class="form-item">
            <label for="verification_code">查询码 (可选):</label>
            <input type="text" id="verification_code" name="verification_code" placeholder="留空则自动生成10位查询码">
        </div>
        <div class="form-item">
            <label for="user_content">输入填写的内容:</label>
            <input type="text" id="user_content" name="user_content">
        </div>
        <div class="form-item">
            <label for="days_to_expire">到期天数:</label>
            <input type="text" id="days_to_expire" name="days_to_expire" value="1" required>
        </div>
        <div class="form-item">
            <button type="submit" id="submitBtn">生成并保存</button>
        </div>
    </div>
</form>

<h2>批量添加接码数据</h2>
<form method="post" action="admin.php?action=verification_code_bulk_save">
    <div class="dhg2">
        <div class="form-item">
            <label for="bulk_category_name">选择分类:</label>
            <select id="bulk_category_name" name="bulk_category_name" required>
                <option value="">请选择一个分类</option>
                <?php foreach ($classifications as $classification): ?>
                    <?php $cat_id = htmlspecialchars($classification['id'] ?? ''); ?>
                    <option value="<?php echo $cat_id; ?>" <?php if ($cat_id === $retained_bulk_category) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($classification['category_name'] ?? ''); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-item">
            <label for="bulk_days_to_expire">到期天数(expirationtime):</label>
            <input type="text" id="bulk_days_to_expire" name="bulk_days_to_expire" value="<?php echo $retained_bulk_days; ?>" required>
        </div>
        <div class="form-item">
            <label for="bulk_user_content">批量输入填写的内容:</label>
            <input type="text" id="bulk_user_content" name="bulk_user_content" value="<?php echo $retained_bulk_user_content; ?>">
        </div>
    </div>
    <div style="margin-top: 10px;">
        <p style="font-size:13px; color:#666;">请按格式粘贴数据，每行一条：<code>电话号 [查询码]</code> (查询码可选，留空会自动生成。分隔符可用---、空格或Tab)</p>
        <textarea name="bulk_data" rows="10" cols="80" placeholder="示例 (查询码可选):&#x0a;1234567890 XYZ123&#x0a;9876543210" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;"><?php echo $retained_bulk_data; ?></textarea>
    </div>
    <button type="submit" id="bulkSubmitBtn" style="margin-top:10px;">批量保存</button>
</form>

<?php if (!empty($verificationData) || isset($_GET['search_term'])): ?>
    <h2>已添加的接码数据 <?php if (isset($_GET['search_term']) && !empty($_GET['search_term'])) echo "(搜索 ‘".htmlspecialchars($_GET['search_term'])."’ 的结果)"; ?></h2>
    
    <div class="toolbar">
        <button type="submit" form="bulkActionsForm" formaction="admin.php?action=verification_code_bulk_delete" onclick="return confirm('确定要删除所选数据吗？')" class="btn-danger">批量删除</button>
        <button type="submit" form="bulkActionsForm" formaction="admin.php?action=verification_code_bulk_update" onclick="return confirm('确定要更新所选数据吗？')" class="btn-primary">批量更新</button>
        <button type="submit" form="bulkActionsForm" formaction="admin.php?action=verification_code_bulk_move" onclick="return confirm('确定要移动所选数据到过期列表吗？')" class="btn-warning">批量移动</button>
        <button type="submit" form="bulkActionsForm" formaction="admin.php?action=export_selected_combinations" class="btn-info">批量导出</button>
        <a href="admin.php?action=export_all_combinations" class="btn-info">导出所有</a>
        <a href="admin.php?action=verification_code_move_expired" class="btn-warning" onclick="return confirm('确定要将所有过期数据移动到过期列表吗？')">移动所有过期</a>
        <a href="admin.php?action=verification_code_sync" class="btn-success" onclick="return confirm('确定要更新所有已添加的数据吗？')">更新所有数据</a>
        
        <form method="GET" action="admin.php" class="search-form">
            <input type="hidden" name="action" value="verification_code">
            <input type="text" id="searchInput" name="search_term" placeholder="搜索..." value="<?php echo htmlspecialchars($_GET['search_term'] ?? ''); ?>">
            <button type="submit" class="btn-primary">搜索</button>
            <?php if (isset($_GET['search_term']) && !empty($_GET['search_term'])): ?>
                <a href="admin.php?action=verification_code" class="btn-default">清空</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="toolbar" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">
        <form method="POST" action="admin.php?action=verification_code_update_all_content" onsubmit="return confirm('这是一个全局操作，将更新所有接码数据的“输入填写的内容”，确定要继续吗？');" style="display:flex; align-items:center;">
            <label for="update_all_user_content" style="min-width: auto; margin-bottom: 0; margin-right:10px;">更新所有“输入填写的内容”为:</label>
            <input type="text" name="update_all_user_content" required style="padding:5px; border:1px solid #ccc; border-radius:4px; margin-right:10px; height:32px;">
            <button type="submit" class="btn-primary">更新全部内容</button>
        </form>
    </div>

    <?php if (empty($verificationData) && isset($_GET['search_term'])): ?>
        <p>根据您的搜索 "<?php echo htmlspecialchars($_GET['search_term']); ?>", 没有找到任何匹配的数据。</p>
    <?php elseif (!empty($verificationData)): ?>
        <form id="bulkActionsForm" method="post">
            <table id="verificationTable" class="data-table data-table-copy">
                <thead>
                    <tr>
                        <th class="no-copy"><input type="checkbox" id="selectAll"> 全选</th>
                        <th data-sort="text" data-sort-dir="asc">查询码 <span class="sort-icon"></span></th>
                        <th data-sort="text">分类 <span class="sort-icon"></span></th>
                        <th data-sort="text">电话号 <span class="sort-icon"></span></th>
                        <th>User</th>
                        <th>关键词</th>
                        <th data-sort="date">上架日期 <span class="sort-icon"></span></th>
                        <th data-sort="text">到期时间 <span class="sort-icon"></span></th>
                        <th data-sort="numeric">剩余时间 <span class="sort-icon"></span></th>
                        <th>组合内容</th>
                        <th class="no-copy">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($verificationData as $code => $data): ?>
                        <?php
                            $is_expired = isset($data['is_expired']) && $data['is_expired'];
                            $releaseDate = $data['releasedate'][0] ?? null;
                            $expirationString = $data['expirationtime'][0] ?? '0天';
                            $daysToAdd = 0;
                            if (preg_match('/^(\d+(\.\d+)?)天$/', $expirationString, $matches)) {
                                $daysToAdd = (float)$matches[1];
                            }
                            $expirationTimestamp = 0;
                            if ($releaseDate) {
                                $releaseDateTime = new DateTime($releaseDate);
                                $releaseDateTime->modify("+" . ($daysToAdd * 24 * 3600) . " seconds");
                                $expirationTimestamp = $releaseDateTime->getTimestamp();
                            }
                        ?>
                        <tr class="<?php echo $is_expired ? 'expired-row' : ''; ?>">
                            <td class="no-copy"><input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($code); ?>"></td>
                            <td class="code-cell"><?php echo htmlspecialchars($code); ?></td>
                            <td class="category-cell">
                                <?php echo htmlspecialchars($data['category'] ?? ''); ?>
                                <?php if ($is_expired) echo '<span class="expired-badge">(已过期)</span>'; ?>
                            </td>
                            <td class="phonenumber-cell"><?php echo htmlspecialchars(explode('---', $data['combination'][0] ?? '')[0]); ?></td>
                            <td><?php echo htmlspecialchars($data['user'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(implode(', ', $data['match_keywords'] ?? [])); ?></td>
                            <td class="releasedate-cell" data-sort-value="<?php echo htmlspecialchars(implode(', ', $data['releasedate'] ?? [])); ?>"><?php echo htmlspecialchars(implode(', ', $data['releasedate'] ?? [])); ?></td>
                            <td class="expirationtime-cell" data-sort-value="<?php echo $daysToAdd; ?>"><?php echo htmlspecialchars(implode(', ', $data['expirationtime'] ?? [])); ?></td>
                            <td class="time-left-cell" data-expiration="<?php echo $expirationTimestamp; ?>"></td>
                            <td class="combination-cell"><?php echo htmlspecialchars(implode(', ', $data['combination'] ?? [])); ?></td>
                            <td class="no-copy action-cell">
                                <button type="button" class="edit-btn" data-code="<?php echo htmlspecialchars($code); ?>" data-category="<?php echo htmlspecialchars($data['category'] ?? ''); ?>">编辑</button>
                                <a href="admin.php?action=verification_code_delete&code=<?php echo urlencode($code); ?>&category=<?php echo urlencode($data['category']); ?>" onclick="return confirm('确定要删除这条数据吗？');" class="delete-btn">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="pagination-controls" class="pagination-controls"></div>
        </form>
    <?php endif; ?>
<?php else: ?>
    <p>当前没有已添加的接码数据。</p>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Success Modal Logic ---
        const successModal = document.getElementById('success-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        const closeModalBtn = document.querySelector('.modal-close-btn');
        const copyAllBtn = document.getElementById('modal-copy-btn');
        const saveTxtBtn = document.getElementById('modal-save-btn');

        <?php if (isset($success_details) && !empty($success_details)): ?>
            const successData = <?php echo json_encode($success_details); ?>;
            modalTitle.textContent = `批量添加成功 ${successData.length} 个`;
            modalBody.textContent = successData.join('\n');
            successModal.style.display = 'flex';
        <?php endif; ?>

        if(closeModalBtn) {
            closeModalBtn.addEventListener('click', () => {
                successModal.style.display = 'none';
            });
        }

        if(copyAllBtn) {
            copyAllBtn.addEventListener('click', () => {
                const textToCopy = modalBody.textContent;
                copyTextToClipboard(textToCopy, copyAllBtn);
            });
        }
        
        if(saveTxtBtn) {
            saveTxtBtn.addEventListener('click', () => {
                const textToSave = modalBody.textContent;
                const blob = new Blob([textToSave], { type: 'text/plain' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'successful_combinations.txt';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        }
        
        // --- Pagination Logic ---
        const table = document.getElementById('verificationTable');
        const tableBody = table ? table.querySelector('tbody') : null;
        const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')) : [];
        const controlsContainer = document.getElementById('pagination-controls');
        let currentPage = 1;
        let rowsPerPage = 30;

        function setupPagination() {
            if (!controlsContainer) return;
            controlsContainer.innerHTML = `
                <div class="page-size-selector">
                    <select id="rows-per-page">
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="500">500</option>
                        <option value="999999">全部</option>
                    </select>
                    <label for="rows-per-page">条/页</label>
                </div>
                <div class="page-info" id="page-info"></div>
                <div class="page-buttons">
                    <button id="prev-page" disabled>&laquo; 上一页</button>
                    <span class="page-numbers" id="page-numbers"></span>
                    <button id="next-page">下一页 &raquo;</button>
                </div>
            `;

            const rowsPerPageSelect = document.getElementById('rows-per-page');
            rowsPerPageSelect.addEventListener('change', (e) => {
                rowsPerPage = parseInt(e.target.value, 10);
                currentPage = 1;
                render();
            });

            document.getElementById('prev-page').addEventListener('click', () => {
                if (currentPage > 1) { currentPage--; render(); }
            });

            document.getElementById('next-page').addEventListener('click', () => {
                 const totalPages = Math.ceil(allRows.length / rowsPerPage);
                if (currentPage < totalPages) { currentPage++; render(); }
            });
            render();
        }

        function createPaginationButtons(totalPages) {
            const pageNumbersContainer = document.getElementById('page-numbers');
            pageNumbersContainer.innerHTML = '';
            const maxVisibleButtons = 8;
            
            if (totalPages <= maxVisibleButtons) {
                for (let i = 1; i <= totalPages; i++) {
                    pageNumbersContainer.appendChild(createPageButton(i));
                }
            } else {
                pageNumbersContainer.appendChild(createPageButton(1));
                if (currentPage > 4) {
                    pageNumbersContainer.appendChild(createEllipsis());
                }

                let start = Math.max(2, currentPage - 2);
                let end = Math.min(totalPages - 1, currentPage + 2);

                if (currentPage <= 4) { start = 2; end = 6; }
                if (currentPage >= totalPages - 3) { start = totalPages - 5; end = totalPages - 1; }

                for (let i = start; i <= end; i++) {
                    pageNumbersContainer.appendChild(createPageButton(i));
                }

                if (currentPage < totalPages - 3) {
                    pageNumbersContainer.appendChild(createEllipsis());
                }
                pageNumbersContainer.appendChild(createPageButton(totalPages));
            }
        }

        function createPageButton(pageNumber) {
            const button = document.createElement('button');
            button.textContent = pageNumber;
            button.className = (pageNumber === currentPage) ? 'active' : '';
            button.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent form submission
                currentPage = pageNumber;
                render();
            });
            return button;
        }

        function createEllipsis() {
            const ellipsis = document.createElement('button');
            ellipsis.className = 'ellipsis';
            ellipsis.textContent = '...';
            ellipsis.disabled = true;
            return ellipsis;
        }

        function render() {
            const totalRows = allRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            
            if (tableBody) {
                tableBody.innerHTML = '';
                const visibleRows = allRows.slice(start, end);
                visibleRows.forEach(row => tableBody.appendChild(row));
            }

            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');
            if (prevBtn) prevBtn.disabled = currentPage === 1;
            if (nextBtn) nextBtn.disabled = currentPage === totalPages || totalRows === 0;
            
            const pageInfo = document.getElementById('page-info');
            if (pageInfo) pageInfo.textContent = `共 ${totalRows} 条数据，第 ${currentPage} / ${totalPages > 0 ? totalPages : 1} 页`;
            
            createPaginationButtons(totalPages);
        }

        if (table && allRows.length > 0) {
            setupPagination();
        }
        
        function copyTextToClipboard(text, element) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => showCopySuccess(element), () => showCopyError());
            } else {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "absolute";
                textArea.style.left = "-9999px";
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    showCopySuccess(element);
                } catch (err) {
                    showCopyError();
                } finally {
                    document.body.removeChild(textArea);
                }
            }
        }

        function showCopySuccess(element) {
            if (element.tagName === 'TD') {
                element.classList.add('copy-feedback');
                setTimeout(() => element.classList.remove('copy-feedback'), 200);
            }
            const notification = document.createElement('div');
            notification.textContent = '已复制!';
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.background = '#28a745';
            notification.style.color = 'white';
            notification.style.padding = '10px 20px';
            notification.style.borderRadius = '5px';
            notification.style.zIndex = '1002';
            notification.style.transition = 'opacity 0.5s';
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 500);
            }, 1000);
        }
        
        function showCopyError() {
            alert("复制失败，请手动复制。");
        }

        document.querySelectorAll('.data-table-copy').forEach(table => {
            table.addEventListener('click', function(e) {
                const cell = e.target.closest('td');
                if (!cell || cell.classList.contains('no-copy')) { return; }
                const textToCopy = cell.textContent.trim();
                if (textToCopy) { copyTextToClipboard(textToCopy, cell); }
            });
        });

        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                document.querySelectorAll('input[name="selected_items[]"]').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
        
        const form = document.getElementById('verificationForm');
        const originalCodeInput = document.getElementById('originalCode');
        const originalCategoryInput = document.getElementById('originalCategory');
        const categoryNameSelect = document.getElementById('category_name');
        const phonenumberInput = document.getElementById('phonenumber');
        const verificationCodeInput = document.getElementById('verification_code');
        const userContentInput = document.getElementById('user_content');
        const daysToExpireInput = document.getElementById('days_to_expire');
        const submitBtn = document.getElementById('submitBtn');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const originalCode = row.querySelector('.code-cell').textContent;
                const originalCategory = row.querySelector('.category-cell').textContent.replace('(已过期)', '').trim();
                const originalPhoneNumber = row.querySelector('.phonenumber-cell').textContent;
                const originalUserContentAndCode = row.querySelector('.combination-cell').textContent.split('---')[1] || '';
                const lastSlashIndex = originalUserContentAndCode.lastIndexOf('/');
                const originalUserContent = lastSlashIndex !== -1 ? originalUserContentAndCode.substring(0, lastSlashIndex) : '';
                const originalDaysToExpire = row.querySelector('.expirationtime-cell').textContent.replace('天', '');
                
                originalCodeInput.value = originalCode;
                originalCategoryInput.value = originalCategory;
                form.action = 'admin.php?action=verification_code_edit';
                submitBtn.textContent = '保存修改';
                
                for(let i=0; i<categoryNameSelect.options.length; i++){
                    if(categoryNameSelect.options[i].text === originalCategory){
                        categoryNameSelect.value = categoryNameSelect.options[i].value;
                        break;
                    }
                }
                
                phonenumberInput.value = originalPhoneNumber;
                verificationCodeInput.value = originalCode;
                userContentInput.value = originalUserContent;
                daysToExpireInput.value = originalDaysToExpire;
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        function updateTimeLeft() {
            const now = new Date();
            document.querySelectorAll('.time-left-cell').forEach(cell => {
                const expirationTimestamp = parseInt(cell.dataset.expiration, 10) * 1000;
                if (!expirationTimestamp) return;

                const timeDifference = expirationTimestamp - now.getTime();
                if (timeDifference <= 0) {
                    cell.textContent = '已过期';
                } else {
                    const totalSeconds = Math.floor(timeDifference / 1000);
                    const days = Math.floor(totalSeconds / (3600 * 24));
                    const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = totalSeconds % 60;
                    cell.textContent = `${days}天${hours}:${minutes}:${seconds}`;
                    cell.dataset.sortValue = totalSeconds;
                }
            });
        }
        
        setInterval(updateTimeLeft, 1000);
        updateTimeLeft();

        document.querySelectorAll('th[data-sort]').forEach(header => {
            header.addEventListener('click', function() {
                const table = this.closest('table');
                const tbody = table.querySelector('tbody');
                const currentRows = Array.from(tbody.querySelectorAll('tr')); 
                
                const sortType = this.dataset.sort;
                let sortDir = this.dataset.sortDir === 'asc' ? 'desc' : 'asc';
                const cellIndex = this.cellIndex;

                document.querySelectorAll('th[data-sort]').forEach(h => {
                    if (h !== this) h.dataset.sortDir = '';
                });
                this.dataset.sortDir = sortDir;

                allRows.sort((a, b) => {
                    const aCell = a.cells[cellIndex];
                    const bCell = b.cells[cellIndex];
                    let aValue = aCell.dataset.sortValue || aCell.textContent.trim();
                    let bValue = bCell.dataset.sortValue || bCell.textContent.trim();

                    if (sortType === 'numeric') {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                    } else if (sortType === 'date') {
                        aValue = new Date(aValue).getTime() || 0;
                        bValue = new Date(bValue).getTime() || 0;
                    }

                    if (aValue < bValue) return sortDir === 'asc' ? -1 : 1;
                    if (aValue > bValue) return sortDir === 'asc' ? 1 : -1;
                    return 0;
                });
                
                currentPage = 1; 
                render(); 
            });
        });
    });
</script>