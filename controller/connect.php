<?php
class Connect{
    public $host;
    public $user;
    public $pass;
    public $db;

    function __construct(){
        require_once('../config/db.php');
        $this->host = $host_dev;
        $this->user = $user_dev;
        $this->pass = $pass_dev;
        $this->db = $db_dev;
    }

    public function connect_db(){
        $link = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($link->connect_error){
            printf("Connect failed: %s\n", $link->connect_error);
            exit();
        }
        return $link;
    }
}