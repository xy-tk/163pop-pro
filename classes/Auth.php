<?php
// classes/Auth.php
class Auth {
    public static function checkLogin() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function doLogin($username, $password) {
        global $pdo; // 调用 admin.php 中实例化的数据库连接
        
        if (isset($_SESSION['login_locked_until']) && time() < $_SESSION['login_locked_until']) {
            return false;
        }

        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        
        if ($stmt->fetch()) {
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_locked_until']);
            $_SESSION['logged_in'] = true;
            return true;
        }
        
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['login_locked_until'] = time() + (3 * 3600); // 错3次锁定3小时
        }
        return false;
    }

    public static function doLogout() {
        unset($_SESSION['logged_in']);
        session_destroy();
    }
}