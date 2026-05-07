<h2>已过期电话</h2>
<?php
$classifications = getClassificationData();
$phoneNumbers = getPhoneNumberData();
if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    echo '<p style="color: green; font-weight: bold;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
    unset($_SESSION['success_message']);
}
?>

<h3>编辑过期数据并恢复</h3>
<form id="verificationForm" method="post" action="admin.php?action=expired_phones_edit">
    <input type="hidden" name="original_code" id="originalCode">
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
            <label for="verification_code">查询码:</label>
            <input type="text" id="verification_code" name="verification_code" required>
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
            <button type="submit" id="submitBtn">保存并恢复</button>
        </div>
    </div>
</form>

<?php if (!empty($verificationData)): ?>
    <h3>已过期接码数据</h3>
    
    <div class="toolbar">
        <button type="submit" form="bulkActionsForm" formaction="admin.php?action=expired_phones_bulk_delete" onclick="return confirm('确定要删除所选数据吗？')" class="btn-danger">批量删除</button>
        <button type="submit" form="bulkActionsForm" formaction="admin.php?action=export_selected_combinations" class="btn-info">批量导出组合内容</button>
        
        <div class="search-box" style="display: flex; align-items: center; gap: 5px;">
            <label for="searchInput" style="margin-bottom: 0; margin-right: 5px;">搜索:</label>
            <input type="text" id="searchInput" placeholder="请输入查询码、电话号或关键词..." style="padding: 5px; border: 1px solid #ccc; border-radius: 4px; min-width: 200px;">
        </div>
    </div>

    <form id="bulkActionsForm" method="post">
        <table id="expiredTable" class="data-table data-table-copy">
            <thead>
                <tr>
                    <th class="no-copy"><input type="checkbox" id="selectAll"> 全选</th>
                    <th>查询码</th>
                    <th>分类</th>
                    <th>电话号</th>
                    <th>上架日期</th>
                    <th>到期时间</th>
                    <th>组合内容</th>
                    <th class="no-copy">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($verificationData as $code => $data): ?>
                    <tr>
                        <td class="no-copy"><input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($code); ?>"></td>
                        <td class="code-cell"><?php echo htmlspecialchars($code); ?></td>
                        <td class="category-cell"><?php echo htmlspecialchars($data['category'] ?? ''); ?></td>
                        <td class="phonenumber-cell"><?php echo htmlspecialchars(explode('---', $data['combination'][0] ?? '')[0]); ?></td>
                        <td><?php echo htmlspecialchars(implode(', ', $data['releasedate'] ?? [])); ?></td>
                        <td><?php echo htmlspecialchars(implode(', ', $data['expirationtime'] ?? [])); ?></td>
                        <td class="combination-cell"><?php echo htmlspecialchars(implode(', ', $data['combination'] ?? [])); ?></td>
                        <td class="no-copy action-cell">
                            <button type="button" class="edit-btn" data-code="<?php echo htmlspecialchars($code); ?>" data-category="<?php echo htmlspecialchars($data['category'] ?? ''); ?>">编辑</button>
                            <a href="admin.php?action=expired_phones_delete&code=<?php echo urlencode($code); ?>" onclick="return confirm('确定要删除这条数据吗？');" class="delete-btn">删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
         <div id="pagination-controls" class="pagination-controls"></div>
    </form>
<?php else: ?>
    <p>当前没有已过期接码数据。</p>
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
    .form-item select {
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
    /* 使用 margin 控制工具栏元素间距 */
    .toolbar > * {
        margin-right: 8px;
        margin-bottom: 8px;
    }

    /* 按钮统一样式 */
    .btn-danger { background-color: #F56C6C; border-color: #F56C6C; color: #fff !important; border: 1px solid transparent; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; }
    .btn-danger:hover { background-color: #f78989; border-color: #f78989; }

    .btn-info { background-color: #909399; border-color: #909399; color: #fff !important; border: 1px solid transparent; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; }
    .btn-info:hover { background-color: #a6a9ad; border-color: #a6a9ad; }

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
    .copy-feedback {
        background-color: #d4edda !important;
        transition: background-color 0.1s ease-in-out;
    }

    /* 分页 */
    .pagination-controls { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 10px; background-color: #fff; border: 1px solid #ebeef5; border-radius: 4px; }
    .pagination-controls button, .pagination-controls select { padding: 5px 10px; margin: 0 2px; border: 1px solid #dcdfe6; border-radius: 3px; background-color: #fff; cursor: pointer; color: #606266; }
    .pagination-controls button:disabled { cursor: not-allowed; opacity: 0.5; color: #c0c4cc; }
    .pagination-controls .page-numbers button.active { background-color: #409EFF; color: white; border-color: #409EFF; }
    .pagination-controls .page-numbers .ellipsis { border: none; background: none; padding: 8px 0; }
    .pagination-controls .page-info { font-size: 14px; color: #555; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('expiredTable');
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
                if (currentPage > 1) {
                    currentPage--;
                    render();
                }
            });

            document.getElementById('next-page').addEventListener('click', () => {
                 const totalPages = Math.ceil(allRows.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    render();
                }
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

                if (currentPage <= 4) {
                    start = 2;
                    end = 6;
                }
                if (currentPage >= totalPages - 3) {
                    start = totalPages - 5;
                    end = totalPages - 1;
                }

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
            button.addEventListener('click', () => {
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
            if(prevBtn) prevBtn.disabled = currentPage === 1;
            if(nextBtn) nextBtn.disabled = currentPage === totalPages || totalRows === 0;
            
            const pageInfo = document.getElementById('page-info');
            if(pageInfo) pageInfo.textContent = `共 ${totalRows} 条数据，第 ${currentPage} / ${totalPages > 0 ? totalPages : 1} 页`;
            
            createPaginationButtons(totalPages);
        }

        if (allRows.length > 0) {
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
                if (!cell || cell.classList.contains('no-copy')) { return; }
                const textToCopy = cell.textContent.trim();
                if (textToCopy) { copyTextToClipboard(textToCopy, cell); }
            });
        });

        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('input[name="selected_items[]"]');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
        
        const form = document.getElementById('verificationForm');
        const originalCodeInput = document.getElementById('originalCode');
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
                const originalCategory = row.querySelector('.category-cell').textContent;
                const originalPhoneNumber = row.querySelector('.phonenumber-cell').textContent;
                const originalUserContentAndCode = row.querySelector('.combination-cell').textContent.split('---')[1] || '';
                const lastSlashIndex = originalUserContentAndCode.lastIndexOf('/');
                const originalUserContent = lastSlashIndex !== -1 ? originalUserContentAndCode.substring(0, lastSlashIndex) : '';
                const originalDaysToExpire = row.querySelector('td:nth-child(6)').textContent.replace('天', '');
                
                originalCodeInput.value = originalCode;
                form.action = 'admin.php?action=expired_phones_edit';
                submitBtn.textContent = '保存并恢复';
                
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
        
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const trs = tableBody.getElementsByTagName('tr');

                for (let i = 0; i < trs.length; i++) {
                    const textContent = trs[i].textContent || trs[i].innerText;
                    if (textContent.toLowerCase().indexOf(filter) > -1) {
                        trs[i].style.display = '';
                    } else {
                        trs[i].style.display = 'none';
                    }
                }
            });
        }
    });
</script>