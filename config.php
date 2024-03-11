<?php

/**
 * Define database configuration
 */
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'se-php');
if (!defined('DB_USER')) define('DB_USER', "root");
if (!defined('DB_PASS')) define('DB_PASS', "");

/**
 * Define APP URL
 */
if (!defined("APP_URL")) define("APP_URL", "http://localhost/se-php");

/**
 * Define timezone configuration
 */

if(!defined('TZ_DEFAULT')) define('TZ_DEFAULT', 'Asia/Ho_Chi_Minh');

/**
 * Define action methods
 */
if (!defined("DELETE")) define("DELETE", "DELETE");
if (!defined("DELETE_ALL")) define("DELETE_ALL", "DELETE_ALL");
if (!defined("CHECKOUT")) define("CHECKOUT", "CHECKOUT");

/**
 * Define order status
 */
if (!defined("PENDING")) define("PENDING", 1);
if (!defined("PAID")) define("PAID", 2);
if (!defined("DELIVERING")) define("DELIVERING", 3);
if (!defined("DELIVERED")) define("DELIVERED", 4);

/**
 * Debug mode
 */
if (!defined('DB_DEBUG')) define('DB_DEBUG', true);

/**
 * Define upload file configuration
 */
if (!defined('FILE_MAX_SIZE')) define('FILE_MAX_SIZE', 2 * 1024 * 1024);
if (!defined('FILE_TYPE')) define('FILE_TYPE', ['image/gif', 'image/png', 'image/jpeg']);

/**
 * Define mail configuration
 */

if(!defined('MAIL_HOST')) define('MAIL_HOST', 'smtp.gmail.com');
if(!defined('MAIL_SMTP_SECURE')) define('MAIL_SMTP_SECURE', 'tls');
if(!defined('MAIL_USER')) define('MAIL_USER', 'segroupdemo@gmail.com');
if(!defined('MAIL_PORT')) define('MAIL_PORT', 587);
if(!defined('MAIL_PASSWORD')) define('MAIL_PASSWORD', 'hngv jsge smvh ceqf');
if(!defined('OTP_EXPIRED_TIME')) define('OTP_EXPIRED_TIME', 300);

/* PayPal REST API configuration 
 * You can generate API credentials from the PayPal developer panel. 
 * See your keys here: https://developer.paypal.com/dashboard/ 
 */
if (!defined('CURRENCY')) define('CURRENCY', 'USD');
if (!defined('PAYPAL_SANDBOX')) define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production 
if (!defined('PAYPAL_SANDBOX_CLIENT_ID')) define('PAYPAL_SANDBOX_CLIENT_ID', 'AQynCXsQNrYzwiFjdl6BQYMbis9PlyNncgioermsVIWTt5rjztGuiQ-17vqBSt5oR3flw_hv5JSvy9N4');
if (!defined('PAYPAL_SANDBOX_CLIENT_SECRET')) define('PAYPAL_SANDBOX_CLIENT_SECRET', 'EIiT3ZdjJz0SWQCwk39EtwD4EBFDXsRi6eD3vNfSXGm4ujV2Upk6ZY65VOw1LkqSUBSIo786rjucnWF1');
if (!defined('PAYPAL_PROD_CLIENT_ID')) define('PAYPAL_PROD_CLIENT_ID', 'Insert_Live_PayPal_Client_ID_Here');
if (!defined('PAYPAL_PROD_CLIENT_SECRET')) define('PAYPAL_PROD_CLIENT_SECRET', 'Insert_Live_PayPal_Secret_Key_Here');
