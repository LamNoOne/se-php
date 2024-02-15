<?php 


spl_autoload_register(
    function ($className) {
        $fileName = strtolower($className) . ".php";
        $dirRoot = dirname(__DIR__);
        if(file_exists($dirRoot . "/classes/controllers/{$fileName}")) {
            require $dirRoot . "/classes/controllers/{$fileName}";
        } else if(file_exists($dirRoot . "/classes/services/{$fileName}")) {
            require $dirRoot . "/classes/services/{$fileName}";
        }
    }
);

require dirname(__DIR__) . "/config.php";

if(session_id() === "") session_start();