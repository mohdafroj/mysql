<?php
function getCurrentPath(){ 
	$curPageURL = "";
	if ($_SERVER["HTTPS"] != "on")
		$curPageURL .= "http://";
	else
		$curPageURL .= "https://" ;
	if ($_SERVER["SERVER_PORT"] == "80")
		$curPageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	else
		$curPageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	//echo $curPageURL;
	$count = strlen(basename($curPageURL));
	$path = substr($curPageURL,0, -$count);
	return $path ;
}
$resUrl = getCurrentPath().'response.php';

// Merchant key here as provided by Payu
$prodMode = true;
$MERCHANT_KEY = ($prodMode) ? "kl6JhP":"gtKFFx"; //Please change this value with live key for production
   $hash_string = '';
// Merchant Salt as provided by Payu
$SALT = ($prodMode) ? "gY1lxrD3":"eCwWELxi"; //Please change this value with live salt for production

// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = ($prodMode) ? "https://secure.payu.in":"https://test.payu.in"; //"https://test.payu.in";

$action = '';

$posted = array();
if(!empty($_POST)) {
    
  $_POST['surl'] = $_POST['furl'] = $resUrl;
  $_POST['key'] = $MERCHANT_KEY;
  foreach($_POST as $key => $value) {    
    $posted[$key] = $value;	
  }
}

$formError = 0;

if(empty($posted['txnid'])) {
   // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
         
  ) {
    $formError = 1;
  } else {
    
	$hashVarsSeq = explode('|', $hashSequence);
 
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;


    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
}

//print_r($_POST);
?>
<html>
  <body>
    <form action="request.php" method="post" name="payuForm" >
      <table>
        <tr>
		  <td>Order Number:</td>
          <td><input type="text" name="txnid" id="txnid" value="123456" /></td>
		  <td>Order Amount:</td>
          <td><input type="text" name="amount" value="10" /></td>
        </tr>
        <tr>
		  <td>Email:</td>
          <td><input type="text" name="email" value="mohd.afroj@perfumebooth.com" /></td>
		  <td>Phone:</td>
          <td><input type="text" name="phone" value="7838799646" /></td>
        </tr>
        <tr>
		  <td>Name:</td>
          <td><input type="text" name="firstname" value="afroj" /></td>
		  <td>Info:</td>
          <td><textarea name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
        </tr>
        
        <tr>
		  <td>First Name:</td>
          <td><input type="text" name="shipping_firstname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" /></td>
		  <td>Last Name:</td>
          <td><input type="text" name="shipping_lastname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" /></td>
        </tr>
        <tr>
		  <td>Address1:</td>
          <td><input type="text" name="shipping_address1" value="address1" /></td>
		  <td>Address2:</td>
          <td><input type="text" name="shipping_address2" value="Address2" /></td>
        </tr>
        <tr>
		  <td>City:</td>
          <td><input type="text" name="shipping_city" value="Kirti Nagar" /></td>
		  <td>State:</td>
          <td><input type="text" name="shipping_state" value="Delhi" /></td>
        </tr>
        <tr>
		  <td>Country:</td>
          <td><input type="text" name="shipping_country" value="India" /></td>
		  <td>Pincode:</td>
          <td><input type="text" name="shipping_zipcode" value="110015" /></td>
        </tr>
        <tr>
			<td colspan="4">
				<input type="text" name="shipping_phone" value="7838799646" />			
			</td>
        </tr>
        <tr>
            <td colspan="4"><input type="submit" value="Submit" /></td>
        </tr>
      </table>
    </form>
  </body>
</html>

