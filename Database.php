<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "vendor1/stripe/stripe-php/init.php";
include 'jwthandler.php';

class Database{

    public function build_connection(){     //build sql database connection 
        $conn = new mysqli("localhost","root","","email_service");
        if ($conn->connect_error){
            echo "Database Connection Error";
        }
        else{
            return $conn;
        }
        
    }
    public function close_connection($conn){   //close database connection
        $conn->close();
    }

    public function getStripeToke($data){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_URL => 'https://api.stripe.com/v1/tokens',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer sk_test_51Jp3VHFK1zNyDuDTPsSp0QCzvlkXvtGWL76ML3FHxYHuK1ruEgw8AkevnA0Zi36ZkX0eldBYKFx2V6yEHU4gIJ3k00zfk1ETa2',
                'Content-type: application/x-www-form-urlencoded',
            ]
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function charge($token,$amount){
        $stripe = new \Stripe\StripeClient(
            'sk_test_51Jp3VHFK1zNyDuDTPsSp0QCzvlkXvtGWL76ML3FHxYHuK1ruEgw8AkevnA0Zi36ZkX0eldBYKFx2V6yEHU4gIJ3k00zfk1ETa2'
        );
        $stripe->charges->create([
            'amount' => $amount,
            'currency' => 'usd',
            'source' => $token,
            'description' => 'balance top up',
        ]);

        return $stripe;
    }
    /**
     * Function to insert data in merchant table.
     * 
     */

    
    function insert_in_merchants($d){

        $innerPera = "merchant_name,merchant_email,merchant_password,image";
        $merchant_name = $d["merchant_name"];
        $merchant_email = $d["merchant_email"];
        $merchant_password = $d["merchant_password"];
        $image = $d["image"];
        $conn = self::build_connection();
        $q1 = "insert into merchants($innerPera) values('{$merchant_name}','{$merchant_email}','{$merchant_password}','{$image}')";
        $conn->query($q1);  
        $merchant_id = $conn->insert_id;
        self::close_connection($conn);   
        return $merchant_id;
    }
/**
 * 
 * function to subtract balance after mail
 */

public function subtract($d)
{
    $conn=self::build_connection();
    $key=$d["token"];
    $q="select merchant_id from merchants where token = '{$key}'";
    $result= $conn->query($q);
    $mid=$result->fetch_assoc();
    $id=$mid["merchant_id"];
    $q1="UPDATE cards SET credit = credit-1 WHERE merchant_id ='{$id}'";
    $res=$conn->query($q1);
    self::close_connection($conn);
    if($res)
    {
    return true;
    }
    else {
        return false;
    }
}





/**
 * 
 * inseting data in user
 */

    
    function insert_in_user($tableName,$parameter,$email){

        $q2 = "SELECT merchant_id from merchants where merchant_email = '{$email}'";  
        $conn = self::build_connection(); 
        $res=$conn->query($q2); 
        $mid=$res->fetch_assoc();
        
        $parameter[6]=$mid["merchant_id"];
       
        $innerPera = "user_name,user_email,user_password,email_permission,list_view_permission,payment_permission,merchant_id";
        $S = implode("','",$parameter);       
        $S2 = "'".$S."'";     
        $q3 = "insert into $tableName($innerPera) values($S2)"; 
        echo $q3;
         
        $conn->query($q3);       
          
    }
    
    /**
     * 
     * inserting data in cards
     */

        function insert_in_cards($d, $merchant_id){

        $innerPera = "card_no,cvc,credit,valid_from,valid_through,merchant_id";
        $card_no=$d["card_no"];
        $cvc=$d["cvc"];
        $credit=$d["credit"];
        $valid_from=$d["valid_from"];
        $valid_through=$d["valid_through"];
        $conn = self::build_connection();           
        $q = "insert into cards($innerPera) values('{$card_no}','{$cvc}','{$credit}','{$valid_from}','{$valid_through}','{$merchant_id}')";
        $conn->query($q);
        self::close_connection($conn);
  
    }

            /**
             * 
             * inserting request data against erchant id who is sending mail in requests table
             */

            function insert_request($d){

                $conn = self::build_connection(); 
                $q="select MAX(merchant_id) from merchants";
                $result=$conn->query($q);
                $q1="select MAX(response_id) from responses";
                $result1=$conn->query($q1);

                if($result->num_rows > 0)  
                { 
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    $data1 = $result1->fetch_all(MYSQLI_ASSOC);

                
                    $mid= $data[0]["MAX(merchant_id)"];
                    $rid=$data1[0]["MAX(response_id)"];
                    $email_subject=$d["subject"];
                    $email_from=$d["from"];
                    $send_to=$d["to"];
                    $email_body=$d["body"];

                    $innerPera = "response_id,merchant_id,email_subject,email_from,send_to,email_body";
                    $q2 = "insert into requests($innerPera) values('{$rid}','{$mid}','{$email_subject}','{$email_from}','{$send_to}','{$email_body}')";
                    $result=$conn->query($q2);
                    self::close_connection($conn);
                    
                }


                else{

                    echo "your data is not valid";
                }
        }


            /**
             * 
             * merchant log out using key
             */
            public function logout_by_key ($k)
            {
                
                $conn = self::build_connection();
                $q="select merchant_id from merchants where token = '{$k}'";
                $result=$conn->query($q) or die("query not executed");

                if($result->num_rows > 0)
                {
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $mid=$data[0]["merchant_id"];
                $q1="UPDATE merchants SET merchant_status='0'  WHERE merchant_id='{$mid}'";
                $result1=$conn->query($q1) or die("query not executed");
                $q2="UPDATE merchants SET token=NULL  WHERE merchant_id='{$mid}'";
                $result2=$conn->query($q2) or die("query not executed");
                echo json_encode(array("Message"=>"you are logout!!!"));
                self::close_connection($conn);
                }

                else{
                    echo json_encode(array("Message"=>"You are already logout"));
                    return false;
                }
                

            }

