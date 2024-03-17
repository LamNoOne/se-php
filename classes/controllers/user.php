<?php
require_once dirname(__DIR__) . "/services/message.php";
require_once dirname(__DIR__) . "/services/validation.php";
require_once dirname(dirname(__DIR__)) . "/inc/utils.php";
require_once dirname(__DIR__) . "/controllers/oauth.php";
class User extends OAuth
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
        $firstName = null,
        $lastName = null,
        $username = null,
        $email = null,
        $password = null,
        $imageUrl = null,
        $phoneNumber = null,
        $address = null,
        $roleId = 3,
        $active = 0
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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

    public static function oAuthenticate($conn, $oData = array())
    {
        try {
            if (empty($oData) || empty($oData['email'])) throw new Exception("No oAuth user data");
            $user = static::getUserByEmail($conn, $oData['email']);
            $userId = empty($user) ? static::signUpOAuth($conn, $oData) : $userId = static::signInOAuth($conn, $user->id, $oData);
            if (empty($userId)) throw new Exception("Can not authenticate user");
            return static::getUserById($conn, $userId);
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }


    public static function signUpOAuth($conn, $data)
    {
        try {
            $user = new User(
                $data['firstName'],
                $data['lastName'],
                $data['username'],
                $data['email'],
                $data['password'] ?? null,
                $data['imageUrl'],
                $data['$phoneNumber'] ?? null,
                $data['$address'] ?? null,
                $data['$roleId'] ?? 3,
                $data['$active'] ?? 1
            );

            $userResponse = $user->createUser($conn);
            if (!$userResponse['status']) throw new Exception("User creation failed");
            $userId = $userResponse['data']['userId'];

            $oauth = new OAuth($data['oauthId'], $userId, $data['oauthProvider']);
            if (!$oauth->register($conn)) throw new Exception("User registration failed");

            return $userId;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function signInOAuth($conn, $userId, $data)
    {
        try {
            $oauth = OAuth::getOauth($conn, $data['oauthId'], $data['oauthProvider']);
            if (empty($oauth)) {
                $oauthRegister = new OAuth($data['oauthId'], $userId, $data['oauthProvider']);
                if (!$oauthRegister->register($conn)) throw new Exception("User registration failed");
            }
            return $userId;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function createUser($conn)
    {
        try {

            // check whether user is already exists in database
            $userByEmail = static::getUserByEmail($conn, $this->email);
            if (!empty($userByEmail))
                return Message::message(false, "Email is already taken");

            // check whether username is already taken
            $userByUsername = static::getUserByUsername($conn, $this->username);
            if (!empty($userByUsername))
                return Message::message(false, "Username is already taken");

            // create user
            $insertStmt = "INSERT INTO 
                user (lastName, firstName, imageUrl, phoneNumber, email, address, username, password, active) 
                VALUES (:lastName, :firstName, :imageUrl, :phoneNumber, :email, :address, :username, :password, :active)";
            $stmt = $conn->prepare($insertStmt);
            $password_hash = $this->password ? password_hash($this->password, PASSWORD_DEFAULT) : null;
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

    public static function updateUserPassword($conn, $userId, $password)
    {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET password=:password WHERE id=:userId";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $status = $stmt->execute();
            if (!$status) {
                throw new PDOException("Can not execute query");
            }
            return Message::message(true, "Update password successfully");
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

    public static function changeUserPasswordByEmail($conn, $email, $password)
    {
        try {
            // check email is exists in database
            $user = static::getUserByEmail($conn, $email);
            if (empty($user))
                throw new Exception("Email does not exist");

            // change password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET password=:password WHERE email=:email";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $status = $stmt->execute();
            if (!$status) {
                throw new PDOException("Can not execute query");
            }
            return Message::message(true, "Update password successfully");
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

    public static function getUsers(
        $conn,
        $filter = [['field' => '', 'value' => '', 'like' => false]],
        $pagination = [],
        $sort =  [['sortBy' => 'createdAt', 'order' => 'ASC']]
    ) {
        try {
            $selectConditions = [
                'id' => ['table' => TABLES['USER'], 'column' => 'id'],
                'firstName' => ['table' => TABLES['USER'], 'column' => 'firstName'],
                'lastName' => ['table' => TABLES['USER'], 'column' => 'lastName'],
                'phoneNumber' => ['table' => TABLES['USER'], 'column' => 'phoneNumber'],
                'email' => ['table' => TABLES['USER'], 'column' => 'email'],
                'address' => ['table' => TABLES['USER'], 'column' => 'address'],
                'username' => ['table' => TABLES['USER'], 'column' => 'username'],
                'createdAt' => ['table' => TABLES['USER'], 'column' => 'createdAt'],
                'updatedAt' => ['table' => TABLES['USER'], 'column' => 'updatedAt'],
                'roleId' => ['table' => TABLES['ROLE'], 'column' => 'id'],
                'roleName' => ['table' => TABLES['ROLE'], 'column' => 'name']
            ];
            $sortConditions = [
                'id' => ['table' => TABLES['USER'], 'column' => 'id'],
                'firstName' => ['table' => TABLES['USER'], 'column' => 'firstName'],
                'lastName' => ['table' => TABLES['USER'], 'column' => 'lastName'],
                'phoneNumber' => ['table' => TABLES['USER'], 'column' => 'phoneNumber'],
                'email' => ['table' => TABLES['USER'], 'column' => 'email'],
                'address' => ['table' => TABLES['USER'], 'column' => 'address'],
                'username' => ['table' => TABLES['USER'], 'column' => 'username'],
                'createdAt' => ['table' => TABLES['USER'], 'column' => 'createdAt'],
                'updatedAt' => ['table' => TABLES['USER'], 'column' => 'updatedAt'],
                'roleId' => ['table' => TABLES['ROLE'], 'column' => 'id'],
                'roleName' => ['table' => TABLES['ROLE'], 'column' => 'name'],
            ];

            $selection = [];
            foreach ($filter as $filterItem) {
                $selectCondition = $selectConditions[$filterItem['field']];
                $selectCondition['value'] = $filterItem['value'];
                if (isset($filterItem['like']) && $filterItem['like'] !== NULL) {
                    $selectCondition['like'] = $filterItem['like'];
                }
                $selection[] = $selectCondition;
            }

            $sorter = [];
            foreach ($sort as $sortItem) {
                $sortCondition = $sortConditions[$sortItem['sortBy']];
                $sortCondition['order'] = $sortItem['order'];
                $sorter[] = $sortCondition;
            }

            $projection =  [
                [
                    'table' => TABLES['USER'],
                    'column' => 'id'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'firstName'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'lastName'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'imageUrl'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'phoneNumber'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'email'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'address'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'username'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'active'
                ],
                [
                    'table' => TABLES['ROLE'],
                    'column' => 'id',
                    'as' => 'roleId'
                ],
                [
                    'table' => TABLES['ROLE'],
                    'column' => 'name',
                    'as' => 'roleName'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'createdAt'
                ],
                [
                    'table' => TABLES['USER'],
                    'column' => 'updatedAt'
                ]
            ];

            $join =  [
                'tables' => [
                    TABLES['USER'],
                    TABLES['ROLE'],
                ],
                'on' => [
                    [
                        'table1' => TABLES['USER'],
                        'table2' => TABLES['ROLE'],
                        'column1' => 'roleId',
                        'column2' => 'id',
                    ]
                ]
            ];

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection,
                $pagination,
                $sorter,
            );
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Get users successfully', [
                'users' => $stmt->fetchAll()
            ]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return Message::message(false, 'Something went wrong');
        }
    }

    public static function getUserById($conn, $userId)
    {
        /**
         * Write your code here
         */
        try {
            $query = "SELECT U.id, U.firstName, U.lastName, U.imageUrl, U.phoneNumber, U.email, U.address, U.username, U.password, R.id as 'roleId', R.name as roleName, U.createdAt, U.updatedAt
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

    public static function getUserByUsername($conn, $username)
    {
        try {
            $query = "SELECT U.id, U.firstName, U.lastName, U.imageUrl, U.phoneNumber, U.email, U.address, U.username, R.id as 'roleId', R.name as roleName, U.createdAt, U.updatedAt
                FROM `user` U join `role` R on U.roleId = R.id
                WHERE U.username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->setFetchMode(PDO::FETCH_INTO, new User());
            if (!$stmt->execute()) {
                throw new PDOException("Can not execute query");
            }
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    public static function count(
        $conn,
        $filter = [['field' => 'id', 'value' => '', 'like' => false]]
    ) {
        try {
            $projection =  [
                [
                    'aggregate' => 'COUNT',
                    'expression' => '*',
                    'as' => 'total'
                ],
            ];
            $join =  [
                'tables' => [
                    TABLES['USER']
                ]
            ];
            $selection = array_map(function ($filterItem) {
                $selectionItem = [
                    'table' => TABLES['USER'],
                    'column' => $filterItem['field'],
                    'value' => $filterItem['value']
                ];
                if (isset($filterItem['like'])) {
                    $selectionItem['like'] = $filterItem['like'];
                }
                return $selectionItem;
            }, $filter);

            $stmt = getQuerySQLPrepareStatement(
                $conn,
                $projection,
                $join,
                $selection
            );

            $stmt->setFetchMode(PDO::FETCH_OBJ);
            if (!$stmt->execute()) {
                throw new PDOException('Cannot execute query');
            }
            return Message::messageData(true, 'Count users successfully', [
                'total' => $stmt->fetch()->total
            ]);
        } catch (Exception $e) {
            return Message::message(false, 'Something went wrong');
        }
    }
}
