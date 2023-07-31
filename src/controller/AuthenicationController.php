<?php

class AuthenicationController extends controller{





public function getAccessToken ($secretKey) {

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    
        http_response_code(405);
        header("Allow: POST");
        exit;
    }

    $data = (array) json_decode(file_get_contents("php://input"), true);

    if ( ! array_key_exists("username", $data) ||
     ! array_key_exists("password", $data)) {

    http_response_code(400);
    echo json_encode(["message" => "missing login credentials"]);
    exit;
    }

    $user = $this->gateway ->getByUsername($data["username"]);

    if ($user === false) {
    
        http_response_code(401);
        echo json_encode(["message" => "invalid authentication"]);
        exit;
    }
    if ( !password_verify($data["password"], $user["password_hash"])) {
    
        http_response_code(401);
        echo json_encode(["message" => "invalid authentication"]);
        exit;
    }

    
    $userDetails = $this->gateway ->getUserDetails($user["staff_id"]); 
    $payload = [
        "sub" => $userDetails["staff_ID"],
        "name" => $userDetails["name"],
        "exp" => time() + 20
    ];
    
    $codec = new JWTCodec($secretKey);
    $access_token = $codec->encode($payload);
    
    echo json_encode([
        "access_token" => $access_token
    ]);




}















}







