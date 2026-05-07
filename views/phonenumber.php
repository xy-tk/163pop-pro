<h2>电话管理</h2>
<?php
$data_file = 'phonenumber.json';
$phonenumbers = [];
if (file_exists($data_file)) {
    $json_content = file_get_contents($data_file);
    if ($json_content) {
        $phonenumbers = json_decode($json_content, true);
        if ($phonenumbers === null) {
            $phonenumbers = [];
        }
    }
}
?>

<h3>添加/编辑电话</h3>
<form id="phonenumberForm" method="post" action="admin.php?action=phonenumber_save">
    <div class="dhgl">
        <input type="hidden" name="original_id" id="originalId">
        <div class="form-item">
            <label for="host">Host:</label>
            <input type="text" id="host" name="host">
        </div>
        <div class="form-item">
            <label for="port">Port:</label>
            <input type="text" id="port" name="port" value="995">
        </div>
        <div class="form-item">
            <label for="user">邮箱 (user):</label>
            <input type="text" id="user" name="user">
        </div>
        <div class="form-item">
            <label for="pass">密码 (pass):</label>
            <input type="password" id="pass" name="pass">
        </div>
        <div class="form-item">
            <label for="match_sender">发件邮箱 (match_sender):</label>
            <input type="text" id="match_sender" name="match_sender">
        </div>
        <div class="form-item">
            <label for="phonenumber">电话号 (phonenumber):</label>
            <input type="text" id="phonenumber" name="phonenumber" required>
        </div>
        <div class="form-item">
            <button type="submit" id="submitBtn">添加电话</button>
        </div>
    </div>
</form>

<h3>批量添加电话</h3>
<div class="bulk-add-container">
    <form method="post" action="admin.php?action=phonenumber_bulk_save">
        <div class="dhgl">
            <div class="form-item" style="flex-grow: 1; max-width: 100%;">
                <label for="bulk_data">批量数据 (分隔符可用---, 空格, Tab):</label>
                <textarea id="bulk_data" name="bulk_data" rows="5" style="width: 100%; min-width: 600px;" placeholder="每行一条，格式：host port 邮箱 密码 发件邮箱 电话号"></textarea>
            </div>
            <div class="form-item">
                <button type="submit" id="submitBtn">批量添加</button>
            </div>
        </div>
    </form>
</div>

<?php if (!empty($phonenumbers)): ?>
    <h3>已添加的电话</h3>
    
    <div class="toolbar">
        <form id="bulkDeleteForm" method="post" action="admin.php?action=phonenumber_bulk_delete" onsubmit="return confirm('确定要删除选中的电话吗？');" style="display:inline;">
            <button type="submit" class="btn-danger">批量删除</button>
        </form>
        
        <div class="search-box" style="display: flex; align-items: center; gap: 5px;">
            <label for="search" style="margin-bottom: 0; margin-right: 5px;">搜索:</label>
            <input type="text" id="search" placeholder="输入邮箱、发件邮箱或电话号..." style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; min-width: 200px;">
        </div>
    </div>

    <form id="phonenumberTableForm" method="post">
        <table id="phoneTable" class="data-table data-table-copy">
            <thead>
                <tr>
                    <th class="no-copy"><input type="checkbox" id="selectAll"> 全选</th>
                    <th>Host</th>
                    <th>Port</th>
                    <th class="search-col">邮箱 (user)</th>
                    <th>密码 (pass)</th>
                    <th class="search-col">发件邮箱 (match_sender)</th>
                    <th class="search-col">电话号 (phonenumber)</th>
                    <th class="no-copy">操作</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php foreach ($phonenumbers as $number): ?>
                <?php
                $host = htmlspecialchars($number['host'] ?? '');
                $port = htmlspecialchars($number['port'] ?? '');
                $user = htmlspecialchars($number['user'] ?? '');
                $pass = htmlspecialchars($number['pass'] ?? '');
                $match_sender = htmlspecialchars($number['match_sender'] ?? '');
                $phonenumber = htmlspecialchars($number['phonenumber'] ?? '');
                $id = $phonenumber;
                ?>
                <tr>
                    <td class="no-copy"><input type="checkbox" name="selected_items[]" value="<?php echo $id; ?>"></td>
                    <td><?php echo $host; ?></td>
                    <td><?php echo $port; ?></td>
                    <td class="search-col"><?php echo $user; ?></td>
                    <td><?php echo $pass; ?></td>
                    <td class="search-col"><?php echo $match_sender; ?></td>
                    <td class="search-col"><?php echo $phonenumber; ?></td>
                    <td class="no-copy action-cell">
                        <button type="button" class="edit-btn" 
                           data-host="<?php echo $host; ?>" 
                           data-port="<?php echo $port; ?>"
                           data-user="<?php echo $user; ?>" 
                           data-pass="<?php echo $pass; ?>"
                           data-match_sender="<?php echo $match_sender; ?>"
                           data-phonenumber="<?php echo $phonenumber; ?>"
                           data-id="<?php echo $id; ?>">编辑</button>
                        <a href="admin.php?action=phonenumber_delete&id=<?php echo urlencode($id); ?>" onclick="return confirm('确定要删除这个电话吗？');" class="delete-btn">删除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
         <div id="pagination-controls" class="pagination-controls"></div>
    </form>
