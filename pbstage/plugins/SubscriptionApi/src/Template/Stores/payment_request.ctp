<?php 
	switch($paymentMethodCode) {
		case 'razorpay':
?>
			<div class="row">
				
				<div class="col-md-6 offset-md-3 col-12 offset-0">
					<figure>
						<?php echo $this->Html->image('SubscriptionApi.payment_gateway.png', ['alt'=>'Payment Gateway', 'class'=>'img-fluid mx-auto']); ?>
					</figure>
					<p class="text-center mb-4 text-warning"> <?=$waitMessage?> </p>
					<p class="text-center mb-4">
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
								data-name="<?php echo PC['COMPANY']['name']?>"
								data-description="<?php echo PC['COMPANY']['title']?>"    
								data-image="<?php echo $this->Url->build('/subscription_api/img/logo.svg');?>"    
								data-model.handleback="true"	
								data-model.escape="false"
								data-theme.color="#3b4e76">
							</script>
							<input type="hidden" custom="Hidden Element" name="hidden">
						</form>	
						<script type="text/javascript">						
							$(document).ready(function(){
								$(".razorpay-payment-button").hide();
								setTimeout(() => { $(".razorpay-payment-button").click(); }, 2000);	
							});
						</script>
						<!--button type="submit" class="btn my-btn cart_btn from-left">Pay with Razorpay</button-->
					</p>
					<p class="text-center">
						<a href="<?php echo PC['COMPANY']['website']?>" class="btn my-btn checkout_btn from-left mr-3">back to home</a>
						<a href="#" onClick="location.reload();" class="btn my-btn btn_outline from-left">Refresh</a>
					</p>
				</div>
				
			</div>

			<?php 
			break;
		case 'mobikwik':
?>
			<div class="row">
				
				<div class="col-md-6 offset-md-3 col-12 offset-0">
					<figure>
						<?php echo $this->Html->image('SubscriptionApi.payment_gateway.png', ['alt'=>'Payment Gateway', 'class'=>'img-fluid mx-auto']); ?>
					</figure>
					<p class="text-center mb-4 text-warning"> <?=$waitMessage?> </p>
					<p class="text-center mb-4">
						<form action="<?=$txnPostUrl;?>" method="post" name="pbRequestForm">
							<?=$outputForm;?>
						</form>
						<script type="text/javascript"> document.pbRequestForm.submit(); </script>
					</p>
					<p class="text-center">
						<a href="<?php echo PC['COMPANY']['website']?>" class="btn my-btn checkout_btn from-left mr-3">back to home</a>
					</p>
				</div>
				
			</div>
			<?php 
			break;
		case 'paytm':
?>
			<div class="row">
				
				<div class="col-md-6 offset-md-3 col-12 offset-0">
					<figure>
						<?php echo $this->Html->image('SubscriptionApi.payment_gateway.png', ['alt'=>'Payment Gateway', 'class'=>'img-fluid mx-auto']); ?>
					</figure>
					<p class="text-center mb-4 text-warning"> <?=$waitMessage?> </p>
					<p class="text-center mb-4">
						<form action="<?=$txnPostUrl;?>" method="post" name="pbRequestForm">
							<table border="0">
								<tbody>
						<?php
							foreach ($paramList as $name => $value) {
								echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
							}
						?>
									<input type="hidden" name="CHECKSUMHASH" value="<?php echo $checkSum ?>">
								</tbody>
							</table>
						</form>
						<script type="text/javascript"> document.pbRequestForm.submit(); </script>
					</p>
					<p class="text-center">
						<a href="<?php echo PC['COMPANY']['website']?>" class="btn my-btn checkout_btn from-left mr-3">back to home</a>
					</p>
				</div>
				
			</div>
<?php 
			break;
		default:
?>
			<div class="row">
				
				<div class="col-md-6 offset-md-3 col-12 offset-0">
					<figure>
						<?php echo $this->Html->image('SubscriptionApi.payment_gateway.png', ['alt'=>'Payment Gateway', 'class'=>'img-fluid mx-auto']); ?>
					</figure>
					<p class="text-center mb-4 text-warning"> <?=$waitMessage?> </p>
					<p class="text-center">
						<a href="<?php echo PC['COMPANY']['website']?>" class="btn my-btn checkout_btn from-left mr-3">back to home</a>
					</p>
				</div>
				
			</div>
<?php 
	}	
?>
