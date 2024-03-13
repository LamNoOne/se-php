<?php

require_once dirname(__DIR__) . '/inc/init.php';
 
// Remove token and user data from the session 
unset($_SESSION['token']); 
 
// Reset OAuth access token 
$gClient->revokeToken(); 
 
// Destroy entire session data 
if ($_SERVER['REQUEST_METHOD'] === 'POST')
    Auth::logout();

exit(); 
