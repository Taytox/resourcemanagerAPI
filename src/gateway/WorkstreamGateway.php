<?php
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

        $sql = "INSERT INTO workstream(name,location,required_staffing,current_staffing)
                VALUES (:name, :location, :required_staffing, :current_staffing)";


        $stmt = $this->conn->prepare($sql);
        echo "Name is" . $data["name"],
        $stmt ->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt ->bindValue(":location",$data["location"], PDO::PARAM_INT);
        $stmt ->bindValue(":required_staffing",$data["reqstaffing"], PDO::PARAM_INT);
        $stmt ->bindValue(":current_staffing",$data["curstaffing"] ?? 0, PDO::PARAM_INT);

        $stmt ->execute(); 
        return $this->conn->lastInsertId();
    }

}