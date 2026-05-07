<h2>查询码管理 (used_codes.json)</h2>

<?php
// 显示提示消息
if (isset($_SESSION['success_message'])) {
    echo '<p style="color: green; font-weight: bold; background-color: #f0f9eb; padding: 10px; border-radius: 4px;">' . htmlspecialchars($_SESSION['success_message']) . '</p>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<p style="color: red; font-weight: bold; background-color: #fef0f0; padding: 10px; border-radius: 4px;">' . htmlspecialchars($_SESSION['error_message']) . '</p>';
    unset($_SESSION['error_message']);
}
?>

<div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <div class="action-group" style="display: flex; gap: 10px;">
        <button type="button" class="btn-primary" id="btnShowAddModal">
            <i class="fas fa-plus"></i> 新建查询码
        </button>
        <button type="submit" form="managerForm" formaction="admin.php?action=used_codes_bulk_delete" onclick="return confirm('确定要删除选中的查询码吗？删除后这些码可能会被系统再次生成。')" class="btn-danger">
            <i class="fas fa-trash"></i> 批量删除
        </button>
    </div>

    <div class="search-box">
        <form method="GET" action="admin.php" class="search-form">
            <input type="hidden" name="action" value="code_manager">
            <label style="margin-right: 5px; margin-bottom: 0; font-weight: normal;">搜索:</label>
            <input type="text" name="search_term" placeholder="输入查询码..." value="<?php echo htmlspecialchars($_GET['search_term'] ?? ''); ?>">
            <button type="submit" class="btn-primary">搜索</button>
            <?php if (!empty($_GET['search_term'])): ?>
                <a href="admin.php?action=code_manager" class="btn-default">清空</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<form id="managerForm" method="post">
    <table id="codesTable" class="data-table data-table-copy">
        <thead>
            <tr>
                <th class="no-copy" width="40"><input type="checkbox" id="selectAll"></th>
                <th data-sort="text">查询码 (Code) <span class="sort-icon"></span></th>
                <th class="no-copy" width="100">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($verificationData)): ?>
                <tr><td colspan="3" style="text-align:center; padding: 20px; color: #999;">暂无数据</td></tr>
            <?php else: ?>
                <?php foreach ($verificationData as $code): ?>
                    <tr>
                        <td class="no-copy"><input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($code); ?>"></td>
                        <td class="code-cell" style="font-weight:bold; color:#409EFF;"><?php echo htmlspecialchars($code); ?></td>
                        <td class="no-copy action-cell">
                            <a href="admin.php?action=used_codes_delete&code=<?php echo urlencode($code); ?>" onclick="return confirm('确定要删除这个查询码吗？');" class="delete-btn">
                                删除
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <div id="pagination-controls" class="pagination-controls"></div>
</form>

