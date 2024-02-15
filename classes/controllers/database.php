<?php

class Database {
    protected $db_host;
    protected $db_name;
    protected $db_user;
    protected $db_pass;

    public function __construct($db_host, $db_name, $db_user, $db_pass) {
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
    }

    public function getConnection() {
        $dns = "mysql:host=" . $this->db_host . ";dbname=" . $this->db_name . ";";
        try {
            $connection = new PDO($dns, $this->db_user, $this->db_pass);
            $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    } 
}