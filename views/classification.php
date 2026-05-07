<h2>分类管理</h2>
<?php
$data_file = 'classification.json';
$classifications = [];
if (file_exists($data_file)) {
    $json_content = file_get_contents($data_file);
    if ($json_content) {
        $classifications = json_decode($json_content, true);
        if ($classifications === null) {
            $classifications = [];
        }
    }
}
?>

<h3>添加/编辑分类</h3>
<form id="classificationForm" method="post" action="admin.php?action=classification_save">
    <input type="hidden" name="id" id="classificationId">
    <div class="dhgl">
        <div class="form-item">
            <label for="category_name">分类名称:</label>
            <input type="text" id="category_name" name="category_name" required>
        </div>
        <div class="form-item">
            <label for="match_keywords">关键词 (以逗号分隔):</label>
            <textarea id="match_keywords" name="match_keywords" rows="1" cols="50"></textarea>
        </div>
        <div class="form-item">
            <button type="submit" id="submitBtn">添加分类</button>
        </div>
    </div>
</form>

<?php if (!empty($classifications)): ?>
    <h3>已添加的分类</h3>
    <div class="toolbar">
        <form id="bulkDeleteForm" method="post" action="admin.php?action=classification_bulk_delete" onsubmit="return confirm('确定要删除选中的分类吗？');">
             <button type="submit" class="btn-danger">批量删除</button>
        </form>
    </div>
    <form id="classificationTableForm" method="post">
        <table id="classTable" class="data-table data-table-copy">
            <thead>
                <tr>
                    <th class="no-copy"><input type="checkbox" id="selectAll"> 全选</th>
                    <th>分类名称</th>
                    <th>关键词</th>
                    <th class="no-copy">操作</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($classifications as $index => $classification): ?>
                <?php
                $id = $classification['category_name'] ?? '';
                $category_name = $classification['category_name'] ?? '';
                $match_keywords = $classification['match_keywords'] ?? [];
                $keywords_string = implode(', ', $match_keywords);
                ?>
                <tr>
                    <td class="no-copy"><input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($id); ?>"></td>
                    <td><?php echo htmlspecialchars($category_name); ?></td>
                    <td><?php echo htmlspecialchars($keywords_string); ?></td>
                    <td class="no-copy action-cell">
                        <a href="#" class="edit-btn" data-id="<?php echo htmlspecialchars($id); ?>" data-name="<?php echo htmlspecialchars($category_name); ?>" data-keywords="<?php echo htmlspecialchars($keywords_string); ?>">编辑</a>
                        <a href="admin.php?action=classification_delete&id=<?php echo urlencode($id); ?>" onclick="return confirm('确定要删除这个分类吗？');" class="delete-btn">删除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div id="pagination-controls" class="pagination-controls"></div>
    </form>
<?php else: ?>
    <p>当前没有已添加的分类。</p>
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
    .form-item textarea {
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        min-width: 200px; /* 稍微宽一点给关键词 */
        height: 32px;
        box-sizing: border-box;
        font-size: 13px;
    }
    
    /* 文本域高度自适应 */
    .form-item textarea {
        min-height: 32px;
        resize: vertical;
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

    .toolbar { margin-bottom: 15px; display: flex; flex-wrap: wrap; align-items: center; }
    .toolbar form { margin: 0; }
    
    /* 表格样式继承 */
    .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; background: #fff; }
    .data-table th, .data-table td { border: 1px solid #ebeef5; padding: 10px; text-align: left; word-break: break-all; color: #606266; }
    .data-table th { background-color: #f5f7fa; font-weight: bold; color: #909399; }
    .data-table tbody tr:hover { background-color: #f5f7fa; }

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
    .btn-danger { background-color: #F56C6C; border-color: #F56C6C; color: #fff !important; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; }
    .btn-danger:hover { background-color: #f78989; border-color: #f78989; }

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
        const table = document.getElementById('classTable');
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
                if (currentPage > 1) {
                    currentPage--;
                    render();
                }
            });

            document.getElementById('next-page').addEventListener('click', (e) => {
                 e.preventDefault();
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
        
        const editButtons = document.querySelectorAll('.edit-btn');
        const form = document.getElementById('classificationForm');
        const idInput = document.getElementById('classificationId');
        const categoryInput = document.getElementById('category_name');
        const keywordsInput = document.getElementById('match_keywords');
        const submitBtn = document.getElementById('submitBtn');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const keywords = this.getAttribute('data-keywords');

                idInput.value = id;
                categoryInput.value = name;
                keywordsInput.value = keywords;
                
                form.action = 'admin.php?action=classification_edit';
                submitBtn.textContent = '保存修改';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
        
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const tableForm = document.getElementById('classificationTableForm');
        if (bulkDeleteForm) {
            bulkDeleteForm.addEventListener('submit', function(e){
                const selected = Array.from(tableForm.querySelectorAll('input[name="selected_items[]"]:checked'));
                if (selected.length === 0) {
                    alert('请至少选择一个分类进行删除。');
                     e.preventDefault();
                    return false;
                }
            });
        }

        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });
        }
    });
</script>