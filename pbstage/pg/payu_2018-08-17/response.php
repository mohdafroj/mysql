<?php
$status=$_POST["status"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"]; //Please use the amount value from database
$txnid=$_POST["txnid"];
$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];
$salt="gY1lxrD3"; //Please change the value with the live salt for production environment


function getRedirectPath(){
	$basePath = "";
	if($_SERVER["HTTPS"] != "on"){
		$basePath = "http://";
	}else{
		$basePath = "https://";
	}	
	if ($_SERVER["SERVER_PORT"] == "80"){
		$basePath .= $_SERVER["SERVER_NAME"];
	}else{
		$basePath .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	}
	
	if (strpos($_SERVER["REQUEST_URI"], '/new/') != FALSE ){
		$basePath .= '/new';
	}
	
	return $basePath;
}
$redirectUrl = getRedirectPath();

//Validating the reverse hash
if (isset($_POST["additionalCharges"])) {
    $additionalCharges=$_POST["additionalCharges"];
    $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
} else {
    $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
}
$hash = hash("sha512", $retHashSeq);
	

if( $_POST ){
	$key = md5($txnid);
	if ($hash != $posted_hash) {
		header('Location: '.$redirectUrl.'/store/order-confirmation?order-number='.$txnid.'&key='.$key.'&temp=1&status='.$status);
		//echo "Transaction has been tampered. Please try again";
	} else {
		header('Location: '.$redirectUrl.'/store/order-confirmation?type=payu&order-number='.$txnid.'&key='.$key.'&status='.$status);
		//echo "<h3>Thank You, " . $firstname .".Your order status is ". $status .".</h3>";
		//echo "<h4>Your Transaction ID for this transaction is ".$txnid.".</h4>";
	}     
}else{	
	header('Location: '.$redirectUrl);
}
?>	