<?php
header('Access-Control-Allow-Origin: *');
Class WorkstreamGateway{
    private PDO $conn;

    public function __construct(Database $database){
        $this->conn = $database ->getConnection();
    }

    public function getAll():array{
        $sql = "select * FROM workstream";

        $stmt = $this->conn->query($sql);

        $data = [];

        while($row = $stmt-> fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }
        return $data;

    }

    public function create(array $data) : string{

        $sql = "INSERT INTO workstream (name, team, required_staffing, description, start_date) 
        VALUES (:name, :team, :required_staffing, :description, :start_date)";


        $stmt = $this ->conn->prepare($sql);

// Bind the parameters to the prepared statement
    $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
    $stmt->bindParam(':team', $data['team'], PDO::PARAM_INT);
    $stmt->bindParam(':required_staffing', $data['required_staffing'], PDO::PARAM_INT);
    $stmt->bindParam(':description', $data['description'], PDO::PARAM_STR);
    $stmt->bindParam(':start_date', $data['date'], PDO::PARAM_STR);

        // $stmt ->execute(); 
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            // Handle the error, display it, or log it
            die("Error: " . $errorInfo[2]);
        } else {
             //Success message or other actions after successful insert
             return $this->conn->lastInsertId();
        }
        
    }

    public function get(string $id,?string $modifier) : array | false
    {
        $sql = "SELECT * FROM workstream WHERE workstream_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
        var_dump($stmt);
        $stmt ->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE workstream 
        SET name = :name, location = :location, required_staffing = :required_staffing, current_staffing = :current_staffing
        WHERE workstream_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":name", $new["name"] ?? $current["name"],PDO::PARAM_STR);
        $stmt ->bindValue(":location", $new["location"] ?? $current["location"],PDO::PARAM_INT);
        $stmt ->bindValue(":required_staffing", $new["required_staffing"] ?? $current["required_staffing"],PDO::PARAM_INT);
        $stmt ->bindValue(":current_staffing", $new["current_staffing"] ?? $current["current_staffing"],PDO::PARAM_INT);
        $stmt ->bindValue(":id",$current["workstream_id"], PDO::PARAM_INT);
        $stmt -> execute();

        return $stmt-> rowCount();
        
    }
    public function delete(string $id): int
    {
        $sql = "DELETE FROM workstream
                WHERE workstream_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt -> bindValue(":id", $id,PDO::PARAM_INT);
        $stmt -> execute();

        return $stmt -> rowCount();







    }
}