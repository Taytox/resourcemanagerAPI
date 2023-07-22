<?php   
declare(strict_types=1);

#if local dev enviroment, load config.php otherwise use environment vairables set in heroku. 
if(is_file(dirname(__DIR__,1) . "/config/config.php")){
    require dirname(__DIR__,1) . "/config/config.php";
}else{
    $config=array( 
        'DB_HOST'=> $_ENV["DB_HOST"],
        'DB_USERNAME'=> $_ENV["DB_USERNAME"],
        'DB_PASSWORD'=> $_ENV["DB_PASSWORD"],
        'DB_DATABASE'=>$_ENV["DB_DATABASE"]
    );
}


require dirname(__DIR__,1) . "/src/includes/headers.php"; 
require dirname(__DIR__,1) . "/config/database.php";
require dirname(__DIR__,1) . "/src/ErrorHandler.php";    
require dirname(__DIR__,1) . "/src/controller/controller.php";
#Autoload required  gateway and controller classes 
spl_autoload_register(function ($class){

#Determine the correct folder to load the class from based on its name. 
if(strpos($class, "Controller")!==false){
    require dirname(__DIR__,1) . "/src/controller/$class.php";
} elseif (strpos($class, "Gateway")!==false){
    require dirname(__DIR__,1) . "/src/gateway/$class.php";
}
});


set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

#Set the content type returned by the API to JSON
header("Content-type:application/json; charset=UTF-8");


$parts = explode("/",$_SERVER["REQUEST_URI"]);
$database = new database($config['DB_HOST'],$config['DB_DATABASE'],$config['DB_USERNAME'],$config['DB_PASSWORD']);
$database->getConnection();

$id = $parts[2] ?? null;

switch($parts[1]){
    case'workstreams':
        $gateway = new WorkstreamGateway($database);
        $controller = new WorkStreamController($gateway,"Workstream");
        $controller -> processRequest($_SERVER["REQUEST_METHOD"],$id);
        break;
    case'teams':
        $gateway = new TeamGateway($database);
        $controller = new TeamController($gateway,"team");
        $controller -> processRequest($_SERVER["REQUEST_METHOD"],$id);
        break;
    case'schedules':
        $gateway = new scheduleGateway($database);
        $controller = new scheduleController($gateway,"schedule");
        $controller -> processRequest($_SERVER["REQUEST_METHOD"],$id);
        break;
    default:
        http_response_code(404);
        break;

}








