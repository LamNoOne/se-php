<?php
ob_start();
require_once dirname(dirname(__DIR__)) . '/inc/utils.php';

class Auth
{
    private static function removeSessionInfo()
    {
        // Remove token and user data from the session 
        if (isset($_SESSION['token'])) unset($_SESSION['token']);
        if (isset($_SESSION['username'])) unset($_SESSION['username']);
        if (isset($_SESSION['firstName'])) unset($_SESSION['firstName']);
        if (isset($_SESSION['lastName'])) unset($_SESSION['lastName']);
        if (isset($_SESSION['email'])) unset($_SESSION['email']);
        if (isset($_SESSION['userId'])) unset($_SESSION['userId']);
        if (isset($_SESSION['image'])) unset($_SESSION['image']);
        if (isset($_SESSION['phoneNumber'])) unset($_SESSION['phoneNumber']);
        if (isset($_SESSION['address'])) unset($_SESSION['address']);
        if (isset($_SESSION['userId'])) unset($_SESSION['userId']);
        if (isset($_SESSION['roleId'])) unset($_SESSION['roleId']);
        if (isset($_SESSION['logged_in'])) unset($_SESSION['logged_in']);

        static::logout();
    }

    #Is login
    public static function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    }

    #Require login
    public static function requireLogin()
    {
        if (!static::isLoggedIn()) {
            echo "<script>window.location.href='" . APP_URL . "/auth/'</script>";
        }
    }

    public static function requireAdmin($conn)
    {
        if (
            !isset($_SESSION['roleId'])
            && !isset($_SESSION['userId'])
            && !$_SESSION['roleId']
            && !$_SESSION['userId']
        ) {
            redirectByServer(APP_URL . '/auth/');
            return;
        }
        $roleId = $_SESSION['roleId'];
        $userId = $_SESSION['userId'];

        $getRoleResult = static::getRoleById($conn, $roleId);
        if (!$getRoleResult['status']) {
            redirectByServer(APP_URL . '/auth/');
        }
        $getUserResult = User::getUserByIdV2($conn, $userId);
        if (!$getUserResult['status']) {
            redirectByServer(APP_URL . '/auth/');
        }

        $foundRole = $getRoleResult['data'];
        $foundUser = $getUserResult['data'];

        // logout when something unusual happened
        if (
            !$foundRole
            || !$foundUser
            || $roleId !== $foundUser->roleId
            || $foundRole->id !== $foundUser->roleId
            || (int) $foundRole->id !== ADMIN
        ) {
            static::removeSessionInfo();
            redirectByServer(APP_URL . '/auth/');
            exit();
        }
        return true;
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

    public static function getRoleById($conn, $id)
    {
        try {
            $stmt = getQuerySQLPrepareStatement(
                $conn,
                [],
                [
                    'tables' => [
                        TABLES['ROLE']
                    ]
                ],
                [
                    [
                        'table' => TABLES['ROLE'],
                        'column' => 'id',
                        'value' => $id
                    ]
                ]
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new Exception('Cannot execute sql query');
            }
            return Message::messageData(
                true,
                'Get role successfully',
                $stmt->fetch()
            );
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }
}
