<?php
class ErrorHandler
{

    public static function handleException(Throwable $exception)
    { 
        http_response_code(500);  
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }


    public static function handleError(int $errno, string $errstr,string $errfile,int $errline) : bool
    {


        #Throw an exception with the error, will be caught by exception handler \ halt processing. 
        throw new ErrorException($errstr,0,$errno,$errfile,$errline);         
    }
}