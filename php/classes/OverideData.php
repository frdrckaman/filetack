<?php
class OverideData{
    private $_pdo;
    function __construct(){
        try {
            $this->_pdo = new PDO('mysql:host='.config::get('mysql/host').';dbname='.config::get('mysql/db'),config::get('mysql/username'),config::get('mysql/password'));
        }catch (PDOException $e){
            $e->getMessage();
        }
    }
   public function unique($table,$field,$value){
        if($this->get($table,$field,$value)){
            return true;
        }else{
            return false;
        }
    }

    public function getNo($table){
        $query = $this->_pdo->query("SELECT * FROM $table");
        $num = $query->rowCount();
        return $num;
    }
    public function getCount($table,$field,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $num = $query->rowCount();
        return $num;
    }
    public function countData($table,$field,$value,$field1,$value1){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }
    public function getData($table){
        $query = $this->_pdo->query("SELECT * FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNews($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function get3($table,$where,$id,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSumD($table,$variable){
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSumD1($table,$variable, $field, $value){
        $query = $this->_pdo->query("SELECT SUM($variable) FROM $table WHERE $field = '$value' ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function get($table,$where,$id){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function delete($table,$field,$value){
        return $this->_pdo->query("DELETE FROM $table WHERE $field = $value");
    }

}