<?php else: ?>
    <p>当前没有已添加的电话信息。</p>
<?php endif; ?>

<style>
    /* 核心布局调整：水平排列 */
    .dhgl { 
        display: flex; 
        flex-wrap: wrap; 
        align-items: flex-end; 
    }

    /* 表单项 */
    .form-item { 
        display: flex; 
        flex-direction: column; 
        margin: 0 10px 10px 0;
    }

    label { 
        font-weight: bold; 
        min-width: auto; 
        margin-bottom: 5px;
        font-size: 12px; 
    }
    
    .form-item input[type="text"], 
    .form-item input[type="password"] {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        min-width: 120px; 
        height: 32px;
        box-sizing: border-box;
        font-size: 13px;
    }

    /* 提交按钮 */
    #submitBtn {
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
    #submitBtn:hover {
        background-color: #66b1ff;
        border-color: #66b1ff;
    }

    .toolbar { 
        margin-bottom: 15px; 
        display: flex; 
        flex-wrap: wrap; 
        align-items: center; 
    }
    .toolbar form { margin: 0 10px 0 0; }

    /* 按钮统一样式 */
    .btn-danger { background-color: #F56C6C; border-color: #F56C6C; color: #fff !important; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; }
    .btn-danger:hover { background-color: #f78989; border-color: #f78989; }

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

    .data-table-copy td:not(.no-copy) { cursor: pointer; }
    .copy-feedback { background-color: #f0f9eb !important; transition: background-color 0.1s ease-in-out; }

    /* 分页 */
    .pagination-controls { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 10px; background-color: #fff; border: 1px solid #ebeef5; border-radius: 4px; }
    .pagination-controls button, .pagination-controls select { padding: 5px 10px; margin: 0 2px; border: 1px solid #dcdfe6; border-radius: 3px; background-color: #fff; cursor: pointer; color: #606266; }
    .pagination-controls button:disabled { cursor: not-allowed; opacity: 0.5; color: #c0c4cc; }
    .pagination-controls .page-numbers button.active { background-color: #409EFF; color: white; border-color: #409EFF; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('phoneTable');
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

            document.getElementById('prev-page').addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) { currentPage--; render(); }
            });

            document.getElementById('next-page').addEventListener('click', (e) => {
                 e.preventDefault();
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
                e.preventDefault();
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
            // 先应用搜索过滤，然后再分页
            const searchTerm = document.getElementById('search') ? document.getElementById('search').value.toLowerCase() : '';
            let visibleRows = allRows;
            
            if (searchTerm) {
                visibleRows = allRows.filter(row => {
                    const userCell = row.cells[3] ? row.cells[3].textContent.toLowerCase() : '';
                    const senderCell = row.cells[5] ? row.cells[5].textContent.toLowerCase() : '';
                    const phoneCell = row.cells[6] ? row.cells[6].textContent.toLowerCase() : '';
                    return userCell.includes(searchTerm) || senderCell.includes(searchTerm) || phoneCell.includes(searchTerm);
                });
            }

            const totalRows = visibleRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            
            // 隐藏所有行
            allRows.forEach(row => row.style.display = 'none');

            // 显示当前页的行
            const currentRows = visibleRows.slice(start, end);
            currentRows.forEach(row => row.style.display = '');

            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');
            if(prevBtn) prevBtn.disabled = currentPage === 1;
            if(nextBtn) nextBtn.disabled = currentPage === totalPages || totalRows === 0;
            
            const pageInfo = document.getElementById('page-info');
            if(pageInfo) pageInfo.textContent = `共 ${totalRows} 条数据，第 ${currentPage} / ${totalPages > 0 ? totalPages : 1} 页`;
            
            createPaginationButtons(totalPages > 0 ? totalPages : 1);
        }

        if (table && allRows.length > 0) {
            setupPagination();
        }
        
        function copyTextToClipboard(text, cell) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => showCopySuccess(cell), () => showCopyError());
            } else {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "absolute";
                textArea.style.left = "-9999px";
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    showCopySuccess(cell);
                } catch (err) {
                    showCopyError();
                } finally {
                    document.body.removeChild(textArea);
                }
            }
        }

        function showCopySuccess(cell) {
            cell.classList.add('copy-feedback');
            setTimeout(() => cell.classList.remove('copy-feedback'), 200);
            const notification = document.createElement('div');
            notification.textContent = '已复制!';
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.background = '#28a745';
            notification.style.color = 'white';
            notification.style.padding = '10px 20px';
            notification.style.borderRadius = '5px';
            notification.style.zIndex = '1000';
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
                if (!cell || cell.classList.contains('no-copy')) return;
                const textToCopy = cell.textContent.trim();
                if (textToCopy) copyTextToClipboard(textToCopy, cell);
            });
        });

        const editButtons = document.querySelectorAll('.edit-btn');
        const form = document.getElementById('phonenumberForm');
        const originalIdInput = document.getElementById('originalId');
        const hostInput = document.getElementById('host');
        const portInput = document.getElementById('port');
        const userInput = document.getElementById('user');
        const passInput = document.getElementById('pass');
        const matchSenderInput = document.getElementById('match_sender');
        const phonenumberInput = document.getElementById('phonenumber');
        const submitBtn = document.getElementById('submitBtn');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const host = this.getAttribute('data-host');
                const port = this.getAttribute('data-port');
                const user = this.getAttribute('data-user');
                const pass = this.getAttribute('data-pass');
                const match_sender = this.getAttribute('data-match_sender');
                const phonenumber = this.getAttribute('data-phonenumber');

                originalIdInput.value = id;
                hostInput.value = host;
                portInput.value = port;
                userInput.value = user;
                passInput.value = pass;
                matchSenderInput.value = match_sender;
                phonenumberInput.value = phonenumber;
                
                form.action = 'admin.php?action=phonenumber_edit';
                submitBtn.textContent = '保存修改';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        if(bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function(e) {
                const selected = Array.from(document.querySelectorAll('#phonenumberTableForm input[name="selected_items[]"]:checked'));
                if (selected.length === 0) {
                    alert('请至少选择一个电话进行删除。');
                     e.preventDefault();
                    return false;
                }
            });
        }

        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
                checkboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    // Only select visible rows if you want to avoid deleting filtered rows
                    if(row.style.display !== 'none') {
                        checkbox.checked = this.checked;
                    }
                });
            });
        }
        
        const searchInput = document.getElementById('search');
        if(searchInput){
            searchInput.addEventListener('keyup', function() {
                currentPage = 1;
                render();
            });
        }
    });
</script>