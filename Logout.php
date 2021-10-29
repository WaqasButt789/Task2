<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require "Database.php";

class LogOut extends Database{


    public $key;

    public function get_data()
    {
      $data=json_decode(file_get_contents("php://input"),true);
     
      
      $key=$data["token"];
      
      return $key;

      

      
    }

    public function logout($key)
    {
       self::logout_by_key($key);
    }
}

$obj=new LogOut();
$k=$obj->get_data();
$obj->logout($k);


?>