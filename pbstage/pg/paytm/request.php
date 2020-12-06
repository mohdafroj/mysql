<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");
// following files need to be included
require_once("./lib/config_paytm.php");
require_once("./lib/encdec_paytm.php");

$checkSum = "";
$paramList = array();
// Create an array having all required parameters for creating checksum.
$paramList["MID"] 					= PAYTM_MERCHANT_MID;
$paramList["ORDER_ID"] 				= isset($_POST['ORDER_ID']) ? $_POST['ORDER_ID'] : 0;
$paramList["CUST_ID"] 				= isset($_POST['CUST_ID']) ? $_POST['CUST_ID'] : NULL;
$paramList["INDUSTRY_TYPE_ID"] 		= INDUSTRY_TYPE_ID;
$paramList["CHANNEL_ID"] 			= CHANNEL_ID;
$paramList["TXN_AMOUNT"] 			= isset($_POST['TXN_AMOUNT']) ? $_POST['TXN_AMOUNT'] : 0;
$paramList["WEBSITE"] 				= PAYTM_MERCHANT_WEBSITE;
$paramList["CALLBACK_URL"] 			= getBasePath().'/pg/paytm/response.php';
$paramList["EMAIL"] 				= isset($_POST['EMAIL']) ? $_POST['EMAIL'] : NULL;
$paramList["MOBILE_NO"] 			= isset($_POST['MOBILE_NO']) ? $_POST['MOBILE_NO'] : NULL;
$paramList["PAYMENT_DETAILS"] 		= isset($_POST['PAYMENT_DETAILS']) ? $_POST['PAYMENT_DETAILS'] : NULL;
$paramList["ORDER_DETAILS"] 		= isset($_POST['ORDER_DETAILS']) ? $_POST['ORDER_DETAILS'] : NULL;
$paramList["ADDRESS_1"] 			= isset($_POST['ADDRESS_1']) ? $_POST['ADDRESS_1'] : NULL;
//$paramList["ADDRESS_2"] 			= isset($_POST['ADDRESS_2']) ? $_POST['ADDRESS_2'] : NULL;
$paramList["CITY"] 					= isset($_POST['CITY']) ? $_POST['CITY'] : NULL;
$paramList["STATE"] 				= isset($_POST['STATE']) ? $_POST['STATE'] : NULL;
$paramList["PINCODE"] 				= isset($_POST['PINCODE']) ? $_POST['PINCODE'] : NULL;

/*
$paramList["MSISDN"] = $MSISDN; //Mobile number of customer
$paramList["EMAIL"] = $EMAIL; //Email ID of customer
$paramList["VERIFIED_BY"] = "EMAIL"; //
$paramList["IS_USER_VERIFIED"] = "YES"; //

*/

//Here checksum string will return by getChecksumFromArray() function.
$checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);

?>
<html>
<head>
<title>Please do not refresh this page...</title>
</head>
<body>
	<center><h1>Please do not refresh this page...</h1></center>
		<form method="post" action="<?php echo PAYTM_TXN_URL ?>" name="f1">
		<table border="0">
			<tbody>
			<?php
			foreach($paramList as $name => $value) {
				echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
			}
			?>
			<input type="hidden" name="CHECKSUMHASH" value="<?php echo $checkSum ?>">
			</tbody>
		</table>
		<script type="text/javascript">
			document.f1.submit();
		</script>
	</form>
</body>
</html>