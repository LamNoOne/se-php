<?php 

use Google\Client;
use Google\Service\Oauth2;

require dirname(__DIR__) . "/config.php";
require_once dirname(__DIR__) . "/vendor/google-api-php-client/vendor/autoload.php";

// Call Google API
$gClient = new Client();
$gClient->setApplicationName('Login to SEShop');
$gClient->setClientId(GOOGLE_CLIENT_ID);
$gClient->setClientSecret(GOOGLE_CLIENT_SECRET);
$gClient->addScope(Google\Service\Oauth2::USERINFO_PROFILE . " " . Google\Service\Oauth2::USERINFO_EMAIL);
$gClient->setRedirectUri(GOOGLE_REDIRECT_URL);
$gClient->setAccessType('offline');
$gClient->setState('pass-through-value');
$gClient->setPrompt('consent');
$gClient->setIncludeGrantedScopes(true);

$google_oauthV2 = new Oauth2($gClient);


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


if(session_id() === "") session_start();