<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - PopAPI</title>
    <link href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        /* =========================================
           1. 全局基础样式
           ========================================= */
        body {
            background-color: #f4f7f6;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            overflow-x: hidden;
            font-size: 14px;
        }

        h2 {
            color: #333;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 18px !important;
            font-weight: bold;
            line-height: 1.2;
            border-left: 4px solid #409EFF;
            padding-left: 10px;
        }
        
        h3 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #666;
        }

        /* =========================================
           2. 表单布局 (兼容搜狗/旧版浏览器)
           ========================================= */
        .dhgl, .bulk-add-container .dhgl {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .content-card .input-group, 
        .form-item {
            display: flex;
            flex-direction: column;
            margin: 0 10px 10px 0 !important;
            width: auto !important;
            float: none !important;
        }

        /* 标签样式 */
        .content-card label, label {
            font-weight: bold;
            min-width: auto !important;
            margin-bottom: 5px;
            font-size: 12px;
            color: #333;
            display: block;
        }

        /* 全局输入框样式 (注意：这里设置了 block，会导致搜索框换行，下面会修复) */
        .content-card input[type="text"], 
        .content-card input[type="password"], 
        .content-card select, 
        .content-card textarea {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            min-width: 150px;
            height: 32px;
            box-sizing: border-box;
            display: block; 
            font-size: 13px;
        }
        
        .content-card textarea {
            height: auto;
            min-height: 32px;
        }

        /* =========================================
           3. 搜索栏修复 (关键修复)
           ========================================= */
        .search-form, .search-box {
            display: inline-flex !important; /* 强制 Flex 布局 */
            align-items: center !important;  /* 垂直居中 */
            flex-wrap: nowrap !important;    /* 禁止换行 */
            vertical-align: middle;
        }

        /* 强制覆盖全局 block 样式，让搜索输入框变回行内块 */
        .search-form input[type="text"], 
        .search-box input[type="text"] {
            display: inline-block !important;
            width: auto !important;
            min-width: 200px;
            margin-bottom: 0 !important; /* 去掉下边距 */
            margin-right: 5px;
        }
        
        .search-form label, .search-box label {
            margin-bottom: 0 !important;
            margin-right: 5px;
        }
        
        .search-form button, .search-box button {
            margin-bottom: 0 !important;
        }

        /* =========================================
           4. 按钮美化
           ========================================= */
        button, .btn, input[type="submit"] {
            padding: 0 15px;
            background-color: #409EFF;
            color: white;
            border: 1px solid #409EFF;
            border-radius: 4px;
            cursor: pointer;
            height: 32px;
            line-height: 30px;
            font-size: 13px;
            display: inline-block;
            text-decoration: none;
            margin-bottom: 2px;
        }
        button:hover, .btn:hover, input[type="submit"]:hover {
            background-color: #66b1ff;
            border-color: #66b1ff;
            color: white;
            text-decoration: none;
        }
        
        /* 红色删除按钮 */
        a[onclick*="删除"], button[onclick*="删除"] {
            background-color: #F56C6C;
            border-color: #F56C6C;
        }
        a[onclick*="删除"]:hover, button[onclick*="删除"]:hover {
            background-color: #f78989;
            border-color: #f78989;
        }

        /* =========================================
           5. 表格样式
           ========================================= */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
            font-size: 13px;
        }
        th, td {
            border: 1px solid #ebeef5;
            padding: 8px 10px;
            text-align: left;
            color: #606266;
        }
        th {
            background-color: #f5f7fa;
            font-weight: bold;
            color: #909399;
        }
        tr:hover {
            background-color: #f5f7fa; 
        }

        /* =========================================
           6. 分页样式 (修复对齐)
           ========================================= */
        .pagination-controls { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 20px; 
            padding: 10px; 
            background-color: #fff; 
            border: 1px solid #ebeef5; 
            border-radius: 4px; 
        }

        .pagination-controls .page-size-selector {
            display: flex;
            align-items: center; 
        }

        .pagination-controls label {
            display: inline-block;
            margin-bottom: 0;
            margin-left: 5px;
            font-weight: normal;
            color: #606266;
            min-width: auto !important;
        }

        .pagination-controls select {
            height: 28px;
            line-height: 28px;
            padding: 0 5px;
            margin: 0;
            border: 1px solid #dcdfe6;
            border-radius: 3px;
            background-color: #fff;
            cursor: pointer;
            color: #606266;
            min-width: auto; 
            display: inline-block;
        }

        .pagination-controls button { 
            display: inline-flex; 
            align-items: center;
            justify-content: center;
            height: 28px;
            padding: 0 12px;
            margin: 0 2px; 
            border: 1px solid #dcdfe6; 
            border-radius: 3px; 
            background-color: #fff; 
            cursor: pointer; 
            color: #606266; 
            font-size: 13px;
            line-height: 1; 
        }

        .pagination-controls button:disabled { 
            cursor: not-allowed; 
            opacity: 0.6; 
            color: #c0c4cc; 
            background-color: #f5f7fa;
        }

        .pagination-controls .page-numbers button.active { 
            background-color: #409EFF; 
            color: white; 
            border-color: #409EFF; 
        }

        .pagination-controls .page-info {
            font-size: 13px;
            color: #606266;
        }

        /* =========================================
           7. 框架布局 (侧边栏)
           ========================================= */
        .admin-container { display: flex; min-height: 100vh; }
        
        .admin-sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: #ecf0f1;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            z-index: 1000;
            transition: left 0.3s;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid #34495e;
            background-color: #2c3e50;
        }
        .sidebar-header a { color: #fff; text-decoration: none; }

        .admin-sidebar .nav { margin-top: 10px; }
        .admin-sidebar .nav > li > a {
            color: #bdc3c7;
            padding: 12px 20px;
            border-left: 3px solid transparent;
            border-radius: 0;
            font-size: 14px;
        }
        .admin-sidebar .nav > li > a:hover, 
        .admin-sidebar .nav > li > a:focus {
            background-color: #34495e;
            color: #ffffff;
        }
        .admin-sidebar .nav > li.active > a {
            background-color: #e74c3c;
            color: #ffffff;
            border-left-color: #c0392b; 
        }
        .admin-sidebar .nav > li > a > i { margin-right: 8px; width: 20px; text-align: center; }

        /* 子菜单 */
        .sidebar-submenu {
            list-style: none;
            padding: 0;
            background-color: #22303d;
            display: none; 
        }
        .sidebar-menu-item.active .sidebar-submenu { display: block; }
        .sidebar-submenu > li > a {
            color: #95a5a6;
            display: block;
            padding: 10px 15px 10px 48px;
            text-decoration: none;
            font-size: 13px;
        }
        .sidebar-submenu > li > a:hover { color: #fff; }
        .sidebar-submenu > li > a.current { color: #fff; font-weight: bold; }

        /* 内容区 */
        .admin-content {
            margin-left: 220px;
            width: calc(100% - 220px);
            padding: 20px;
            transition: margin-left 0.3s, width 0.3s;
        }
        .content-card {
            background: #fff;
            padding: 25px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            min-height: 85vh;
        }

        /* 移动端适配 */
        .mobile-header { display: none; }
        .admin-overlay { display: none; }

        @media screen and (max-width: 768px) {
            .admin-sidebar { left: -220px; }
            .admin-sidebar.show { left: 0; }
            .admin-content { margin-left: 0; width: 100%; padding-top: 70px; }
            .mobile-header {
                display: flex;
                position: fixed; top: 0; left: 0; width: 100%; height: 50px;
                background: #fff; border-bottom: 1px solid #ddd;
                z-index: 1001; justify-content: space-between; align-items: center;
                padding: 0 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .admin-overlay {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5); z-index: 999;
                display: none;
            }
            .admin-overlay.show { display: block; }
        }
    </style>
</head>
<body>

    <div class="mobile-header">
        <span style="font-weight: bold; font-size: 18px; color: #333;">后台管理</span>
        <button class="btn btn-default" id="menu-toggle" style="border:none; background:transparent; font-size:20px; color:#333;">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="admin-overlay" id="overlay"></div>

    <div class="admin-container">
        <div class="admin-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="admin.php">PopAPI Admin</a>
            </div>
            
            <?php $current_action = $_GET['action'] ?? 'dashboard'; ?>

            <ul class="nav nav-pills nav-stacked">
                <li class="<?php echo ($current_action === 'dashboard') ? 'active' : ''; ?>">
                    <a href="admin.php?action=dashboard"><i class="fas fa-tachometer-alt"></i> 仪表盘</a>
                </li>
                <li class="<?php echo ($current_action === 'phonenumber') ? 'active' : ''; ?>">
                    <a href="admin.php?action=phonenumber"><i class="fas fa-phone-alt"></i> 电话管理</a>
                </li>
                <li class="<?php echo ($current_action === 'classification') ? 'active' : ''; ?>">
                    <a href="admin.php?action=classification"><i class="fas fa-tags"></i> 分类管理</a>
                </li>
                <li class="<?php echo ($current_action === 'verification_code') ? 'active' : ''; ?>">
                    <a href="admin.php?action=verification_code"><i class="fas fa-sms"></i> 接码管理</a>
                </li>
                <li class="<?php echo ($current_action === 'code_manager') ? 'active' : ''; ?>">
                    <a href="admin.php?action=code_manager"><i class="fas fa-tasks"></i> 查询码管理</a>
                </li>
                <li class="<?php echo ($current_action === 'expired_phones') ? 'active' : ''; ?>">
                    <a href="admin.php?action=expired_phones"><i class="fas fa-history"></i> 已过期电话</a>
                </li>

                <li class="sidebar-menu-item <?php echo ($current_action === 'verification_code_list') ? 'active' : ''; ?>" id="verification-list-menu">
                    <a href="#" id="toggle-submenu">
                        <i class="fas fa-list-ul"></i> 接码分类列表 <i class="fas fa-angle-down pull-right"></i>
                    </a>
                    <ul class="sidebar-submenu" style="<?php echo ($current_action === 'verification_code_list') ? 'display:block;' : ''; ?>">
                        <?php
                        if (function_exists('getClassificationData')) {
                            $classifications = getClassificationData();
                            foreach ($classifications as $classification) {
                                $category = htmlspecialchars($classification['id'] ?? '');
                                $category_name = htmlspecialchars($classification['category_name'] ?? '');
                                $current_category = $_GET['category'] ?? '';
                                $is_current = ($current_action === 'verification_code_list' && $current_category === $category);
                                $active_class = $is_current ? 'current' : '';
                                echo '<li><a href="admin.php?action=verification_code_list&category=' . urlencode($category) . '" class="' . $active_class . '"><i class="far fa-circle" style="font-size:12px;"></i> ' . $category_name . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </li>

                <li>
                    <a href="admin.php?action=logout"><i class="fas fa-sign-out-alt"></i> 退出登录</a>
                </li>
            </ul>
        </div>

        <div class="admin-content">
            <div class="content-card">
                <?php 
                    if (isset($content) && $content) {
                        require $content;
                    }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function(){
            $('#menu-toggle').click(function(e){
                e.stopPropagation();
                $('#sidebar').toggleClass('show');
                $('#overlay').toggleClass('show');
            });
            $('#overlay').click(function(){
                $('#sidebar').removeClass('show');
                $('#overlay').removeClass('show');
            });
            $('#toggle-submenu').click(function(e){
                e.preventDefault();
                $(this).next('.sidebar-submenu').slideToggle();
                $(this).find('.pull-right').toggleClass('fa-angle-down fa-angle-up');
            });
        });
    </script>
</body>
</html>