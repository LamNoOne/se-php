<?php

require_once dirname(__DIR__) . '/inc/init.php';

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
if (isset($_SESSION['logged_in'])) unset($_SESSION['logged_in']);

// Reset OAuth access token 
$gClient->revokeToken();

// Destroy entire session data 
if ($_SERVER['REQUEST_METHOD'] === 'POST')
    Auth::logout();

exit();
