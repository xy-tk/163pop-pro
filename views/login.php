<h2>登录</h2>
<?php if (isset($error_message) && !empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<?php if (isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']): ?>
    <?php
        $remaining_time = ceil(($_SESSION['login_locked_until'] - time()) / 60);
        echo "<p style='color: red; font-weight: bold;'>登录尝试次数过多，请在 {$remaining_time} 分钟后再试。</p>";
    ?>
<?php else: ?>
    <form method="post" action="admin.php?action=login">
        <div style="margin-bottom: 15px;">
            <label for="username" style="display:inline-block; width: 70px; font-weight:bold;">用户名:</label>
            <input type="text" id="username" name="username" required autocomplete="username" style="padding: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password" style="display:inline-block; width: 70px; font-weight:bold;">密码:</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" style="padding: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="captcha" style="display:inline-block; width: 70px; font-weight:bold;">验证码:</label>
            <input type="text" id="captcha" name="captcha" required autocomplete="off" style="width: 80px; padding: 5px;">
            <img src="captcha.php" alt="CAPTCHA Image" style="vertical-align: middle; margin-left: 10px; border: 1px solid #ccc; cursor: pointer; height: 30px;" onclick="this.src='captcha.php?'+Math.random();" title="点击刷新验证码">
        </div>
        
        <button type="submit" style="padding: 6px 20px; cursor: pointer; background-color: #409EFF; color: white; border: none; border-radius: 4px;">登录</button>
    </form>
<?php endif; ?>