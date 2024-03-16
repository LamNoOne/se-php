<?php
ob_start();
class Auth
{
    #Is login
    public static function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    }

    #Require login
    public static function requireLogin()
    {
        if (!static::isLoggedIn()) {
            echo "<script>window.location.href='". APP_URL ."/auth/'</script>";
        }
    }
    #Handle login
    public static function login()
    {
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
    }
    #Handle logout
    public static function logout()
    {
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }
}
