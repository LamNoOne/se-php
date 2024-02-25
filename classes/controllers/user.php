<?php
require_once dirname(__DIR__) . "/services/message.php";
class User
{
    public $id;
    public $roleId;
    public $lastName;
    public $firstName;
    public $imageUrl;
    public $phoneNumber;
    public $email;
    public $address;
    public $username;
    private $password;
    public $createdAt;
    public $updatedAt;
    public $active;

    public function __construct()
    {
    }

    public static function authenticate($conn, $email, $password)
    {
        try {
            $query = "SELECT * FROM user WHERE email=:email LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":email", $email, PDO::PARAM_INPUT_OUTPUT);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "User");
            if (!$stmt->execute())
                throw new PDOException("Cannot execute query");
            $stmt->execute();
            $user = $stmt->fetch();
            if ($user)
                if (password_verify($password, $user->password))
                    return Message::messageData(true, "Login successfully", $user);
            return Message::message(false, "Cannot login with given information");
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function createUser($conn, $adminId, $userData)
    {
        /**
         * Write your code here
         * Validate admin and userData
         */
    }

    public static function updateUser($conn, $adminId, $userData)
    {
        /**
         * Write your code here
         * Validate admin and userData 
         */
    }

    public static function deleteUser($conn, $adminId, $userId)
    {
        /**
         * Write your code here
         * Validate admin and userData
         */
    }

    public static function getUsers($conn, $adminId)
    {
        /**
         * Write your code here
         */
    }

    public static function getUserById($conn, $userId)
    {
        /**
         * Write your code here
         */
    }

    public static function getUserByUsername($conn)
    {
        /**
         * Write your code here
         */
    }
}
