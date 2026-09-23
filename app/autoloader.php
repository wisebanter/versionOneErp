<?php

// declare(strict_types=1);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);



try {
    $baseUri = 'http' . ((!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/';
    $baseUri .= 'hibah/';

    define('BASEURL', $baseUri);
    define('DATABASE_HOST', 'localhost');
    define('DATABASE_USER', 'root');
    define('DATABASE_PASS', '');
    define('DATABASE_NAME', 'erp_api');
    define('DATABASE_PORT', 0);

    define('TOKEN_SECRET', 'Dexter@2023Api');
    define('TOKEN_EXPIRY', 3600);
    define('TOKEN_CAN_EXPIRE', 'F');

    // define('SESSION_KEY', 'inventory');


    spl_autoload_register(function ($class) {
        $filename = $class . '.php';
        // $baseDir = (__DIR__) . '/';
        $path = "";
        foreach (array('cores', 'libraries', 'models') as $dir) {
            $file = (__DIR__) . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR .  $filename;

            if (file_exists($file)) {
                $path = $file;
                break;
            }
        }

        if (!empty($path)) {
            require_once $path;
        }
    });

} catch (Exception $ex) {


    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: POST, GET, PUT, PATCH');
    header('Access-Control-Allow-Headers: Content-Type');
    header("Content-Type: application/json; charset=UTF-8");


    $result = array('status' => false, 'statusCode' => 500, 'messageCode' => 300,  'message' => $ex->getMessage());
    http_response_code($result['statusCode']);
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}
