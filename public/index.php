<?php   

declare(strict_types=1);
require dirname(__DIR__,1) . "/config/config.php";
require dirname(__DIR__,1) . "/config/database.php";
require dirname(__DIR__,1) . "/src/ErrorHandler.php";

#Autoload required controller classes 
spl_autoload_register(function ($class){

require dirname(__DIR__,1) . "/src/controller/$class.php";

});



set_exception_handler("ErrorHandler::handleException");

#Set the content type returned by the API to JSON
header("Content-type:application/json; charset=UTF-8");


$parts = explode("/",$_SERVER["REQUEST_URI"]);

if ($parts[1] != "workstreams"){
    http_response_code(404);
}


$id = $parts[2] ?? null;

$database = new database($config['DB_HOST'],$config['DB_DATABASE'],$config['DB_USERNAME'],$config['DB_PASSWORD']);
$database->getConnection();

$controller = new workstreamController;
$controller -> processRequest($_SERVER["REQUEST_METHOD"],$id);