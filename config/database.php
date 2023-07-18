<?php

Class Database
{
    public function __construct(private string $host,private string $name,private string $user,private string $password)
    {}

    public function getConnection(): PDO

    {
        echo json_encode([
            
            "host" => $this -> host,
            "name" => $this -> name,
            "user" => $this -> user,
            "password" => $this -> password,
        ]);

        $dsn = "mysql:host ={$this -> host};dbname={$this->name};charset =utf8";
        return new PDO ($dsn, $this->user, $this ->password, 
            [PDO::ATTR_EMULATE_PREPARES =>false,
             PDO::ATTR_STRINGIFY_FETCHES =>false]);

    }
}

