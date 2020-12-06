<?php
$status		= isset($_POST["status"]) ? $_POST["status"] : NULL;
$firstname	= isset($_POST["firstname"]) ? $_POST["firstname"] : NULL;
$amount		= isset($_POST["amount"]) ? $_POST["amount"] : 0;
$txnid		= isset($_POST["txnid"]) ? $_POST["txnid"] : 0;
$posted_hash= isset($_POST["hash"]) ? $_POST["hash"] : NULL;
$key		= isset($_POST["key"]) ? $_POST["key"] : NULL;
$productinfo= isset($_POST["productinfo"]) ? $_POST["productinfo"] : NULL;
$email		= isset($_POST["email"]) ? $_POST["email"] : NULL;
$salt		= "gY1lxrD3"; //Please change the value with the live salt for production environment


function getBasePath(){
	$basePath = ($_SERVER["HTTPS"] != "on") ? "http://" : "https://";
	$basePath .= $_SERVER["SERVER_NAME"];
	if (strpos($_SERVER["REQUEST_URI"], 'new/') != FALSE ){
		$basePath .= '/new';
	}	
	return $basePath;
}

function pgResEnDe($string, $action = 'e' ) {
	// you may change these values to your own
	$secret_key = 'pb';
	$secret_iv = 'google';
 
	$output = false;
	$encrypt_method = "AES-256-CBC";
	$key = hash( 'sha256', $secret_key );
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
 
	if( $action == 'e' ) {
		$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	}
	else if( $action == 'd' ){
		$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
 
	return $output;
}

//Validating the reverse hash
if (isset($_POST["additionalCharges"])) {
    $additionalCharges=$_POST["additionalCharges"];
    $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
} else {
    $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$hash = hash("sha512", $retHashSeq);
	
$basePath = getBasePath();
$pgData = pgResEnDe(json_encode($_POST),'e');

$responseCode = 500;
switch($status){
	case 'success': $responseCode = 100; break;
	case 'failure': $responseCode = 300; break;
	default:	
}
if ($hash != $posted_hash) {
	$redirectUrl = 'checkout/unauthorized'; //echo "Transaction has been tampered. Please try again";
} else {
	$redirectUrl = 'checkout/onepage/confirmation?order-number='.$txnid.'&pg-name=payu&status='.$responseCode.'&pg-data='.$pgData;
	//echo "<h3>Thank You, " . $firstname .".Your order status is ". $status .".</h3>";
	//echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
}
header('Location: '.$basePath.'/'.$redirectUrl);

?>