            /*
             * check token whether the token exists or notfunction
             */
            public function check_token($key)
            {
                $conn = self::build_connection();
                $q="select * from merchants where token = '{$key}'";
                
                
                $result=$conn->query($q) or die("query not executed");

            
                self::close_connection($conn);

                if( $result->num_rows > 0)
                {
                    return true;
                }

                else{
                    return false;
                }

            }

            /**
             * merchant login function
             */

            function merchant_login($data)
            {
                $conn = self::build_connection();
                $email=$data["email"];
                $password=$data["password"];
                $query = "SELECT * FROM  merchants WHERE merchant_password = '{$password}' AND  merchant_email='{$email}'";
                //sql query to check Password and email is present in databse 
                $result = $conn->query($query) or die("SQL QUERY FAIL.");
                if($result->num_rows > 0)  { $data = $result->fetch_all(MYSQLI_ASSOC);
                
                    $jwt = new JWT($email);
                    $token = $jwt->Generate_jwt();
                    //$token="fjhfuufhrf";
                $message_display=array("Status_code"=>200,"Message"=>"Successfully Login","token" => $token);//if password and email are matched display this message
                http_response_code(200);
                print_r(json_encode($message_display));
                    $q="UPDATE merchants SET token = '{$token}'  WHERE merchant_email='{$email}' ";
                    $result = $conn->query($q) or die("SQL  QUERY FAIL for adding token .");
                    $q1="UPDATE merchants SET merchant_status='1'  WHERE merchant_email='{$email}' ";
                    $result = $conn->query($q1) or die("SQL  QUERY FAIL for adding token .");
                    return true;
                }
                else{
                    return false;
                }
            }

        /**
             * user login function
             */

            function user_log($data)
            {
               $conn = self::build_connection();
               $email=$data["email"];
               $password=$data["password"];
               $query = "SELECT * FROM  secondaryusers WHERE user_password = '{$password}' AND  user_email='{$email}'";
               echo($query);
               //sql query to check Password and email is present in databse 
               $result = $conn->query($query) or die("SQL QUERY FAIL.");
               if($result->num_rows > 0)  { $data = $result->fetch_all(MYSQLI_ASSOC);
               
                    $jwt = new JWT($email);
                    $token = $jwt->Generate_jwt();
                   //$token="fjhfuufhrf";
                  $message_display=array("Status_code"=>200,"Message"=>"Successfully Login","token" => $token);//if password and email are matched display this message
                  http_response_code(200);
                  print_r(json_encode($message_display));
                   $q="UPDATE secondaryusers SET token = '{$token}'  WHERE user_email='{$email}' ";
                   $result = $conn->query($q) or die("SQL  QUERY FAIL for adding token .");
                   return true;
               }
               else{
                   return false;
               }
            }

    /**
     * This function is used to insert response in response table.
     */
 public function insert_response($k)
 {
    $conn = self::build_connection();
     if($k=="Recieved")
     {
         
         $desc="Mail sent successfuly";
         $err="NO";
         $q = "insert into responses (response_status,response_description,response_error)values('{$k}','{$desc}','{$err}')";
                       
        $conn->query($q);

     }
     if($k=="Processed")
     {
        
        $desc="Mail is in process";
        $err="NO";
        $q = "insert into responses (response_status,response_description,response_error)values('{$k}','{$desc}','{$err}')";
                        
        $conn->query($q);
     }
     if($k=="Error")
     {
        
        $desc="An Error Occoured";
        $err="YES";
        $q = "insert into responses (response_status,response_description,response_error)values('{$k}','{$desc}','$err')";
    
        $conn->query($q);
     }
    if($k=="Invalid")
     {
        
        $desc="Plz Enter Valid Email";
        $err="YES";
        $q = "insert into responses (response_status,response_description,response_error)values('{$k}','{$desc}','{$err}')";
                        
        $conn->query($q);
     }

     self::close_connection($conn);




 }

    /**
     * This function is used to fetch users from table.
     */
    function Fetch_list($tableName)
    {
        $conn = self::build_connection();
        $q = "select * from ".$tableName;
        $result = $conn->query($q);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        self::close_connection($conn);
        return $data;
    }

    /**
     * This function is used to select merchant from table with the specific email.
     */
    function search_employ_by_email($tableName,$email)        // searching merchant by email
    {
        $conn = self::build_connection();
        $q = "select * from ".$tableName ." WHERE merchant_email='{$email}'";
        $result = $conn->query($q);
        self::close_connection($conn);
        if($result->num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
     * This function is used to select card from table with the specific card_no.
     */

    function search_card_by_card_no($tableName,$card_no)        
    {
        $conn = self::build_connection();
        $q = "select * from ".$tableName ." WHERE card_no='{$card_no}'";
        $result = $conn->query($q);
        self::close_connection($conn);
        if($result->num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * This functioon is used to search employee with specific CNIC and name.
     */
        function searchEmployee($tableName,$Name,$CNIC){
        $conn = self::build_connection();
        $N = "'$Name'";
        $C = "'$CNIC'";
        $q = "select * from $tableName where CNIC = $C and Name = $N";
        $result = $conn->query($q);
        if ($result->num_rows > 0){
            $output = $result->fetch_assoc();
        }else{
            $output = array('Message' => 'No Employee Match :','status'=>'204');
        }
        self::close_connection($conn);
           return $output;
        }
   
}

?>