<div id="codeManagerModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-box">
        <div class="custom-modal-header">
            <h3>新建查询码</h3>
            <span class="custom-close-btn" id="btnClockModal">&times;</span>
        </div>
        <form method="post" action="admin.php?action=used_codes_save">
            <div class="custom-modal-body">
                <div class="form-item" style="margin-bottom: 15px;">
                    <label for="verification_code" style="display:block; margin-bottom:5px; color:#333;">查询码:</label>
                    <input type="text" id="verification_code" name="verification_code" required placeholder="例如：ABC123456" style="width: 100%; padding:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">注意：手动添加后，系统在生成新码时将避开此码。</p>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn-default" id="btnCancelModal" style="margin-right: 10px;">取消</button>
                <button type="submit" class="btn-primary">保存</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* 局部样式优化 */
    
    /* 按钮基础样式 */
    .btn-primary, .btn-danger, .btn-info, .btn-default {
        padding: 6px 12px;
        font-size: 13px;
        border-radius: 4px;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-block;
        text-decoration: none;
        color: #fff;
        line-height: 1.5;
    }
    
    .btn-primary { background-color: #409EFF; border-color: #409EFF; }
    .btn-primary:hover { background-color: #66b1ff; }

    .btn-danger { background-color: #F56C6C; border-color: #F56C6C; }
    .btn-danger:hover { background-color: #f78989; }

    .btn-default { background-color: #fff; border-color: #dcdfe6; color: #606266 !important; }
    .btn-default:hover { color: #409EFF !important; border-color: #c6e2ff; background-color: #ecf5ff; }

    /* 删除按钮样式 - 实心红 */
    .delete-btn {
        display: inline-block;
        padding: 2px 8px;
        font-size: 12px;
        border-radius: 3px;
        text-decoration: none;
        color: #fff !important;
        background-color: #F56C6C;
        border: none;
        cursor: pointer;
        line-height: 1.5;
    }
    .delete-btn:hover {
        background-color: #f78989;
        color: #fff !important;
        text-decoration: none;
    }

    /* 搜索框样式 */
    .search-form input {
        height: 32px;
        padding: 5px 10px;
        border: 1px solid #dcdfe6;
        border-radius: 4px;
        box-sizing: border-box;
        min-width: 200px;
        margin-right: 5px;
    }
    
    /* 分页栏样式 */
    .pagination-controls {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 15px; padding: 10px; background: #fff; border: 1px solid #ebeef5; border-radius: 4px;
    }

    /* --- 独立的 Modal 样式 (防止冲突) --- */
    .custom-modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex; justify-content: center; align-items: center;
        z-index: 2000; /* 极高的层级，确保在最上层 */
    }
    .custom-modal-box {
        background-color: #fff;
        width: 400px;
        border-radius: 4px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: fadeIn 0.2s;
    }
    .custom-modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex; justify-content: space-between; align-items: center;
    }
    .custom-modal-header h3 { margin: 0; font-size: 16px; color: #333; font-weight: bold; }
    .custom-close-btn { font-size: 20px; cursor: pointer; color: #999; }
    .custom-close-btn:hover { color: #333; }
    .custom-modal-body { padding: 20px; }
    .custom-modal-footer {
        padding: 10px 20px;
        background-color: #f9f9f9;
        text-align: right;
        border-top: 1px solid #eee;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 弹窗逻辑 (使用新的唯一ID) ---
        const modal = document.getElementById('codeManagerModal');
        const openBtn = document.getElementById('btnShowAddModal');
        const closeBtn = document.getElementById('btnClockModal');
        const cancelBtn = document.getElementById('btnCancelModal');

        function openModal() { 
            if(modal) modal.style.display = 'flex'; 
        }
        function closeModal() { 
            if(modal) modal.style.display = 'none'; 
        }

        if (openBtn) openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });
        
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        
        // 点击遮罩层关闭
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                closeModal();
            }
        });

        // --- 表格排序和分页 ---
        const table = document.getElementById('codesTable');
        const tableBody = table ? table.querySelector('tbody') : null;
        const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')) : [];
        const controlsContainer = document.getElementById('pagination-controls');
        let currentPage = 1;
        let rowsPerPage = 30;

        function render() {
            if (!tableBody) return;
            const totalRows = allRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            
            tableBody.innerHTML = '';
            const visibleRows = allRows.slice(start, end);
            if (visibleRows.length === 0 && totalRows > 0) {
                 // 保持空
            } else if (visibleRows.length === 0 && totalRows === 0) {
                 tableBody.innerHTML = '<tr><td colspan="3" style="text-align:center; padding: 20px; color: #999;">暂无数据</td></tr>';
            } else {
                visibleRows.forEach(row => tableBody.appendChild(row));
            }
            
            updatePaginationControls(totalPages, totalRows);
        }

        function updatePaginationControls(totalPages, totalRows) {
            if (!controlsContainer) return;
            controlsContainer.innerHTML = `
                <div class="page-size-selector" style="display:flex; align-items:center;">
                    <select id="rows-per-page" style="height:28px; border:1px solid #dcdfe6; border-radius:3px; margin-right: 5px;">
                        <option value="30" ${rowsPerPage==30?'selected':''}>30</option>
                        <option value="50" ${rowsPerPage==50?'selected':''}>50</option>
                        <option value="100" ${rowsPerPage==100?'selected':''}>100</option>
                        <option value="999999" ${rowsPerPage==999999?'selected':''}>全部</option>
                    </select>
                    <label style="margin:0; font-weight:normal; color:#606266;">条/页</label>
                </div>
                <div class="page-info" style="color:#606266; font-size:13px;">共 ${totalRows} 条，第 ${currentPage}/${totalPages||1} 页</div>
                <div class="page-buttons" style="display:flex;">
                    <button type="button" id="prev-page" ${currentPage===1?'disabled':''} style="margin-right:5px; padding:2px 10px; border:1px solid #dcdfe6; background:#fff; border-radius:3px; cursor:pointer;">上一页</button>
                    <button type="button" id="next-page" ${currentPage>=totalPages?'disabled':''} style="padding:2px 10px; border:1px solid #dcdfe6; background:#fff; border-radius:3px; cursor:pointer;">下一页</button>
                </div>
            `;
            
            document.getElementById('rows-per-page').addEventListener('change', (e) => {
                rowsPerPage = parseInt(e.target.value); currentPage = 1; render();
            });
            document.getElementById('prev-page').addEventListener('click', () => {
                if(currentPage > 1) { currentPage--; render(); }
            });
            document.getElementById('next-page').addEventListener('click', () => {
                if(currentPage < totalPages) { currentPage++; render(); }
            });
        }

        const selectAll = document.getElementById('selectAll');
        if(selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        }
        
        document.querySelectorAll('th[data-sort]').forEach(header => {
            header.addEventListener('click', function() {
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
                    let aValue = aCell.textContent.trim();
                    let bValue = bCell.textContent.trim();

                    if (aValue < bValue) return sortDir === 'asc' ? -1 : 1;
                    if (aValue > bValue) return sortDir === 'asc' ? 1 : -1;
                    return 0;
                });
                
                currentPage = 1; 
                render(); 
            });
        });

        render();
    });
</script>