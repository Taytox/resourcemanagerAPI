<?php
Class TeamMembershipGateway{
    private PDO $conn;

    public function __construct(Database $database){
        $this->conn = $database ->getConnection();
    }

    public function getAll():array{
        $sql = "SELECT * FROM team_membership";

        $stmt = $this->conn->query($sql);

        $data = [];

        while($row = $stmt-> fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }
        return $data;

    }

    // public function create(array $data) : string{

    //     // $sql = "INSERT INTO teams(name,location)
    //     //         VALUES (:name, :location)";


    //     // $stmt = $this->conn->prepare($sql);
    //     // $stmt ->bindValue(":name", $data["name"], PDO::PARAM_STR);
    //     // $stmt ->bindValue(":location",$data["location"], PDO::PARAM_INT);
    //     // $stmt ->execute(); 
    //     // return $this->conn->lastInsertId();
    // }

    public function get(string $id,?string $modifier) : array | false
    {
        $sql = "SELECT
            teams.teams_id,
            teams.name
            FROM
            team_membership
            JOIN
            teams ON team_membership.team = teams.teams_id
            WHERE
        team_membership.staff_member = :id;";
        $stmt = $this->conn->prepare($sql);
        $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
        $stmt ->execute();
        
        while($row = $stmt-> fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }
        return $data;
    }

    // public function update(array $current, array $new): int
    // {
    //     // $sql = "UPDATE teams
    //     // SET name = :name, location = :location
    //     // WHERE teams_id = :id";
    //     // $stmt = $this->conn->prepare($sql);
    //     // $stmt ->bindValue(":name", $new["name"] ?? $current["name"],PDO::PARAM_STR);
    //     // $stmt ->bindValue(":location", $new["location"] ?? $current["location"],PDO::PARAM_INT);
    //     // $stmt ->bindValue(":id",$current["teams_id"], PDO::PARAM_INT);
    //     // $stmt -> execute();

    //     // return $stmt-> rowCount();
        
    // }
    // public function delete(string $id): int
    // {
    //     $sql = "DELETE FROM teams
    //             WHERE teams_id = :id";

    //     $stmt = $this->conn->prepare($sql);
    //     $stmt -> bindValue(":id", $id,PDO::PARAM_INT);
    //     $stmt -> execute();

    //     return $stmt -> rowCount();







    // }
}