<?php

require_once dirname(__DIR__) . '/inc/init.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST')
    Auth::logout();
