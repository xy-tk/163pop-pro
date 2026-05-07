// /assets/js/main.js

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. 通用复制到剪贴板功能
    const copyButtons = document.querySelectorAll('.btn-copy');
    copyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-clipboard-text');
            if (textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    // 改变图标或颜色提示成功
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check text-success"></i> 已复制';
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                    }, 2000);
                }).catch(err => {
                    console.error('复制失败:', err);
                    alert('复制失败，请手动复制');
                });
            }
        });
    });

    // 2. 批量操作时的全选/取消全选
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
        });
    }

    // 3. 删除操作前的安全确认拦截
    const deleteLinks = document.querySelectorAll('.btn-delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('确定要删除这条数据吗？此操作不可逆！')) {
                e.preventDefault(); // 取消默认的跳转操作
            }
        });
    });

    // 4. 自动隐藏全局提示框 (如成功/失败信息)
    const alertBox = document.querySelector('.alert-dismissible');
    if (alertBox) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertBox);
            bsAlert.close();
        }, 5000); // 5秒后自动关闭
    }
});
