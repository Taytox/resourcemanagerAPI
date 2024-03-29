<?php

class UserGateway
{
    private PDO $conn;
    
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }
    
    
    public function getByUsername(string $username): array | false
    {
         $sql = "SELECT *
                FROM user
                 WHERE username = :username";

                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getUserDetails(string $id):array | false
    {
        $sql = "SELECT staff_ID, first_name, last_name AS name
        FROM staff 
        WHERE staff_id = :id";

        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(":id", $id, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}