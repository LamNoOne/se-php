<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/validation.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
class User
{
    public $lastName;
    public $firstName;
    public $username;
    public $email;
    public $password;
    public $phoneNumber;
    public $address;
    public $imageUrl;
    public $roleId;
    public $active;

    public function __construct(
        $lastName = null,
        $firstName = null,
        $username = null,
        $email = null,
        $password = null,
        $phoneNumber = null,
        $address = null,
        $imageUrl = null,
        $roleId = 3,
        $active = 0
    ) {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->phoneNumber = $phoneNumber;
        $this->address = $address;
        $this->imageUrl = $imageUrl;
        $this->roleId = $roleId;
        $this->active = $active;
    }

    public static function authenticate($conn, $email, $password)
    {
        try {
            $query = "SELECT * FROM user WHERE email=:email LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":email", $email, PDO::PARAM_INPUT_OUTPUT);
            $stmt->setFetchMode(PDO::FETCH_INTO, new User());
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

    public function createUser($conn)
    {
        try {

            // check whether user is already exists in database
            $query = "SELECT * FROM user WHERE email=:email LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_INTO, new User());
            if (!$stmt->execute())
                throw new PDOException("Cannot execute query");
            $stmt->execute();
            $user = $stmt->fetch();
            if (!empty($user))
                return Message::message(false, "Email is already taken");

            // check whether username is already taken
            $query = "SELECT * FROM user WHERE username=:username LIMIT 1";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_INTO, new User());
            if (!$stmt->execute())
                throw new PDOException("Cannot execute query");
            $stmt->execute();
            $user = $stmt->fetch();
            if (!empty($user))
                return Message::message(false, "Username is already taken");

            // create user
            $insertStmt = "INSERT INTO 
                user (lastName, firstName, imageUrl, phoneNumber, email, address, username, password, active) 
                VALUES (:lastName, :firstName, :imageUrl, :phoneNumber, :email, :address, :username, :password, :active)";
            $stmt = $conn->prepare($insertStmt);
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
            // $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);
            $status = $stmt->execute([
                ":lastName" => $this->lastName,
                ":firstName" => $this->firstName,
                ":imageUrl" => $this->imageUrl,
                ":phoneNumber" => $this->phoneNumber,
                ":email" => $this->email,
                ":address" => $this->address,
                ":username" => $this->username,
                ":password" => $password_hash,
                ":active" => $this->active,
            ]);
            if (!$status)
                return Message::message(false, "Can not create user at this time");

            $userId = $conn->lastInsertId();
            return Message::messageData(true, "Create user successfully", ['userId' => $userId]);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function updateUser($conn, $userId, $userData)
    {
        try {
            // define user data pattern
            $userPattern = ['lastName', 'firstName', 'imageUrl', 'phoneNumber', 'address', 'username', 'active'];
            // validate user data
            if (!Validation::validateData($userPattern, $userData)) {
                throw new InvalidArgumentException('Invalid user data');
            }

            //  check if phone number is exist, throw error because phone number is unique in database
            if (!empty($userData['phoneNumber'])) {
                $query = "SELECT * FROM user WHERE phoneNumber=:phoneNumber LIMIT 1";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(":phoneNumber", $userData['phoneNumber'], PDO::PARAM_STR);
                $stmt->setFetchMode(PDO::FETCH_INTO, new User());
                if (!$stmt->execute())
                    throw new PDOException("Cannot execute query");
                $stmt->execute();
                $user = $stmt->fetch();
                if (!empty($user) && $user->id !== $userId)
                    return Message::message(false, "Phone number is already taken");
            }

            // Build user query string
            $userKeys = array_keys($userData);
            $queryBuilder = "UPDATE user SET";
            // Create set values depends on user data
            $setQueryBuilder = implode(",", array_map(fn ($key) => $key . "=:" . $key, $userKeys));
            $conditionQueryBuilder = "WHERE id=:userId";
            $updateStmt = $queryBuilder . " " . $setQueryBuilder . " " . $conditionQueryBuilder;

            $stmt = $conn->prepare($updateStmt);
            foreach ($userKeys as $key) {
                $stmt->bindValue(":" . $key, $userData[$key], PDO::PARAM_INPUT_OUTPUT);
            }
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $status = $stmt->execute();
            if (!$status) {
                throw new PDOException("Can not execute query");
            }
            return Message::message(true, "Update user successfully");
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function changeUserPassword($conn, $userId, $oldPassword, $newPassword)
    {
        try {
            $query = "SELECT password FROM user WHERE id=:userId";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_INTO, new User());
            if (!$stmt->execute())
                throw new PDOException("Cannot execute query");
            $stmt->execute();
            $user = $stmt->fetch();
            if (password_verify($oldPassword, $user->password)) {
                $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
                $query = "UPDATE user SET password=:password WHERE id=:userId";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);
                $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
                $status = $stmt->execute();
                if (!$status) {
                    throw new PDOException("Can not execute query");
                }
                return Message::message(true, "Update password successfully");
            }
            return Message::message(false, "Invalid current password");
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function deleteUser($conn, $adminId, $userId)
    {
        /**
         * Write your code here
         * Validate admin and userData
         */
    }

    public static function getAllUsers(
        $conn,
        $filter = [],
        $sorter = ['id' => 'ASC'],
        $paginator = []
    ) {
        try {
            $sqlConditions = generateSQLConditions($filter, $sorter, $paginator);

            $query = "
                SELECT U.id, U.firstName, U.lastName, U.imageUrl, U.phoneNumber, U.email, U.address, U.username, R.id as 'roleId', R.name as roleName, U.createdAt, U.updatedAt
                FROM `user` U join `role` R on U.roleId = R.id
                {$sqlConditions['where']}
                {$sqlConditions['orderBy']}
                {$sqlConditions['limit']}
                {$sqlConditions['offset']}
            ";
            $stmt = $conn->prepare($query);
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException("Can not execute query");
            }
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getUserById($conn, $userId)
    {
        /**
         * Write your code here
         */
        try {
            $query = "SELECT U.id, U.firstName, U.lastName, U.imageUrl, U.phoneNumber, U.email, U.address, U.username, R.id as 'roleId', R.name as roleName, U.createdAt, U.updatedAt
                FROM `user` U join `role` R on U.roleId = R.id
                WHERE U.id = :userId";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $stmt->setFetchMode(PDO::FETCH_INTO, new User());
            if (!$stmt->execute()) {
                throw new PDOException("Can not execute query");
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function getUserByEmail($conn, $email)
    {
        try {
            $query = "SELECT U.id, U.firstName, U.lastName, U.imageUrl, U.phoneNumber, U.email, U.address, U.username, R.id as 'roleId', R.name as roleName, U.createdAt, U.updatedAt
                FROM `user` U join `role` R on U.roleId = R.id
                WHERE U.email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_INTO, new User());
            if (!$stmt->execute()) {
                throw new PDOException("Can not execute query");
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}
