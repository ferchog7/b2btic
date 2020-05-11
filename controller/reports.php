<?php
include('connect.php');
class Report{
    public $conn;

    function __construct(){
        $connect = new Connect();
        $this->conn = $connect->connect_db();
    }

    private function reportAllFiles(){
        $sql_files = "SELECT F.id, F.name, T.type 
            FROM files as F 
            INNER JOIN file_type as T 
            ON F.id = T.file_id";
        $result = $this->conn->query($sql_files);
        $headers = array('Id', 'Nombre', 'Tipo');
        $values = array();
        while($row = $result->fetch_row()){
            $values[] = array($row[0], $row[1], $row[2]);
        }
        return $this->printTable($headers, $values);
    }

    private function reportFilesByType(){
        $sql_files = "SELECT COUNT(F.id) as Total, T.type 
            FROM files as F 
            INNER JOIN file_type as T 
            ON F.id = T.file_id
            GROUP BY T.type
            ORDER BY Total";
        $result = $this->conn->query($sql_files);
        $headers = array('Tipo', 'Total');
        $values = array();
        while($row = $result->fetch_row()){
            $values[] = array($row[1], $row[0]);
        }
        return $this->printTable($headers, $values);
    }

    public function getAllReports(){
        $table = "<table align='center' width='100%' border='0'><tr><td>";
        $table .= $this->reportAllFiles();
        $table .= "</td><td valign='top'>";
        $table .= $this->reportFilesByType();
        $table .= "</td></tr></table>";
        return $table;
    }

    public function printTable($headers, $values){
        $table = "<table align='center' border='1' width='100%'>";
        if($headers){
            $table .= "<tr style='background:lightgray'>";
            foreach($headers as $header){
                $table .= "<td>".$header."</td>";
            }
            $table .= "</tr>";
        }
        if($values){
            foreach($values as $value){
                $table .= "<tr>";
                    foreach($value as $row){
                        $table .= "<td>".$row."</td>";
                    }
                $table .= "</tr>";
            }
        }
        $table .= "</table>";
        return $table;
    }
}

$report = new Report();
echo $report->getAllReports();