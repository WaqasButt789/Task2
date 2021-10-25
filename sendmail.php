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

    public function get_data(){
        $data=json_decode(file_get_contents("php://input"),true);
        return $data;
    }

	public function add_request()
	{
        //$email_subject=$this->data["subject"];
		//$email_from=$this->data["from"];
		//$email_body=$this->data["body"];
		//$send_to=$this->data["to"];

		//self::insert_request($this->data);
	} 

	public function add_response ()
	{
			$arr= array("Recieved","Processed","Error","Invalid");
			$r=rand(0,3);
			$k=$arr[$r];

			return $k;


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

		self::add_request();



	} catch ( Exception $e ) {
		// something went wrong so display the error message
	    echo 'Caught exception: '. $e->getMessage() ."\n";
	}

 }
}
$obj=new SendMail();
$p=$obj->get_data();
$obj->email($p);
echo $obj->add_response();