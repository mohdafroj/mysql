<?php
function getBasePath(){
	$basePath = ($_SERVER["HTTPS"] != "on") ? "http://" : "https://";
	$basePath .= $_SERVER["SERVER_NAME"];
	if (strpos($_SERVER["REQUEST_URI"], 'new/') != FALSE ){
		$basePath .= '/new';
	}	
	return $basePath;
}
$resUrl = getBasePath().'/pg/payu/response.php';

// Merchant key here as provided by Payu
$prodMode = true;
$MERCHANT_KEY = ($prodMode) ? "kl6JhP":"gtKFFx"; //Please change this value with live key for production
$hash_string = '';
// Merchant Salt as provided by Payu
$SALT = ($prodMode) ? "gY1lxrD3":"eCwWELxi"; //Please change this value with live salt for production

// End point - change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = ($prodMode) ? "https://secure.payu.in":"https://test.payu.in"; //"https://test.payu.in";

$action = $PAYU_BASE_URL . '/_payment';
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
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
}
?>
<html>
  <body onload="submitPayuForm()">
  <center>
  <table width="500px;">
    <tr>
		  <td align="center" valign="middle">Do Not Refresh or Press Back <br/> Redirecting to PayU Money</td>
	  </tr>
    <tr>
			<td>
      <form action="<?php echo $action; ?>" method="post" name="payuForm" > 
        <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
        <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
        <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
	 
	      <input type="hidden" name="surl" value="<?php echo $resUrl;?>" />   <!--Please change this parameter value with your success page absolute url like http://mywebsite.com/response.php. -->
		    <input type="hidden" name="furl" value="<?php echo $resUrl;?>" /><!--Please change this parameter value with your failure page absolute url like http://mywebsite.com/response.php. -->
        <input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" />
        <input type="hidden" name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" />
        <input type="hidden" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" />
        <input type="hidden" name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" />
        <input type="hidden" name="productinfo" value="<?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?>" />
        <input type="hidden" name="lastname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" />
        <input type="hidden" name="curl" value="" />
        <input type="hidden" name="address1" value="<?php echo (empty($posted['address1'])) ? '' : $posted['address1']; ?>" />
        <input type="hidden" name="address2" value="<?php echo (empty($posted['address2'])) ? '' : $posted['address2']; ?>" />
        <input type="hidden" name="city" value="<?php echo (empty($posted['city'])) ? '' : $posted['city']; ?>" />
        <input type="hidden" name="state" value="<?php echo (empty($posted['state'])) ? '' : $posted['state']; ?>" />
        <input type="hidden" name="country" value="<?php echo (empty($posted['country'])) ? '' : $posted['country']; ?>" />
        <input type="hidden" name="zipcode" value="<?php echo (empty($posted['zipcode'])) ? '' : $posted['zipcode']; ?>" />
        <input type="hidden" name="udf1" value="<?php echo (empty($posted['udf1'])) ? '' : $posted['udf1']; ?>" />
        <input type="hidden" name="udf2" value="<?php echo (empty($posted['udf2'])) ? '' : $posted['udf2']; ?>" />
        <input type="hidden" name="udf3" value="<?php echo (empty($posted['udf3'])) ? '' : $posted['udf3']; ?>" />
        <input type="hidden" name="udf4" value="<?php echo (empty($posted['udf4'])) ? '' : $posted['udf4']; ?>" />
        <input type="hidden" name="udf5" value="<?php echo (empty($posted['udf5'])) ? '' : $posted['udf5']; ?>" />
        <input type="hidden" name="pg" value="<?php echo (empty($posted['pg'])) ? '' : $posted['pg']; ?>" />

				<input type="hidden" name="codurl" 			   value="<?php echo (empty($posted['codurl']))             ? '' : $posted['codurl']; ?>" />
				<input type="hidden" name="shipping_firstname" value="<?php echo (empty($posted['shipping_firstname'])) ? '' : $posted['shipping_firstname']; ?>" />
				<input type="hidden" name="shipping_lastname"  value="<?php echo (empty($posted['shipping_lastname']))  ? '' : $posted['shipping_lastname']; ?>" />
				<input type="hidden" name="shipping_address1"  value="<?php echo (empty($posted['shipping_address1']))  ? '' : $posted['shipping_address1']; ?>" />
				<input type="hidden" name="shipping_address2"  value="<?php echo (empty($posted['shipping_address2']))  ? '' : $posted['shipping_address2']; ?>" />
				<input type="hidden" name="shipping_city" 	   value="<?php echo (empty($posted['shipping_city']))      ? '' : $posted['shipping_city']; ?>" />
				<input type="hidden" name="shipping_state"     value="<?php echo (empty($posted['shipping_state']))     ? '' : $posted['shipping_state']; ?>" />
				<input type="hidden" name="shipping_country"   value="<?php echo (empty($posted['shipping_country']))   ? '' : $posted['shipping_country']; ?>" />
				<input type="hidden" name="shipping_zipcode"   value="<?php echo (empty($posted['shipping_zipcode']))   ? '' : $posted['shipping_zipcode']; ?>" />
				<input type="hidden" name="shipping_phone"     value="<?php echo (empty($posted['shipping_phone']))     ? '' : $posted['shipping_phone']; ?>" />			
      </form>
			</td>
    </tr>
  </table>
<center>
<script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        //return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
</script>
</body>
</html>
