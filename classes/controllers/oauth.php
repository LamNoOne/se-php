<?php

class OAuth
{
    public $id;
    public $oauth_uid;
    public $userId;
    public $oauth_provider;

    public function __construct($oauth_uid = null, $userId = null, $oauth_provider = null)
    {
        $this->oauth_uid = $oauth_uid;
        $this->userId = $userId;
        $this->oauth_provider = $oauth_provider;
    }

    protected function register($conn)
    {
        try {
            $insert = "INSERT INTO oauth (oauth_uid, userId, oauth_provider) VALUES (:oauth_uid, :userId, :oauth_provider)";
            $stmt = $conn->prepare($insert);
            $status = $stmt->execute([
                ":oauth_uid" => $this->oauth_uid,
                ":userId" => $this->userId,
                ":oauth_provider" => $this->oauth_provider
            ]);
            return $status;
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }

    protected static function getOauth($conn, $oauth_uid, $oauth_provider)
    {
        try {
            $select = "SELECT * FROM oauth WHERE oauth_uid = :oauth_uid AND oauth_provider = :oauth_provider";
            $stmt = $conn->prepare($select);
            $stmt->bindValue(':oauth_uid', $oauth_uid, PDO::PARAM_STR);
            $stmt->bindValue(':oauth_provider', $oauth_provider, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new Exception("Oauth not found");
            }
            $stmt->setFetchMode(PDO::FETCH_INTO, new Oauth());
            return $stmt->fetch();
        } catch (Exception $e) {
            return Message::message(false, $e->getMessage());
        }
    }
}
