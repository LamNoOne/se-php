<?php
    require dirname(__DIR__) . "/config.php";
    require dirname(__DIR__) . "/classes/controllers/database.php";

    $db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);

    return $db->getConnection();