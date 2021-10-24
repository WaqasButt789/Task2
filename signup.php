<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    header("Content-Type: Application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Methods: post");
    header('Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Methods,Access-Control-Allow-Headers,Authorization,X-Requested-With');

    require "validation.php";
    require "Database.php";
    
    class signup extends Database{

        public $table_name;
        public $data;

        public function get_data(){

            $data=json_decode(file_get_contents("php://input"),true);   //decde input request parameters and store them in an array.
            return $data;

        }

        function insert_data_in_merchants($data)
        {
            if(self::search_employ_by_email("merchants",$data["merchant_email"])) //checking whether merchant already exists or not
            {
                echo json_encode(array('Message'=>'Merchant Already Exist With This Email','status'=>"409"));  //status code 409 because user data added successfuly
            }
            else{
            return self::insert_in_merchants($data);  //insert data in merchant
            }
        }

        function insert_data_in_cards($data, $merchant_id)
        {
            if(self::search_card_by_card_no("cards",$data["card_no"])) //checking whether card already exists or not
            {
                echo json_encode(array('Message'=>'Card Already Exists','status'=>"409"));  //status code 409 because user data added successfuly
            }
            else{
                self::insert_in_cards($data, $merchant_id);  //insert data of card
                echo json_encode(array('Message'=>'SignUp Successfully  :','status'=>"201"));  //status code 201 because user data added successfuly
            }
            
        }

    }

    $obj = new signup();  
    $p=$obj->get_data();
    $merchant_id = $obj->insert_data_in_merchants($p);
    
    $obj->insert_data_in_cards($p, $merchant_id);


?>