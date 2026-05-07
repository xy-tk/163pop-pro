<h2>仪表盘</h2>
<p>欢迎来到管理后台，<?php echo $_SESSION['username'] ?? '用户'; ?>！</p>

<?php
// 此处依赖于 admin.php 传递的 $dashboardData 变量
if (isset($dashboardData)):
?>
<div class="dashboard-stats">
    <div class="stat-card">
        <h3>总电话个数</h3>
        <p class="stat-number"><?php echo htmlspecialchars($dashboardData['total_phones'] ?? 0); ?></p>
    </div>

    <div class="stat-card">
        <h3>已添加的接码数据</h3>
        <p class="stat-number"><?php echo htmlspecialchars($dashboardData['total_added_codes'] ?? 0); ?></p>
        <ul class="stat-list">
            <?php foreach (($dashboardData['added_codes_by_category'] ?? []) as $category => $count): ?>
                <li>**<?php echo htmlspecialchars($category); ?>:** <?php echo htmlspecialchars($count); ?> 条</li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="stat-card">
        <h3>已过期电话</h3>
        <p class="stat-number"><?php echo htmlspecialchars($dashboardData['total_expired_codes'] ?? 0); ?></p>
        <ul class="stat-list">
            <?php foreach (($dashboardData['expired_codes_by_category'] ?? []) as $category => $count): ?>
                <li>**<?php echo htmlspecialchars($category); ?>:** <?php echo htmlspecialchars($count); ?> 条</li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<style>
    .dashboard-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }
    .stat-card {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        flex: 1;
        min-width: 250px;
    }
    .stat-card h3 {
        margin-top: 0;
        color: #0056b3;
    }
    .stat-number {
        font-size: 2.5em;
        font-weight: bold;
        color: #333;
        margin: 10px 0;
    }
    .stat-list {
        list-style-type: none;
        padding: 0;
    }
    .stat-list li {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    .stat-list li:last-child {
        border-bottom: none;
    }
</style>