<?php

class database{
    private $servername = "localhost";
    private $username = "";
    private $password = "";
    private $database = "admin_bootcamp";
    private $db;
    public $result;
    

   public function __construct(){
        $this->db = $this->connect();

        if ($this->db->connect_error) {
           $data = "Connection failed: " . $this->db->connect_error;
            respond($data);
        } 
    }
    public function getInsertedId(){

        return $this->db->insert_id;
    }
   private function connect(){
        
        // Create database connection
        return new mysqli($this->servername, $this->username, $this->password, $this->database);
    }

   public function disconnect(){
         // disconnect daatbase connection
        return mysqli_close($this->db);
    }

    public function query($sql){
        
        $this->result = $this->db->query($sql);

    }

    public function getResults(){
        if ($this->result->num_rows > 0) {
            $data = array();
            while($row = $this->result->fetch_assoc()) {
                $data[] = $row;
            }
           
            return $data;

        } else {
           
           return 0;
        }
    }

    public function clean($string){

        return $this->db->real_escape_string($string);

    }

}