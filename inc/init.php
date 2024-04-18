<?php 

require dirname(__DIR__) . "/config.php";
require_once dirname(__DIR__) . "/vendor/google-api-php-client/vendor/autoload.php";

$guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
// Call Google API
$gClient = new Google\Client();
$gClient->setApplicationName('Login to SEShop');
$gClient->setClientId(GOOGLE_CLIENT_ID);
$gClient->setClientSecret(GOOGLE_CLIENT_SECRET);
$gClient->addScope(Google\Service\Oauth2::USERINFO_PROFILE . " " . Google\Service\Oauth2::USERINFO_EMAIL);
$gClient->setRedirectUri(GOOGLE_REDIRECT_URL);
$gClient->setAccessType('offline');
$gClient->setState('pass-through-value');
$gClient->setPrompt('consent');
$gClient->setIncludeGrantedScopes(true);
$gClient->setHttpClient($guzzleClient);

$google_oauthV2 = new Google\Service\Oauth2($gClient);


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