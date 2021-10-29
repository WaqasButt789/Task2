<?php

require "Database.php";
require "validation.php";


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

    public $data;
    public $parameter;

    function get_data()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        $this->email=$data["merchant_email"];
        $user_name = $data['user_name'];
        $user_email	 = $data['user_email']; 
        $user_password = $data['user_password'];   
        $email_permission = $data['email_permission'];
        $list_view_permission= $data['list_view_permission'];
        $payment_permission= $data['payment_permission'];
        $token=$data['token'];
        $parameter = array($user_name,$user_email,$user_password,$email_permission,$list_view_permission,$payment_permission,$this->merchant_id);
        return $parameter;
    } 

    public function getkey()
    {
        $data=json_decode(file_get_contents("php://input"),true);
        $k=$data["token"];
          return $k;
    }

    public function get_token($data)
	{
         $key= $data;
         echo $key;
		 if(!empty($key))
		 {
			if(self::check_token($key))
			{
				return true;
			}
		 }
		 else
		 {
			echo json_encode(array('Message'=>'you are not login','status'=>"404"));
			return false;
		 }
	}

    public function validating_data($parameter)
    {
        $v=new Validate();
        $check_all = true;

        if(!$v->email_validate($parameter[1]))          { $check_all[0]=false; }            // validating email                                                   
        if(!$v->password_validate($parameter[2]))       { $check_all[0]=false; }            // validating password                                                            
        if(!$v->name_validate($parameter[0]))           { $check_all[0]=false; }            // validating name
        
        return $check_all;


    }
    
    
 


    function check_empty($parameter)
    {
        if((empty($parameter[0])) || (empty($parameter[1])) || (empty($parameter[2])) || (empty($parameter[3])) || (empty($parameter[4]) || (empty($parameter[5])))) 
        {
            echo json_encode(array('Message'=>'Please Enter All Fields :','status'=>false));
            return false;
        }
        else{
            return true;
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
$p=$Add->getkey();
$vali = $Add->get_data();
if($Add->get_token($p)){
if($Add->check_empty($vali))
{
    if($Add->validating_data($vali)){
         
            $Add->insert_in($vali);
    }
}
}
else{

    echo json_encode(array("Message"=>"you are not allowed to add user because you are not login!!!"));
}

?>