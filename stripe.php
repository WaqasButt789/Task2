<?php

require "Database.php";

// add in stripe api
$amount = 500;
//$input = 

$data =  [
           'card[number]' =>4242424242424242,// $input['number'],
            'card[exp_month]' => 10,//$input['expMonth'],
            'card[exp_year]' => 23,//$input['expYear'],
            'card[cvc]' =>456// $input['cvc']
        ];

$db=new Database();
$stripTokenResponse = $db->getStripeToke($data);
$stripTokenRes = json_decode($stripTokenResponse);
$stripToken =  $stripTokenRes->id;
$addBalance = $db->charge($stripToken,$amount);
if($addBalance)
{
print_r ($addBalance);
}
?>