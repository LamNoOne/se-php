<?php

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

    public function __construct(
        $id,
        $lastName,
        $firstName,
        $imageUrl,
        $phoneNumber,
        $email,
        $address,
        $username,
        $password,
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->imageUrl = $imageUrl;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->address = $address;
        $this->username = $username;
        $this->password = $password;
    }

    public static function createUser($conn, $adminId, $userData) {
        /**
         * Write your code here
         * Validate admin and userData
         */
    }

    public static function updateUser($conn, $adminId, $userData) {
        /**
         * Write your code here
         * Validate admin and userData 
         */
    }

    public static function deleteUser($conn, $adminId, $userId) {
        /**
         * Write your code here
         * Validate admin and userData
         */
    }

    public static function getUsers($conn, $adminId) {
        /**
         * Write your code here
         */
    }

    public static function getUserById($conn, $userId) {
        /**
         * Write your code here
         */
    }

    public static function getUserByUsername($conn) {
        /**
         * Write your code here
         */
    }
}
