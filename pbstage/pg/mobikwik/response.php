<?php include('checksum.php'); ?>
<?php
	// Please insert your own secret key here
	$secret = Checksum::$secretKey;

	$orderId 		= isset($_POST['orderId']) ? $_POST['orderId']:NULL;
	$responseCode 	= isset($_POST['responseCode']) ? $_POST['responseCode']:0;
	$recd_checksum 	= isset($_POST['checksum']) ? $_POST['checksum']:NULL;
	$all = Checksum::getAllResponseParams();
	error_log("AllParams:".$all);
	error_log("Secret Key : ".$secret);
	$checksum_check = Checksum::verifyChecksum($recd_checksum, $all, $secret);
	$pgData = Checksum::pgResEnDe(json_encode($_POST), 'e');
	$basePath = Checksum::getBasePath();
	
	if( $checksum_check ){
		$redirectUrl = 'checkout/onepage/confirmation?order-number='.$orderId.'&pg-name=mobikwik&status='.$responseCode.'&pg-data='.$pgData;
	}else{
		$redirectUrl = 'checkout/unauthorized';
	}
	header('Location: '.$basePath.'/'.$redirectUrl);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Do Not Refresh or Press Back</title>
</head>
<body>
<center>
<table width="500px;"><strong>Do Not Refresh or Press Back</strong> <br/> Redirecting to www.perfumebooth.com
	<?php Checksum::outputResponse($checksum_check);?>
</table>
</center>
</body>
</html>
