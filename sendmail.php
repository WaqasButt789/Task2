<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: Application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Methods: post");
header('Access-Control-Allow-Headers: Content-Type,Access-Control-Allow-Methods,Access-Control-Allow-Headers,Authorization,X-Requested-With');
require 'vendor/autoload.php';
require 'Database.php';

class SendMail extends Database
{

    public $data;


	public function dec_credit($d)
	{
		if(self::subtract($d))
		{
          echo "credit decremented";
		}
		else{
			echo "credit not decremented";
		}
        

	}

    public function get_data(){
        $data=json_decode(file_get_contents("php://input"),true);
        return $data;
    }

	public function add_request($data)
	{
        $email_subject=$data["subject"];
		$email_from=$data["from"];
		$email_body=$data["body"];
		$send_to=$data["to"];
		self::insert_request($data);
	} 

	public function add_response ()
	{
			$arr= array("Recieved","Processed","Error","Invalid");
			$r=rand(0,3);
			$k=$arr[$r];
			
			self::insert_response($k);
			
	}

	public function get_token($data)
	{
         $key= $data["token"];
		 
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
    public function email($data) {

	// create new sendgrid mail
	$email = new \SendGrid\Mail\Mail(); 

	// specify the email/name of where the email is coming from
	$email->setFrom( $data["from"], "Waqas" );

	// set the email subject line
	$email->setSubject( $data["subject"] );

	// specify the email/name we are sending the email to
	$email->addTo($data["to"], "waqas" );

	// add our email body content
	$email->addContent( "text/plain", $data["body"] );

	// create new sendgrid
	$sendgrid = new \SendGrid("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");

	try {
		// try and send the email
	    $response = $sendgrid->send( $email );
	    // print out response data
	    print $response->statusCode() . "\n";
	    print_r( $response->headers() );
	    print $response->body() . "\n";

		//self::add_request();



	} catch ( Exception $e ) {
		// something went wrong so display the error message
	    echo 'Caught exception: '. $e->getMessage() ."\n";
	}

 }
}
$obj=new SendMail();
$p=$obj->get_data();

if($obj->get_token($p)){
	$obj->email($p);
	$obj->add_response();
	$obj->add_request($p);
	$obj->dec_credit($p);

}

else{
    echo json_encode(array("Message"=>"you are not login"));
}