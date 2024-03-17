<?php
if (!defined('BASE_NAME')) define('BASE_NAME', (basename(__DIR__) === 'www' || basename(__DIR__) === 'htdocs') ? '' : '/' . basename(__DIR__));
if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', '');
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', '');
if (!defined('GOOGLE_REDIRECT_URL')) define('GOOGLE_REDIRECT_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_NAME);

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
if (!defined("APP_URL")) define("APP_URL", 'http://' . $_SERVER['HTTP_HOST'] . BASE_NAME);

/**
 * Define timezone configuration
 */

if (!defined('TZ_DEFAULT')) define('TZ_DEFAULT', 'Asia/Ho_Chi_Minh');

/**
 * Define action for methods
 */
if (!defined("DELETE")) define("DELETE", "DELETE");
if (!defined("DELETE_ALL")) define("DELETE_ALL", "DELETE_ALL");
if (!defined("CHECKOUT")) define("CHECKOUT", "CHECKOUT");
if (!defined("FORGOT_PASSWORD")) define("FORGOT_PASSWORD", "FORGOT_PASSWORD");

/**
 * Define order status
 */
if (!defined("PENDING")) define("PENDING", 1);
if (!defined("PAID")) define("PAID", 2);
if (!defined("DELIVERING")) define("DELIVERING", 3);
if (!defined("DELIVERED")) define("DELIVERED", 4);
if (!defined("PENDING_CANCEL")) define("PENDING_CANCEL", 5);
if (!defined("CANCELLED")) define("CANCELLED", 6);

/**
 * Define role
 */
if (!defined("ADMIN")) define("ADMIN", 1);
if (!defined("CUSTOMER")) define("CUSTOMER", 2);

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

if (!defined('MAIL_HOST')) define('MAIL_HOST', 'smtp.gmail.com');
if (!defined('MAIL_SMTP_SECURE')) define('MAIL_SMTP_SECURE', 'tls');
if (!defined('MAIL_USER')) define('MAIL_USER', '');
if (!defined('MAIL_PORT')) define('MAIL_PORT', 587);
if (!defined('MAIL_PASSWORD')) define('MAIL_PASSWORD', '');
if (!defined('OTP_EXPIRED_TIME')) define('OTP_EXPIRED_TIME', 300);

/* PayPal REST API configuration 
 * You can generate API credentials from the PayPal developer panel. 
 * See your keys here: https://developer.paypal.com/dashboard/ 
 */
if (!defined('CURRENCY')) define('CURRENCY', 'USD');
if (!defined('PAYPAL_SANDBOX')) define('PAYPAL_SANDBOX', TRUE); //TRUE=Sandbox | FALSE=Production 
if (!defined('PAYPAL_SANDBOX_CLIENT_ID')) define('PAYPAL_SANDBOX_CLIENT_ID', '');
if (!defined('PAYPAL_SANDBOX_CLIENT_SECRET')) define('PAYPAL_SANDBOX_CLIENT_SECRET', '');
if (!defined('PAYPAL_PROD_CLIENT_ID')) define('PAYPAL_PROD_CLIENT_ID', 'Insert_Live_PayPal_Client_ID_Here');
if (!defined('PAYPAL_PROD_CLIENT_SECRET')) define('PAYPAL_PROD_CLIENT_SECRET', 'Insert_Live_PayPal_Secret_Key_Here');

/**
 * Define TABLES
 */
if (!defined('TABLES')) define('TABLES', [
  'CATEGORY' => 'category',
  'PRODUCT' => 'product',
  'ORDER' => 'order',
  'ORDER_DETAIL' => 'orderdetail',
  'ORDER_STATUS' => 'orderstatus',
  'ROLE' => 'role',
  'USER' => 'user',
  'CART' => 'cart',
  'CART_DETAIL' => 'cartdetail'
]);

/**
 * Define APIs
 */

// Product API
if (!defined('GET_PRODUCTS_API'))
  define('GET_PRODUCTS_API', APP_URL . '/admin/products/actions/get-products.php');

if (!defined('ADD_PRODUCT_API'))
  define('ADD_PRODUCT_API', APP_URL . '/admin/products/actions/add.php');

if (!defined('EDIT_PRODUCT_API'))
  define('EDIT_PRODUCT_API', APP_URL . '/admin/products/actions/update.php');

if (!defined('GET_PRODUCT_BY_ID_API'))
  define('GET_PRODUCT_BY_ID_API', APP_URL . '/admin/products/actions/get-by-id.php');

if (!defined('DELETE_PRODUCT_BY_ID_API'))
  define('DELETE_PRODUCT_BY_ID_API', APP_URL . '/admin/products/actions/delete-by-id.php');

if (!defined('DELETE_PRODUCT_BY_IDS_API'))
  define('DELETE_PRODUCT_BY_IDS_API', APP_URL . '/admin/products/actions/delete-by-ids.php');

// Category API
if (!defined('GET_CATEGORIES_API'))
  define('GET_CATEGORIES_API', APP_URL . '/admin/categories/actions/get-categories.php');

if (!defined('GET_CATEGORY_BY_ID_API'))
  define('GET_CATEGORY_BY_ID_API', APP_URL . '/admin/categories/actions/get-by-id.php');

if (!defined('ADD_CATEGORY_API'))
  define('ADD_CATEGORY_API', APP_URL . '/admin/categories/actions/add.php');

if (!defined('UPDATE_CATEGORY_API'))
  define('UPDATE_CATEGORY_API', APP_URL . '/admin/categories/actions/update.php');

if (!defined('DELETE_CATEGORY_BY_ID_API'))
  define('DELETE_CATEGORY_BY_ID_API', APP_URL . '/admin/categories/actions/delete-by-id.php');

if (!defined('DELETE_CATEGORY_BY_IDS_API'))
  define('DELETE_CATEGORY_BY_IDS_API', APP_URL . '/admin/categories/actions/delete-by-ids.php');

// Order APIs
if (!defined('GET_ORDER_BY_ID_API'))
  define('GET_ORDER_BY_ID_API', APP_URL . '/admin/orders/actions/get-by-id.php');

if (!defined('GET_ORDERS_BY_USER_ID_API'))
  define('GET_ORDERS_BY_USER_ID_API', APP_URL . '/admin/orders/actions/get-by-user-id.php');

if (!defined('GET_ORDERS_API'))
  define('GET_ORDERS_API', APP_URL . '/admin/orders/actions/get-orders.php');

if (!defined('UPDATE_ORDER_API'))
  define('UPDATE_ORDER_API', APP_URL . '/admin/orders/actions/update-order.php');

if (!defined('GET_PRODUCTS_OF_ORDER_API'))
  define('GET_PRODUCTS_OF_ORDER_API', APP_URL . '/admin/orders/actions/get-products-of-order.php');

if (!defined('UPDATE_ORDER_PRODUCT_API'))
  define('UPDATE_ORDER_PRODUCT_API', APP_URL . '/admin/orders/actions/update-order-product.php');

if (!defined('DELETE_ORDER_PRODUCT_API'))
  define('DELETE_ORDER_PRODUCT_API', APP_URL . '/admin/orders/actions/delete-order-product.php');

if (!defined('DELETE_ORDER_PRODUCTS_API'))
  define('DELETE_ORDER_PRODUCTS_API', APP_URL . '/admin/orders/actions/delete-order-products.php');

// Order Status APIs
if (!defined('GET_ORDER_STATUSES_API'))
  define('GET_ORDER_STATUSES_API', APP_URL . '/admin/order-statuses/actions/get-order-statuses.php');

// Customer APIs
if (!defined('GET_CUSTOMERS_API'))
  define('GET_CUSTOMERS_API', APP_URL . '/admin/customers/actions/get-customers.php');

// User APIs
if (!defined('GET_USERS_API'))
  define('GET_USERS_API', APP_URL . '/admin/users/actions/get-users.php');

if (!defined('UPDATE_USER_API'))
  define('UPDATE_USER_API', APP_URL . '/admin/users/actions/update-user.php');

if (!defined('DELETE_USER_API'))
  define('DELETE_USER_API', APP_URL . '/admin/users/actions/delete-by-id.php');
