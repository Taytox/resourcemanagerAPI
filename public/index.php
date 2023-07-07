<?php   

declare(strict_types=1);



#Autoload required controller classes 
spl_autoload_register(function ($class){

require dirname(__DIR__,1) . "/src/controller/$class.php";

});




$parts = explode("/",$_SERVER["REQUEST_URI"]);

print_r($parts);


if ($parts[1] != "workstreams"){
    http_response_code(404);
}


$id = $parts[2] ?? null;
var_dump($id);



$controller = new workstreamController;
$controller -> processRequest($_SERVER["REQUEST_METHOD"],$id);