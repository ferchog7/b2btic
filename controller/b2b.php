<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'connect.php';
class B2b{
    private $wsdl_url;
    private $wsdl_options;
    public $conn;

    function __construct(){
        require_once '../config/webservice.php';
        $connect = new Connect();
        $this->wsdl_url = $wsdl;
        $this->wsdl_options = $options;
        $this->conn = $connect->connect_db();
    }

    public function updateFiles($type, $value){
        $params = Array(
            "Condiciones" => Array( 
                "Condicion" => Array(
                    "Tipo" => $type,
                    "Expresion" => $value,
                )
            )
        );
        $soap = new SoapClient($this->wsdl_url, $this->wsdl_options);
        $soap->__setLocation('http://test.analitica.com.co/AZDigital_Pruebas/WebServices/SOAP/index.php');
        $result = $soap->BuscarArchivo($params);
        foreach($result->Archivo as $value_file){
            $this->saveFiles($value_file, $this->getType($value_file->Nombre));
        }
    }

    private function getType($fileName){
        $result = 'None';
        $type = explode('.', $fileName);
        if(count($type) > 1){
            $result = $type[count($type)-1];
        }
        return $result;
    }

    private function saveFiles($file, $type){
        $fileName = $this->conn->real_escape_string($file->Nombre);
        $fileType = $this->conn->real_escape_string($type);
        $sql_file = "INSERT INTO files(remote_id, name) VALUES($file->Id, '".$fileName."')";

        if($this->conn->query($sql_file) === TRUE){
            $last_id = $this->conn->insert_id;
            $sql_type = "INSERT INTO file_type(file_id, type) VALUES($last_id, '".$fileType."')";
            if($this->conn->query($sql_type) === FALSE){
                echo "<div style='color:red'>Error guardando la extensión del archivo: " . $fileName . "<br>" . $this->conn->error. "</div>";
            }
        }else{
            echo "<div style='color:red'>Error guardando el archivo: " . $fileName . "<br>" . $this->conn->error. "</div>"; 
        }
    }
}

$b2b = new B2b();
$archivos = $b2b->updateFiles('FechaInicial', '2019-07-01 00:00:00');