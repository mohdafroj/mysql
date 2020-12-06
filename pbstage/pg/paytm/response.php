<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");

// following files need to be included
require_once("./lib/config_paytm.php");
require_once("./lib/encdec_paytm.php");

$paytmChecksum = "";
$paramList = array();
$isValidChecksum = "FALSE";

$paramList = $_POST;
$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; //Sent by Paytm pg

//Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application�s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.

$basePath = getBasePath();
$pgData   = pgResEnDe(json_encode($paramList), 'e');
$orderId  = isset($_POST["ORDERID"]) ? $_POST["ORDERID"] : 0;
$status   = isset($_POST["STATUS"]) ? $_POST["STATUS"] : NULL;
$responseCode = 500;
switch($status){
	case 'TXN_SUCCESS' : $responseCode = 100; break;
	case 'TXN_FAILURE' : $responseCode = 300; break;
	default : $responseCode = 500;
}

if($isValidChecksum == "TRUE") {
	$redirectUrl = 'checkout/onepage/confirmation?order-number='.$orderId.'&pg-name=paytm&status='.$responseCode.'&pg-data='.$pgData;
}else {
	$redirectUrl = 'checkout/unauthorized'; //echo "Transaction has been tampered. Please try again";
}
header('Location: '.$basePath.'/'.$redirectUrl);

?>