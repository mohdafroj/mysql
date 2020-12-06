<?php
/*
Template Name: PostToZaakpayPage
*/
?>
<?php include('checksum.php'); ?>
<?php
	//set merchant identifier to post form
	$_POST['merchantIdentifier'] = Checksum::$merchantId;
	$_POST['mode'] 		 		 = Checksum::$mode;
	$_POST['showMobile'] 		 = Checksum::$showMobile;
	$_POST['currency'] 		 	 = Checksum::$currency;
	$_POST['returnUrl'] 		 = Checksum::getBasePath().'/pg/mobikwik/response.php';
	//enter your secret key here
	$secret = Checksum::$secretKey;
	//print_r($_POST);
	$all = Checksum::getAllParams();
	//echo '<br /><br />';
	//echo $all_1 = 'amount=100&buyerEmail=mohd.afroj@gmail.com¤cy=INR&merchantIdentifier=b19e8f103bce406cbd3476431b6b7973&orderId=ZPLive1530008316511&returnUrl=https://www.perfumeoffer.com/new/pg/mobiek/response.php&shipToAddress=Karti Nagar&shipToCity=Delhi&shipToCountry=India&shipToFirstname=Mohd Afroj&shipToLastname=Ansari&shipToPhoneNumber=7838799646&shipToPincode=110015&shipToState=Delhi&showMobile=1&';
	//echo '<br /><br />';
	$checksum = Checksum::calculateChecksum($secret, $all);
	
?>
<center>
<table width="500px;">
	<tr>
		<td align="center" valign="middle">Do Not Refresh or Press Back <br/> Redirecting to Zaakpay</td>
	</tr>
	<tr>
		<td align="center" valign="middle">
			<form action="<?php echo Checksum::$txnPostUrl; ?>" method="post">
				<?php
				Checksum::outputForm($checksum);
				?>
			</form>
		</td>

	</tr>

</table>

</center>
<script type="text/javascript">
var form = document.forms[0];
form.submit();
</script>
