<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<center style="padding-top:50px;">
<table width="50%">
    <tr>
		<td align="center" valign="middle"><div style="font-weight:bold;padding-top:20px;"><?=$waitMessage?></div></td>
	</tr>
<?php 
	switch($paymentMethodCode){
		case 'razorpay':
?>
<tr>
<td align="center" valign="middle">
	<table width="100%">
		<tr>
			<td colspan="2" align="center">
			<form action="<?= $order['returnUrl'] ?? '' ?>" method="POST">
		<script    
			src="https://checkout.razorpay.com/v1/checkout.js"    
			data-key="<?= $order['razorpay']['key_id']?>" // Enter the Key ID generated from the Dashboard    
			data-amount="<?= $order['razorpay']['amount']?>" // Amount is in currency subunits. Default currency is INR. Hence, 29935 refers to 29935 paise or INR 299.35.    
			data-currency="<?= $order['razorpay']['currency']?>"
			data-order_id="<?= $order['razorpay']['order_id']?>" //This is a sample Order ID. Create an Order using Orders API. (https://razorpay.com/docs/payment-gateway/orders/integration/#step-1-create-an-order)    
			data-prefill.name="<?= $order['razorpay']['name']?>"
			data-prefill.email="<?= $order['razorpay']['email']?>"    
			data-prefill.contact="<?= $order['razorpay']['mobile']?>"    
			data-buttontext="Pay with Razorpay"    
			data-name="PerfumeBooth Pvt Ltd"
			data-description="Buy Online Perfume Fragrance | Perfume Selfie For Men and Women"    
			data-image="https://www.perfumebooth.com/assets/icons/icon-144x144.png"    
			data-model.handleback="true"	
			data-model.escape="false"
			data-theme.color="#ec297b">
		</script>
		<input type="hidden" custom="Hidden Element" name="hidden">
	</form>	
		<script type="text/javascript">
			$(document).ready(function(){
				setTimeout(() => { $(".razorpay-payment-button").click(); }, 2000);	
			});
		</script>
			</td>
		</tr>
		<tr>
			<td align="center" width="50%"><a href="https://www.perfumebooth.com/usd/" class="btn btn-info btn-sm" style="margin-top:20px;">Click to Home</a></td>
			<td align="center"><a href="#" class="btn btn-default btn-sm" onClick="location.reload();" style="margin-top:20px;">Refresh</a></td>
		</tr>
	</table>
</td>
</tr>
<?php 
			break;
		default:
	}	
?>
    <tr><td align="center" valign="middle"></td></tr>
</table>
<script type="text/javascript">
	///document.pbRequestForm.submit(); 
</script>

</center>

