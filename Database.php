<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        echo $q1;
        $conn->query($q1);  
        $merchant_id = $conn->insert_id;
        self::close_connection($conn);   
        return $merchant_id;
    }


    
    function insert_in_user($tableName,$parameter,$email){

        $q2 = "SELECT merchant_id from merchants where merchant_email = '{$email}'";  
        $conn = self::build_connection(); 
        $res=$conn->query($q2); 
        $mid=$res->fetch_assoc();
        $parameter[5]=$mid["merchant_id"];
        
        
        $innerPera = "user_name,user_email,email_permission,list_view_permission,payment_permission,merchant_id";
        $S = implode("','",$parameter);       
        $S2 = "'".$S."'";     
        
              
        $q3 = "insert into $tableName($innerPera) values($S2)"; 
        echo $q3; 
        $conn->query($q3);       
          
    }
    

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
     * This function is used to select card from table with the specific card.
     */

    function search_card_by_card_no($tableName,$card_no)        // searching merchant by card_no
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
