<?php

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'se-php');
if (!defined('DB_USER')) define('DB_USER', "root");
if (!defined('DB_PASS')) define('DB_PASS', "");

// Define action methods
if(!defined("DELETE")) define("DELETE", "DELETE");
if(!defined("DELETE_ALL")) define("DELETE_ALL", "DELETE_ALL");
if(!defined("CHECKOUT")) define("CHECKOUT", "CHECKOUT");

// Debug mode
if(!defined('DB_DEBUG')) define('DB_DEBUG', true);

/* PayPal REST API configuration 
 * You can generate API credentials from the PayPal developer panel. 
 * See your keys here: https://developer.paypal.com/dashboard/ 
 */

if(!defined('CURRENCY')) define('CURRENCY', 'USD');
if(!defined('PAYPAL_SANDBOX')) define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production 
if(!defined('PAYPAL_SANDBOX_CLIENT_ID')) define('PAYPAL_SANDBOX_CLIENT_ID', 'Insert_Sandbox_PayPal_Client_ID_Here');
if(!defined('PAYPAL_SANDBOX_CLIENT_SECRET')) define('PAYPAL_SANDBOX_CLIENT_SECRET', 'Insert_Sandbox_PayPal_Secret_Key_Here');
if(!defined('PAYPAL_PROD_CLIENT_ID')) define('PAYPAL_PROD_CLIENT_ID', 'Insert_Live_PayPal_Client_ID_Here');
if(!defined('PAYPAL_PROD_CLIENT_SECRET')) define('PAYPAL_PROD_CLIENT_SECRET', 'Insert_Live_PayPal_Secret_Key_Here');