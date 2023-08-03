<?php
Class scheduleGateway{
    private PDO $conn;

    public function __construct(Database $database){
        $this->conn = $database ->getConnection();
    }

    public function getAll():array{
        $sql = "SELECT
        sw.Scheduled_work_id,
        sw.date,
        sw.workstream,
        s1.staff_id AS staff_id,
        CONCAT(s1.first_name, ' ', s1.last_name) AS staff_name,
        s2.staff_id AS assigner_id,
        CONCAT(s2.first_name, ' ', s2.last_name) AS assigner_name
    FROM
        scheduled_work sw
    INNER JOIN
        staff s1 ON sw.staff_member = s1.staff_id
    INNER JOIN
        staff s2 ON sw.assigned_by = s2.staff_id;";

        $stmt = $this->conn->query($sql);

        $data = [];

        while($row = $stmt-> fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }
        return $data;

    }

    public function create(array $data) : string{

        $sql = "INSERT INTO scheduled_work(staff_member,date,assigned_by,workstream)
                VALUES (:staff_member, :date,:assigned_by,:workstream)";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":staff_member", $data["staff_member"], PDO::PARAM_STR);
        $stmt ->bindValue(":date",$data["date"], PDO::PARAM_INT);
        $stmt ->bindValue(":assigned_by",$data["assigned_by"], PDO::PARAM_INT);
        $stmt ->bindValue(":workstream",$data["workstream"], PDO::PARAM_INT);
        $stmt ->execute(); 
        return $this->conn->lastInsertId();
    }
    
    
    
    
    public function get(string $id, ?string $modifier):array | false
    {
    
        switch($modifier){
                case'staff':
                    $sql = "SELECT
                    sw.Scheduled_work_id,
                    sw.date,
                    s.staff_id,
                    CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
                    CONCAT(s2.first_name, ' ', s2.last_name) AS assigner_name,
                    ws.name AS workstream_name
                FROM
                    scheduled_work sw
                INNER JOIN
                    staff s ON sw.staff_member = s.staff_id
                INNER JOIN
                     staff s2 ON sw.assigned_by = s2.staff_id
                INNER JOIN
                    workstream ws ON sw.workstream = ws.workstream_id
                WHERE staff_member = :id;";
                break;
                case'assignment':
                    $sql = "SELECT
                    sw.Scheduled_work_id,
                    sw.date,
                    s.staff_id,
                    CONCAT(s.first_name, ' ', s.last_name) AS staff_name,
                    CONCAT(s2.first_name, ' ', s2.last_name) AS assigner_name,
                    ws.name AS workstream_name
                FROM
                    scheduled_work sw
                INNER JOIN
                    staff s ON sw.staff_member = s.staff_id
                INNER JOIN
                     staff s2 ON sw.assigned_by = s2.staff_id
                INNER JOIN
                    workstream ws ON sw.workstream = ws.workstream_id
                WHERE Scheduled_work_id = :id;";
                break;
                default:
                $sql = null;
        }   

        $stmt = $this->conn->prepare($sql);
        $stmt -> bindValue(":id", $id, PDO::PARAM_INT);
        $stmt ->execute();
        $data = [];

        while($row = $stmt-> fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }
        return $data;

    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE scheduled_work
        SET staff_member = :staff_member, start_date= :start_date,end_date= :end_date,assigned_by= :assigned_by,workstream= :workstream,
        WHERE scheduled_work_id= :id";
        $stmt = $this->conn->prepare($sql);
        $stmt ->bindValue(":staff_member", $new["staff_member"] ?? $current["staff_member"],PDO::PARAM_INT);
        $stmt ->bindValue(":start_date", $new["start_date"] ?? $current["start_date"],PDO::PARAM_STR);
        $stmt ->bindValue(":end_date",$current["end_date"], PDO::PARAM_STR);
        $stmt ->bindValue(":assigned_by", $new["assigned_by"] ?? $current["assigned_by"],PDO::PARAM_INT);
        $stmt ->bindValue(":workstream",$current["workstream"], PDO::PARAM_INT);
        $stmt -> execute();

        return $stmt-> rowCount();
        
    }
    public function delete(string $id): int
    {
        $sql = "DELETE FROM scheduled_work
                WHERE  scheduled_work_id= :id";

        $stmt = $this->conn->prepare($sql);
        $stmt -> bindValue(":id", $id,PDO::PARAM_INT);
        $stmt -> execute();

        return $stmt -> rowCount();







    }
}

