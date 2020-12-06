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
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>
  <body onload="submitPayuForm()">
    <?php if($formError) { ?>
      <span style="color:red">Loading...</span>
      <br/>
      <br/>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" name="payuForm" >
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
	 
	    <input type="hidden" name="surl" value="<?php echo $resUrl;?>" />   <!--Please change this parameter value with your success page absolute url like http://mywebsite.com/response.php. -->
		 <input type="hidden" name="furl" value="<?php echo $resUrl;?>" /><!--Please change this parameter value with your failure page absolute url like http://mywebsite.com/response.php. -->
	 
      <table>
        <!--tr>
          <td><b>Mandatory Parameters</b></td>
        </tr>
        <tr-->
          <td><input type="hidden" name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
          <td><input type="hidden" name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
          <td><input type="hidden" name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
        </tr>
        <tr>
          <td colspan="2"><textarea style="display:none;" name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
        </tr>
        
        <!--tr>
          <td><b>Optional Parameters</b></td>
        </tr-->
        <tr>
          <td><input type="hidden" name="lastname" id="lastname" value="<?php echo (empty($posted['lastname'])) ? '' : $posted['lastname']; ?>" /></td>
          <td><input type="hidden" name="curl" value="" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="address1" value="<?php echo (empty($posted['address1'])) ? '' : $posted['address1']; ?>" /></td>
          <td><input type="hidden" name="address2" value="<?php echo (empty($posted['address2'])) ? '' : $posted['address2']; ?>" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="city" value="<?php echo (empty($posted['city'])) ? '' : $posted['city']; ?>" /></td>
          <td><input type="hidden" name="state" value="<?php echo (empty($posted['state'])) ? '' : $posted['state']; ?>" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="country" value="<?php echo (empty($posted['country'])) ? '' : $posted['country']; ?>" /></td>
          <td><input type="hidden" name="zipcode" value="<?php echo (empty($posted['zipcode'])) ? '' : $posted['zipcode']; ?>" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="udf1" value="<?php echo (empty($posted['udf1'])) ? '' : $posted['udf1']; ?>" /></td>
          <td><input type="hidden" name="udf2" value="<?php echo (empty($posted['udf2'])) ? '' : $posted['udf2']; ?>" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="udf3" value="<?php echo (empty($posted['udf3'])) ? '' : $posted['udf3']; ?>" /></td>
          <td><input type="hidden" name="udf4" value="<?php echo (empty($posted['udf4'])) ? '' : $posted['udf4']; ?>" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="udf5" value="<?php echo (empty($posted['udf5'])) ? '' : $posted['udf5']; ?>" /></td>
          <td><input type="hidden" name="pg" value="<?php echo (empty($posted['pg'])) ? '' : $posted['pg']; ?>" /></td>
        </tr>
        <tr>
			<td colspan="4">
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
			</td>
        </tr>
        <!--tr>
          <?php if(!$hash) { ?>
            <td colspan="4"><input type="submit" value="Submit" /></td>
          <?php } ?>
        </tr-->
      </table>
    </form>
  </body>
</html>
