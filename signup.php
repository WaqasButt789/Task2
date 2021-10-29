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

       
       
       
        public function validating_data($data)
        {
            $v=new Validate();
            $check_all = array(true,true);

            if(!$v->email_validate($data["merchant_email"]))        { $check_all[0]=false; }            // validating email                                                   
            if(!$v->password_validate($data["merchant_password"]))  { $check_all[0]=false; }            // validating password                                                            
            if(!$v->name_validate($data["merchant_name"]))          { $check_all[0]=false; }            // validating name
            //validating card details
            if(!$v->card_no_validate($data["card_no"]))     { $check_all[0]=false; }                    // validating card number
            if(!$v->cvc_validate($data["cvc"]))             { $check_all[0]=false; }                    // validating cvc number
            if(!$v->credit_validate($data["credit"]))       { $check_all[0]=false; }                   // validating credit 
            //if(!$v->date_validate($data["valid_through"]))  { $check_all[0]=false; }                 // validating valid through 
            //if(!$v->date_validate($data["vlid_from"]))      { $check_all[0]=false; }                 // validating valid from     
            
            if($data["merchant_email"]=="")      { $check_all[1]=false; }            // validating email                                                   
            if($data["merchant_password"]=="")   { $check_all[1]=false; }            // validating password                                                            
            if($data["merchant_name"]=="")       { $check_all[1]=false; }            // validating name
            //validating card details
            if($data["card_no"]=="")         { $check_all[1]=false; }                 // validating card number
            if($data["cvc"]=="")             { $check_all[1]=false; }                 // validating cvc number
            if($data["credit"]=="")          { $check_all[1]=false; }                 // validating credit 
            if($data["valid_through"]=="")   { $check_all[1]=false; }                 // validating valid through 
            if($data["valid_from"]=="")       { $check_all[1]=false; }
            
            return $check_all;


        }

        

        function insert_data_in_merchants($data)
        {
            
                return self::insert_in_merchants($data);  //insert data in merchant
            
        }

        function insert_data_in_cards($data, $merchant_id)
        {
           
           
                self::insert_in_cards($data, $merchant_id);  //insert data of card

            
            
        }

    }

    $obj = new signup();  
    $p=$obj->get_data();
    $res=$obj->validating_data($p);
    
                          

    if($res[0]==true && $res[1]==true)
    {
        if(!$obj->search_employ_by_email("merchants",$p["merchant_email"]) && !$obj->search_card_by_card_no("cards",$p["card_no"])) //checking whether merchant already exists or not
        {
            $merchant_id = $obj->insert_data_in_merchants($p);
            $obj->insert_data_in_cards($p, $merchant_id);
            echo json_encode(array('Message'=>'SignUp Successfully  :','status'=>"201"));  //status code 201 because user data added successfuly

        }
        
        else{
            echo json_encode(array('Message'=>'Merchant or card Already Exist With This Email','status'=>"409"));  //status code 409 because user data added successfuly

        }

    }
    else if($res[0]==false && $res[1]==true){
    echo json_encode(array('Message'=>'Please Enter Valid Data','status'=>"409"));  //status code 409 because user data added successfuly
    }
    else if($res[0]==true && $res[1]==false){
        echo json_encode(array('Message'=>'Please Enter All the Fields','status'=>"409"));  //status code 409 because user data added successfuly

    }
    else if($res[0]==false && $res[1]==false){
        echo json_encode(array('Message'=>'Please Enter All the Fields and provide valid data','status'=>"409"));  //status code 409 because user data added successfuly
    }
    

?>