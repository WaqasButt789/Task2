<?php

require "Database.php";
require "validation.php";
include 'jwthandler.php';

header('Content-Type:application/json');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:Access-Control-Allow-Headers,Content_Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');
class AddUser extends Database{
    
    private $user_name;
    private $user_email;
    private $email_permission;
    private $list_view_permission;
    private $merchant_id;
    private $payment_permission;
    public $email;

    private $data;
    private $parameter;

    function get_data()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        $this->email=$data["merchant_email"];
        $user_name = $data['user_name'];
        $user_email	 = $data['user_email'];    
        $email_permission = $data['email_permission'];
        $list_view_permission= $data['list_view_permission'];
        $payment_permission= $data['payment_permission'];
        $parameter = array($user_name,$user_email,$email_permission,$list_view_permission,$payment_permission,$this->merchant_id);
        return $parameter;
    }

    
    /*function validation($parameter)
    {
       $flag=true;
       $validate= new Validate();                                                   
       if(!$validate->cnic_validate($parameter[5]))  { $flag=false; }   // validating cnic                                                   
       if(!$validate->name_validate($parameter[0]))  { $flag=false; }   // validating name                                                            
       if(!$validate->phone_validate($parameter[1]))  { $flag=false; }  // validating phone
       if(!$validate->email_validate($parameter[6]))  { $flag=false; }   // validating email                                           
       if(!$validate->dep_validate($parameter[3]))  { $flag=false; }  // validating department
       return $flag;
    }*/

 


    function check_empty($parameter)
    {
        if((empty($parameter[0])) || (empty($parameter[1])) || (empty($parameter[2])) || (empty($parameter[3])) || (empty($parameter[4]))) 
        {
            echo json_encode(array('Message'=>'Please Enter All Fields :','status'=>false));
        }
        else
        {
            self::insert_in($parameter);
        }

    }
    function insert_in($parameter)
    {
      
       
        if(($parameter))
        { 
            
            self::insert_in_user("secondaryusers",$parameter,$this->email);
            echo json_encode(array('Message'=>'Updated Successfully :','status'=>true));
        }
        else{

            echo json_encode(array('Message'=>'Please Enter valid Data :','status'=>false));
        }
    }
}
$Add= new AddUser();
$vali = $Add->get_data();
$Add->check_empty($vali);
?>