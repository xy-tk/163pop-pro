<?php
// classes/Auth.php
class Auth {
    // 检查是否登录
    public static function checkLogin() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // 登录验证逻辑（适配哈希加密）
    public static function doLogin($username, $password) {
        global $pdo; 
        
        // 防暴力破解拦截
        if (isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']) {
            return false;
        }

        // 1. 先根据用户名查询出记录
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // 2. 使用 password_verify 校验输入的明文密码与数据库里的哈希值是否匹配
        if ($user && password_verify($password, $user['password'])) {
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_locked_until']);
            $_SESSION['logged_in'] = true;
            return true;
        }
        
        // 登录失败记录
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['login_locked_until'] = time() + (3 * 3600); // 错3次锁定3小时
        }
        return false;
    }

    // 退出登录
    public static function doLogout() {
        unset($_SESSION['logged_in']);
        session_destroy();
    }
}
