<?php   
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");


#if local dev enviroment, load config.php otherwise use environment vairables set in heroku. 
if(is_file(dirname(__DIR__,1) . "/config/config.php")){
    require dirname(__DIR__,1) . "/config/config.php";
}else{
    $config=array( 
        'DB_HOST'=> $_ENV["DB_HOST"],
        'DB_USERNAME'=> $_ENV["DB_USERNAME"],
        'DB_PASSWORD'=> $_ENV["DB_PASSWORD"],
        'DB_DATABASE'=>$_ENV["DB_DATABASE"],
        'SECRET_KEY'=>"5A7134743777217A25432646294A404E635266556A586E3272357538782F413F"
    );
}
require dirname(__DIR__,1) . "/src/InvalidSignatureException.php"; 
require dirname(__DIR__,1) . "/src/authentication\JWTCodec.php";
require dirname(__DIR__,1) . "/src/authentication\auth.php";
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

$Usergateway = new UserGateway($database);
$codec = new JWTCodec($config["SECRET_KEY"]);
$auth = new Auth($Usergateway,$codec);



#if there is a 3rd part to the URL then part 3 is the ID and part 2 is a modifier

if(isset($parts[3]) === false && empty($parts[3]) === true){
$id = $parts[2] ?? null;
$modifier = null;
}else{
    $id = $parts[3];
    $modifier = $parts[2];
}

switch($parts[1]){
    case'workstreams':
        if(! $auth -> authenticateAccessToken()){
            exit;
        }
        $gateway = new WorkstreamGateway($database);
        $controller = new WorkStreamController($gateway,"Workstream");
        $controller -> processRequest($_SERVER["REQUEST_METHOD"],$id,$modifier);
        break;
    case'teams':
        if(! $auth -> authenticateAccessToken()){
            exit;
        }
        $gateway = new TeamGateway($database);
        $controller = new TeamController($gateway,"team");
        $controller -> processRequest($_SERVER["REQUEST_METHOD"],$id,$modifier);
        break;
    case'schedules':
        if(! $auth -> authenticateAccessToken()){
            exit;
        }
        $gateway = new scheduleGateway($database);
        $controller = new scheduleController($gateway,"schedule");
        $controller -> processRequest($_SERVER["REQUEST_METHOD"],$id,$modifier);
        break;
        case'teammembership':
            if(! $auth -> authenticateAccessToken()){
                exit;
            }
            $gateway = new TeamMembershipGateway($database);
            $controller = new TeamMembershipController($gateway,"membership");
            $controller -> processRequest($_SERVER["REQUEST_METHOD"],$id,$modifier);
            break;
        case 'login':
            $controller = new AuthenicationController($Usergateway,"");
            $controller -> getAccessToken ($config["SECRET_KEY"]) ;
            break;
    default:
        http_response_code(404);
        break;

}






