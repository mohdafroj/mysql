<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use Cake\Mailer\MailerAwareTrait;


class StoresController extends AppController
{
	use MailerAwareTrait;
	public function initialize(){
		parent::initialize();
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$this->loadComponent('CommonLogic');
		$this->loadComponent('Sms');
		$this->loadComponent('Store');
		$this->loadComponent('Coupon');
		$this->loadComponent('Delhivery');
		$this->loadComponent('Customer');
		$this->loadComponent('Membership');
	}
	
	public function index()
	{

		die;
		$customerId = 16597;
		//$this->Membership->add($memberData);
		/*
		$this->viewBuilder()->setLayout('Email/html/default');
		$orderNumber = 100066617;
		$order = $this->Store->orderReview(100066617);
		foreach( $order as $k=>$v ){
			$this->set($k, $v);
		}
		$this->render('/Email/html/Api/Customer/order_review');
		//pr($order);
		*/
		die;
		
	}
	
	public function autoMem() {
		$customerTable 		= TableRegistry::get('Customers');
		$customer			= $customerTable->find('all',['fields'=>['Customers.id'],'limit'=>10000])
							->join([
								'm' => [
									'table' => 'memberships',
									'type' => 'INNER',
									'conditions' => 'm.customer_id != Customers.id',
								]
							])
							->toArray();
		$total = count($customer); //pr($customer);
		if( $total > 0 ){
			foreach( $customer as $va ){
				$memberData = $this->Membership->getPlanData($va->id, 0);
				$this->Membership->add($memberData);
			}
		}else{
			'Added to all customers!';
		}
		die;
	}

	public function activeMember($customerId=0, $orderId=0){
		if( ($customerId > 0) && ($orderId > 0) ){
			$memberData = $this->Membership->getPlanData($customerId, $orderId);
			$this->Membership->add($memberData);
		}else{

		}
		die;
	}

    public function getMiniCart_0000()
    {
		$userId 	= $this->request->getQuery('userId');
		$auth 		= $this->Customer->isAuth($userId);
		
		$data 		= [];
		$message 	= '';
		$status 	= false;
    	if ($this->request->is(['post']) && $auth['status']) {
			$couponCode = $this->request->getData('couponCode','');
			$giftVoucherStatus = $this->request->getData('giftVoucherStatus');
			$pbPointsStatus = $this->request->getData('pbPointsStatus');
			$pbCashStatus = $this->request->getData('pbCashStatus');
			$paymentMethod = $this->request->getData('paymentMethod','payu');
			$pincode = $this->request->getData('pincode','');
			$cart = $this->Store->getCart($userId);
			$voucherMessage = '';
			$cartQty = $discountAmount = $totalAmountOfCart = $totalAmountAfterDiscount = $giftVoucherAmount = $pbPointsAmount = $pbCashAmount = $grandTotalBeforeShipping = $shippingAmount = $grandTotal = 0;
			$grandFinalTotal = $codAmount = $discountVoucher = $discountPoints = $discountCash = 0;
			if(count($cart) > 0){
				$wallet = $this->Customer->getWalletData($userId);
				$totalAmountOfCart = array_column($cart, 'qty_price');
				$totalAmountAfterDiscount = $totalAmountOfCart = array_sum($totalAmountOfCart);
				
				$price = array_column($cart,'price');
				$price = array_filter($price, function ($value) { return ($value >= 1000);} );
				$giftVoucherAmount = $wallet['voucherAmount'];
				if($giftVoucherStatus == "true"){
					if(count($price) > 0){
						$totalAmountAfterDiscount = $totalAmountOfCart - $giftVoucherAmount;
						$discountVoucher = $giftVoucherAmount;
					}else{
						$voucherMessage = 'Please add at least one item into cart of cost rs 1000 or more!';
					}
				}
				
				$pbPointsAmount = $wallet['pbPointsAmount'];
				if( $totalAmountAfterDiscount <= 499 ){
					$pbPointsAmount = ($pbPointsAmount/20); //5% of wallets points
				}else if( ( ($totalAmountAfterDiscount > 499) && ($totalAmountAfterDiscount <= 999) ) ){
					$pbPointsAmount = ($pbPointsAmount/10); //10% of wallets points
				}else if( ( ($totalAmountAfterDiscount > 999) && ($totalAmountAfterDiscount <= 1999) ) ){
					$pbPointsAmount = (($pbPointsAmount*15)/100); //15% of wallets points
				}else{
					$pbPointsAmount = ($pbPointsAmount/5); //20% of wallets points
				}
				if($pbPointsStatus == "true"){
					$totalAmountAfterDiscount = $totalAmountAfterDiscount - $pbPointsAmount;
					$discountPoints = $pbPointsAmount;
				}
				
				$pbCashAmount = $wallet['pbCash'];
				if($pbCashStatus == "true"){
					$totalAmountAfterDiscount = $totalAmountAfterDiscount - $pbCashAmount;
					$discountCash = $pbCashAmount;
				}
				$grandTotalBeforeShipping = $totalAmountAfterDiscount;
				$cartQty = array_column($cart, 'cart_qty');
				$cartQty = array_sum($cartQty);
				switch($cartQty){
					case 1 : $shippingAmount = 45; break;
					case 2 : $shippingAmount = 75; break;
					case 3 : $shippingAmount = 100; break;
					default : $shippingAmount = 100 + (($cartQty-3)*30);
				}
				$discountAmount = $discountVoucher + $discountPoints + $discountCash;
				$grandTotal = $grandTotalBeforeShipping + $shippingAmount;
				if( $paymentMethod == 'cod' ){ $codAmount = 20; }
				$grandFinalTotal = $grandTotal + $codAmount;
			}
			$data = [
				'cart'=>$cart,
				'coupon_code'=>$couponCode,
				'discount_amount'=>number_format($discountAmount,2),
				'total_amount_of_cart'=>number_format($totalAmountOfCart,2),
				'total_amount_after_discount'=>number_format($totalAmountAfterDiscount,2),
				'voucher_message'=>$voucherMessage,
				'gift_voucher_amount'=>number_format($giftVoucherAmount,2),
				'discount_voucher'=>number_format($discountVoucher,2),
				'pb_points_amount'=>number_format($pbPointsAmount,2),
				'discount_points'=>number_format($discountPoints,2),
				'pb_cash_amount'=>number_format($pbCashAmount,2),
				'discount_cash'=>number_format($discountCash,2),
				'grand_total_before_shipping'=>number_format($grandTotalBeforeShipping,2),
				'shipping_amount'=>number_format($shippingAmount,2),
				'grand_total'=>number_format($grandTotal,2),
				'cod_amount'=>number_format($codAmount,2),
				'payment_method'=>$paymentMethod,
				'grand_final_total'=>number_format($grandFinalTotal,2),
			];
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }		
    
    public function addIntoCart()
    {
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['post']) && $auth['status']) {
			$itemId 	 = $this->request->getData('itemId');
			$qty 		 = $this->request->getData('qty');
			$info 		 = $this->Store->addItemIntoCart($userId, $itemId, $qty);
			$message 	 = $info['message'];
			$status 	 = $info['status'];
			$cart 		 = $this->Store->getActiveCart($userId);
			$data['cart']= $cart['cart'] ?? [];
    	}else{
			$message = 'Sorry, invalid request!';
		}		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }		
    
    
    public function updateCart()
    {
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['put']) && $auth['status']) {
			$id = $this->request->getData('id');
			$qty = $this->request->getData('qty');
			if( is_numeric($qty) && ($qty > 0) ){
				$status = $this->Store->updateItemIntoCart($id, $qty);
				$message = ($status) ?  'Item updated into cart!' : 'Sorry, Item not updated into cart!';
				$cart 		 = $this->Store->getActiveCart($userId);
				$data['cart']= $cart['cart'] ?? [];
			}else{
				$message = 'Sorry, Qunatity should be greater than zero!';
			}
    	}else{
			$message = 'Sorry, invalid request!';
		}		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }		
    
    public function removeItemFromCart()
    {
		$userId = $this->request->getQuery('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['delete']) && $auth['status']) {
			$id = $this->request->getQuery('id');
			$status = $this->Store->revomeItemFromCart($id);
			$message = ($status) ?  'Item removed from cart!' : 'Sorry, Item not remove from cart!';
			$cart 		 = $this->Store->getActiveCart($userId);
			$data['cart']= $cart['cart'] ?? [];
		}else{
			$message = 'Sorry, invalid request!';
		}		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }		
    
    public function getPincode()
    {
		$data = [];
		$message = '';
		$status = false;
		$pincode = $this->request->getQuery('pincode');
    	if ( $this->request->is(['get']) && is_numeric($pincode) && (strlen($pincode) == 6) ) {
			$response = $this->Delhivery->getPincode($pincode);
    	}else{
			$message = 'Sorry, invalid pincode request!';
			$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		}
    	echo json_encode($response);
		die;
    }		

    public function getOtp()
    {
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['post']) && $auth['status']) {
			$mobile = $this->request->getData('mobile');
			$amount = $this->request->getData('amount');
			$name = $this->request->getData('name');
			$email = $this->request->getData('email');
			$otp = $this->Sms->generateOtp(); //param are length, default is 6 digit code
			$info = $this->Sms->otpSend($mobile, $otp, $amount);
			if($info['status'] == 'OK'){
				$status = true;
				$message = 'We have sent OTP on entered mobile number and registered email id, if not received within 3 minutes please click on resend otp!';
				$data = [
					'otp' => $otp
				];
				$otpMailer = [
					'customerId ='> $userId,
					'otp' => $otp,
					'name' => $name,
					'email' => $email,
					'amount' => $amount,
					'mobile' => $mobile
				];
				if( $this->CommonLogic->getDmainEmailStatus() ){
					$this->getMailer('Customer')->send('otpSend', [$otpMailer]);
				}
			}else{
				$message = $info['message'];
			}
    	}else{
			$message = 'Sorry, invalid request!'.$userId;
		}		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }

	//###########Please write code for all customer api after this ///////////////############################################		
    public function getActiveCart()
    {
		$userId				= $this->request->getQuery('userId','0'); //16597
		$auth 				= $this->Store->isAuth($userId);
		
		$data 				= [];
		$message 			= '';
		$status 			= false;
    	if($auth['status'])
		{
			$inputData = [
				'customer_id' => $userId,
				'payment_method' => $this->request->getData('paymentMethod', '2'),
				'pincode' => $this->request->getData('pincode', ''),
				'coupon_code' => $this->request->getData('couponCode', ''),
				'gift_voucher_status' => $this->request->getData('giftVoucherStatus', '1'),
				'pb_points_status' => $this->request->getData('pbPointsStatus', '0'),
				'pb_cash_status' => $this->request->getData('pbCashStatus', '1'),
				'pb_prive' => $this->request->getData('pbPrive', '0')
			];			
			$data				= $this->Store->getActiveCartDetails($inputData);
			$status 			= true;
			$page = $this->request->getData('trackPage', NULL);
			$this->Customer->trackPage($userId, $page);
			//unset($data['cart'],$data['payment_method_data']);
			//echo json_encode($data); 
			//pr($data); 
			//die;
    	}
		else
		{
			$message 	= 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		//pr($response);
    	echo json_encode($response);
		die;
    }
    
    public function removeProductFromCart()
    {
		$userId	= $this->request->getQuery('userId');
		$auth 	= $this->Store->isAuth($userId);
		
		$data 		= [];
		$message 	= '';
		$status 	= false;
    	if ($this->request->is(['delete']) && $auth['status'])
		{
			$cartId 	= $this->request->getQuery('cartId');
			$status 	= $this->Store->revomeItemFromCart($cartId);
			$message 	= ($status) ?  'Item removed from cart!' : 'Sorry, Item not remove from cart!';
    	}
		else
		{
			$message	= 'Sorry, invalid request!';
		}		
		$response	= ['message' => $message, 'status' => $status, 'data' => $data];
    	echo json_encode($response);
		die;
    }
	
    public function updateProductQuantity()
    {
		$userId 	= $this->request->getData('userId');
		$auth 		= $this->Store->isAuth($userId);
		
		$data 		= [];
		$message 	= '';
		$status 	= false;
    	if ($this->request->is(['put']) && $auth['status'])
		{
			$cartId 	= $this->request->getData('cartId');
			$quantity 	= $this->request->getData('qty');
			if($cartId > 0 && $quantity > 0)
			{
				$status 	= $this->Store->updateItemIntoCart($cartId, $quantity);
				$message 	= ($status) ?  'Item updated into cart!' : 'Sorry, Item not updated into cart!';
			}
			else
			{
				$message	= 'Sorry, Qunatity should be greater than zero!';
			}
    	}
		else
		{
			$message	= 'Sorry, invalid request!';
		}		
		$response	= ['message' => $message, 'status' => $status, 'data' => $data];
    	echo json_encode($response);
		die;
    }
    
	public function changeUserAccount()
	{
		$userId		= $this->request->getQuery('userId');
		$username	= $this->request->getQuery('username');
		$password	= $this->request->getQuery('password');
		
		$data 		= [];
		$message 	= '';
		$status 	= false;
    	if(($this->request->is(['put']) || $this->request->is(['post'])) && $userId > 0 && $username != '' && $password != '')
		{
			$data	= $this->Customer->validateLogin($username, $password);
			if(count($data) > 0)
			{
				$updateCart	= $this->Store->updateCartAccount($userId, $data['id']);
				if($updateCart)
				{
					$status		= true;
				}
				else
				{
					$message	= 'Sorry, Unable to change account. Please try again later!';
				}
			}
			else
			{
				$message	= 'Sorry, Please enter valid username and password!';
			}
    	}
		else
		{
			$message	= 'Sorry, invalid request!';
		}		
		$response	= ['message' => $message, 'status' => $status, 'data' => $data];
    	echo json_encode($response);
		die;
	}
	
	public function createOrder()
	{
		$data 			= [];
		$message 		= '';
		$status 		= false;
		$is_error		= 0;
		
		$userId			= $this->request->getData('userId');
		if($this->request->is(['post']) && $userId > 0)
		{
			$auth 		= $this->Store->isAuth($userId);
			if($auth['status'])
			{
				$customerTable 				= TableRegistry::get('Customers');
				$getCustomerData			= $customerTable->get($userId);
				
				$shipping_address			= $this->request->getData('shipping_address');
				$shipping_city				= $this->request->getData('shipping_city');
				$shipping_state				= '';
				$shipping_country			= '';
				$shipping_email				= $this->request->getData('shipping_email');
				$shipping_firstname			= $this->request->getData('shipping_firstname');
				$shipping_lastname			= $this->request->getData('shipping_lastname');
				$shipping_location_id		= $this->request->getData('shipping_location_id');
				$shipping_mobile			= $this->request->getData('shipping_mobile');
				$shipping_pincode			= $this->request->getData('shipping_pincode');
				if($shipping_location_id > 0)
				{
					$shipping_pincode_detail= $this->CommonLogic->getLocationDetails($shipping_location_id);
					$shipping_state			= $shipping_pincode_detail['state'];
					$shipping_country		= $shipping_pincode_detail['country'];
				}
				
				$same_address				= $this->request->getData('same_address');
				if($same_address == 1)
				{
					$billing_address			= $shipping_address;
					$billing_city				= $shipping_city;
					$billing_state				= $shipping_state;
					$billing_country			= $shipping_country;
					$billing_email				= $shipping_email;
					$billing_firstname			= $shipping_firstname;
					$billing_lastname			= $shipping_lastname;
					$billing_location_id		= $shipping_location_id;
					$billing_mobile				= $shipping_mobile;
					$billing_pincode			= $shipping_pincode;
				}
				else
				{
					$billing_address			= $this->request->getData('billing_address');
					$billing_city				= $this->request->getData('billing_city');
					$billing_state				= '';
					$billing_country			= '';
					$billing_email				= $this->request->getData('billing_email');
					$billing_firstname			= $this->request->getData('billing_firstname');
					$billing_lastname			= $this->request->getData('billing_lastname');
					$billing_location_id		= $this->request->getData('billing_location_id');
					$billing_mobile				= $this->request->getData('billing_mobile');
					$billing_pincode			= $this->request->getData('billing_pincode');
					if($billing_location_id > 0)
					{
						$billing_pincode_detail	= $this->CommonLogic->getLocationDetails($billing_location_id);
						$billing_state			= $billing_pincode_detail['state'];
						$billing_country		= $billing_pincode_detail['country'];
					}
				}
				$couponCode					= $this->request->getData('couponCode');
				$paymentMethod				= $this->request->getData('paymentMethod');
				if($paymentMethod == 'payu')
					$paymentMethod	= 2;
				else if($paymentMethod == 'paytm')
					$paymentMethod	= 3;
				else if($paymentMethod == 'cod')
					$paymentMethod	= 1;
				$giftVoucherStatus			= $this->request->getData('giftVoucherStatus');
				$pbCashStatus				= $this->request->getData('pbCashStatus');
				$pbPointsStatus				= $this->request->getData('pbPointsStatus');
				
				if($shipping_firstname == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping first name.";
				}
				else if($shipping_lastname == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping last name.";
				}
				else if($shipping_address == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping address.";
				}
				else if( strpos($shipping_address, "&") != FALSE )
				{
					$is_error	= 1;
					$message	= "Please remove & char from shipping address.";
				}
				else if($shipping_pincode == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping pincode.";
				}
				else if($shipping_city == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping city.";
				}
				else if($shipping_location_id == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping state.";
				}
				else if($shipping_email == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping email.";
				}
				else if($shipping_mobile == '')
				{
					$is_error	= 1;
					$message	= "Please enter your shipping mobile.";
				}
				else if(!$same_address)
				{
					if($billing_firstname == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing first name.";
					}
					else if($billing_lastname == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing last name.";
					}
					else if($billing_address == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing address.";
					}
					else if( strpos($billing_address, "&") != FALSE )
					{
						$is_error	= 1;
						$message	= "Please remove & char from billing address.";
					}
					else if($billing_pincode == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing pincode.";
					}
					else if($billing_city == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing city.";
					}
					else if($billing_location_id == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing state.";
					}
					else if($billing_email == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing email.";
					}
					else if($billing_mobile == '')
					{
						$is_error	= 1;
						$message	= "Please enter your billing mobile.";
					}
				}
				
				if($paymentMethod == '')
				{
					$is_error	= 1;
					$message	= "Please select a payment method.";
				}
				
				if($is_error == 0)
				{
					$inputData = [
						'customer_id' => $userId,
						'payment_method' => $paymentMethod,
						'pincode' => $this->request->getData('pincode', ''),
						'coupon_code' => $couponCode,
						'gift_voucher_status' => $giftVoucherStatus,
						'pb_points_status' => $pbPointsStatus,
						'pb_cash_status' => $pbCashStatus,
						'pb_prive' => $this->request->getData('pbPrive', '0')
					];			
					$cartAmountDetails			= $this->Store->getActiveCartDetails($inputData);
					$orderLog					= $cartAmountDetails;
					unset($orderLog['cart'], $orderLog['payment_method_data']);
					$orderLog					= json_encode($orderLog);

					$orderTable 				= TableRegistry::get('Orders');
					$order 						= $orderTable->newEntity();
					$order->customer_id 		= $userId;
					$order->payment_method_id 	= $cartAmountDetails['payment_method'];
					$order->payment_mode 		= ($cartAmountDetails['payment_method'] == 1) ? 'postpaid':'prepaid';
					$order->product_total 		= $cartAmountDetails['total_amount_after_discount'];
					$order->payment_amount 		= $cartAmountDetails['grand_final_total'];
					$order->discount 			= $cartAmountDetails['discounts']['amount'];
					$order->ship_amount 		= $cartAmountDetails['shipping_amount'];
					$order->ship_discount 		= $cartAmountDetails['discounts']['extra'];
					$order->mode_amount 		= $cartAmountDetails['payment_fees'];
					$order->coupon_code 		= $couponCode;
					$order->mobile 				= $getCustomerData->mobile;
					$order->email 				= $getCustomerData->email;
					$order->credit_mailer 		= 0;
					$order->status 				= 'pending';
					$order->shipping_firstname 	= $shipping_firstname;
					$order->shipping_lastname 	= $shipping_lastname;
					$order->shipping_address 	= $shipping_address;
					$order->shipping_city 		= $shipping_city;
					$order->shipping_state 		= $shipping_state;
					$order->shipping_country 	= $shipping_country;
					$order->shipping_pincode 	= $shipping_pincode;
					$order->shipping_email 		= $shipping_email;
					$order->shipping_phone 		= $shipping_mobile;
					$order->billing_firstname 	= $billing_firstname;
					$order->billing_lastname 	= $billing_lastname;
					$order->billing_address 	= $billing_address;
					$order->billing_city 		= $billing_city;
					$order->billing_state 		= $billing_state;
					$order->billing_country 	= $billing_country;
					$order->billing_pincode 	= $billing_pincode;
					$order->billing_email 		= $billing_email;
					$order->billing_phone 		= $billing_mobile;
					$order->gift_voucher_amount = $cartAmountDetails['discounts']['voucher'];
					$order->pb_points_amount 	= $cartAmountDetails['discounts']['points'];
					$order->pb_cash_amount 		= $cartAmountDetails['discounts']['cash'];
					$order->credit_gift_amount 	= $cartAmountDetails['credits']['voucher'];
					$order->credit_points_amount= $cartAmountDetails['credits']['points'];
					$order->credit_cash_amount 	= $cartAmountDetails['credits']['cash'];
					$order->delhivery_response 	= '';
					$order->packing_slip 		= '';
					$order->cancel_response 	= '';
					$order->order_log 			= $orderLog;
					$order->transaction_ip 		= $_SERVER['REMOTE_ADDR'];
					$orderArray					= $orderTable->save($order)->toArray();
					$order_id					= $orderArray['id'];
					$order->order_number		= $order_id;
					$orderTable->save($order);
					
					$status						= true;
					if($order_id > 0)
					{
						if(count($cartAmountDetails['cart']['cart']) > 0)
						{
							$orderDetailTable	= TableRegistry::get('OrderDetails');
							foreach($cartAmountDetails['cart']['cart'] as $cart_item)
							{
								$order_detail 	= $orderDetailTable->newEntity();
								$order_detail->order_id				= $order_id;
								$order_detail->product_id			= $cart_item['id'];
								$order_detail->title				= $cart_item['title'];
								$order_detail->sku_code				= $cart_item['sku_code'];
								$order_detail->size					= $cart_item['size'].' '.strtoupper($cart_item['size_unit']);   //size_unit
								$order_detail->price				= $cart_item['price'];
								$order_detail->discount				= 0;
								$order_detail->tax_amount			= 0;
								$order_detail->qty					= $cart_item['cart_qty'];
								$order_detail->short_description	= $cart_item['description'];
								$orderDetailTable->save($order_detail);
								
								$this->Store->revomeItemFromCart($cart_item['cart_id']);
							}
						}
						
						$data['orderNumber']		= $order_id;
						$data['shippingState']		= $shipping_state;
						$data['shippingCountry']	= $shipping_country;
					}
					else
					{
						$message	= "Unable to place order. Please try again later.";
					}
					try{
						$customerTable 				= TableRegistry::get('Customers');
						$customer 					= $customerTable->get($userId);
						if( empty($customer->firstname) ){
							$customer->firstname = $shipping_firstname;
						}
						if( empty($customer->lastname) ){
							$customer->lastname = $shipping_lastname;
						}
						if( empty($customer->address) ){
							$customer->address = $shipping_address;
						}
						if( empty($customer->city) ){
							$customer->city = $shipping_city;
						}
						if( $customer->location_id == 0 ){
							$customer->location_id = $shipping_location_id;
						}
						if( empty($customer->pincode) ){
							$customer->pincode = $shipping_pincode;
						}
						$customerTable->save($customer);
					}catch(\Exception $e){

					}
				}
			}
			else
			{
				$message	= "Sorry, Invalid request!";
			}
		}
		else
		{
			$message	= "Sorry, Invalid request!";
		}
		$response	= ['message' => $message, 'status' => $status, 'data' => $data];
    	echo json_encode($response);
		die;
	}
	
	public function updateOrderDetailsAfterPGOld() //this is new update code for order status
	{
		$data 			= [];
		$message 		= '';
		$status 		= false;
		
		$userId			= $this->request->getData('userId');
		$action			= $this->request->getData('action');
		$orderNumber	= $this->request->getData('orderNumber');
		$pgStatus   	= $this->request->getData('pgStatus');
		$pgName			= $this->request->getData('pgName');
		$pgData			= $this->request->getData('pgData');
		
		$getStatus		= "";
		if($this->request->is(['post']) && $action && ($orderNumber > 0))
		{
			$pgData = $this->Store->pgResEnDe($pgData,'d');
			$this->Store->pgResposes($orderNumber, $pgName, $pgData);
			
			$orderTable 	= TableRegistry::get('Orders');
			$order			= $orderTable->get($orderNumber);
			switch($pgName)
			{
				case 'mobikwik':
					if( in_array($pgStatus, [100,601]) ){
						$getStatus = 'accepted';
					}else{
						$getStatus = 'pending';
					}
					break;
				case 'payu':
					switch($pgStatus){
						case 100 : $getStatus = 'accepted'; break;
						case 300 : $getStatus = 'paymentfail'; break;
						default:   $getStatus = 'pending';
					}
					break;
				case 'paytm':
					switch($pgStatus){
						case 100 : $getStatus = 'accepted'; break;
						case 300 : $getStatus = 'paymentfail'; break;
						default:   $getStatus = 'pending';
					}
					break;
				case 'cod':
					$getStatus 		= 'accepted';
					break;
				default:	
			}
			
			if( in_array($getStatus, ['accepted','paymentfail','pending']) )
			{
				$oDetails 	= $this->Customer->getOrdersDetails($orderNumber);
				//pr($oDetails);
				$order->status	= $getStatus;
				//$order->is_processed	= 0;
				if( count($oDetails) == 0 )
				{
					$message	= 'Sorry, customer not belong to this order number!';
				}else if( $orderTable->save($order) )
				{ 
					switch($getStatus)
					{
						case 'accepted':
							$message	= 'Order accepted';
							if($order->is_processed == '0')
							{ 
								$order->is_processed	= 1;
								//$orderTable->save($order);

								//$this->Store->updateWalletAfterPayment($orderNumber);
								//$this->Store->updateStockAfterOrderPlaced($oDetails['details']);
								$this->Store->createInvoiceOld($orderNumber);
								
								if( $this->CommonLogic->getDmainEmailStatus() ){
									//$this->Store->sendOrderToDelhivery($orderNumber);
								}

								if( !empty($order->coupon_code) ){
									$customerTable 		= TableRegistry::get('Customers');
									$customer			= $customerTable->get($order->customer_id);
									$this->Coupon->inOrderStatus($order->coupon_code, $customer->email);
								}
								$log = json_decode($order->order_log,true);
								$prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
								if( $prive && ($order->payment_mode != 'postpaid') ){
									$memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
									$this->Membership->add($memberData);
								}														
							}
							break;
						case 'paymentfail':
							$message	= 'Order accepted but payment status fail!';
							break;
						default:
							$message	= 'Pending Order!';
					}
				}
				else
				{
					$message	= 'Pending Order';
				}
				if( !empty($oDetails) && ($getStatus == 'accepted') )
				{ /*
					$text = '';
					$total = count($oDetails['details']);
					if(count($oDetails['details']) > 1){
						$total = $total - 1;
						$text = $oDetails['details'][0]['title']." + $total";
					}else{
						$text = $oDetails['details'][0]['title'];
					}
				
					$this->Sms->orderSend($oDetails['shippingPhone'], $orderNumber, $oDetails['paymentAmount'], $oDetails['paymentMethodName'], $text);
					$oDetails['customerId'] = $userId;
					$this->Customer->trackPage($userId);

					if( $this->CommonLogic->getDmainEmailStatus() ){
						$this->getMailer('Customer')->send('orderConfirmed', [$oDetails]);	
					} */
				}
			}
			else
			{
				$message	= 'Invalid status of order!';
			}
			$status					= true;					
		}
		else
		{
			$message	= "Sorry, invalid request!";
		}
		$data['orderStatus']	= $message;
		$data['returnStatus']	= $getStatus;
		$data['orderNumber']	= $orderNumber;
		
		$response	= ['message' => $message, 'status' =>$status, 'data' =>$data];
    	echo json_encode($response);
		die;
	}
	
	public function updateOrderDetailsAfterPG() //this is new update code for order status
	{
		$data 			= [];
		$message 		= '';
		$status 		= false;
		
		$userId			= $this->request->getData('userId');
		$orderNumber	= $this->request->getData('orderNumber');
		$pgStatus   	= $this->request->getData('pgStatus');
		$pgName			= $this->request->getData('pgName');
		$pgData			= $this->request->getData('pgData');
		$auth 		 	= $this->Store->isAuth($userId);
		
		$getStatus		= "";
		if($this->request->is(['post']) && $auth['status'] && ($orderNumber > 0))
		{
			$pgData = $this->Store->pgResEnDe($pgData,'d');
			$this->Store->pgResposes($orderNumber, $pgName, $pgData);
			
			$orderTable 	= TableRegistry::get('Orders');
			$order			= $orderTable->get($orderNumber);
			switch($pgName)
			{
				case 'mobikwik':
					if( in_array($pgStatus, [100,601]) ){
						$getStatus = 'accepted';
					}else{
						$getStatus = 'pending';
					}
					break;
				case 'payu':
					switch($pgStatus){
						case 100 : $getStatus = 'accepted'; break;
						case 300 : $getStatus = 'paymentfail'; break;
						default:   $getStatus = 'pending';
					}
					break;
				case 'paytm':
					switch($pgStatus){
						case 100 : $getStatus = 'accepted'; break;
						case 300 : $getStatus = 'paymentfail'; break;
						default:   $getStatus = 'pending';
					}
					break;
				case 'cod':
					$getStatus 		= 'accepted';
					break;
				default:	
			}
			
			if( in_array($getStatus, ['accepted','paymentfail','pending']) )
			{
				$oDetails 	= $this->Customer->getOrdersDetails($orderNumber);
				//pr($oDetails);
				$order->status	= $getStatus;
				if( count($oDetails) == 0 )
				{
					$message	= 'Sorry, customer not belong to this order number!';
				}else if( $orderTable->save($order) )
				{
					switch($getStatus)
					{
						case 'accepted':
							$message	= 'Order accepted';
							if($order->is_processed == '0')
							{
								$order->is_processed	= 1;
								$orderTable->save($order);

								$this->Store->updateWalletAfterPayment($orderNumber);
								$this->Store->updateStockAfterOrderPlaced($oDetails['details']);
								$this->Store->createInvoice($orderNumber);
								
								if( $this->CommonLogic->getDmainEmailStatus() ){
									$this->Store->sendOrderToDelhivery($orderNumber);
								}

								if( !empty($order->coupon_code) ){
									$customerTable 		= TableRegistry::get('Customers');
									$customer			= $customerTable->get($order->customer_id);
									$this->Coupon->inOrderStatus($order->coupon_code, $customer->email);
								}
								$log = json_decode($order->order_log,true);
								$prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
								if( $prive && ($order->payment_mode != 'postpaid') ){
									$memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
									$this->Membership->add($memberData);
								}														
							}
							break;
						case 'paymentfail':
							$message	= 'Order accepted but payment status fail!';
							break;
						default:
							$message	= 'Pending Order!';
					}
				}
				else
				{
					$message	= 'Pending Order';
				}
				if( !empty($oDetails) && ($getStatus == 'accepted') )
				{
					$text = '';
					$total = count($oDetails['details']);
					if(count($oDetails['details']) > 1){
						$total = $total - 1;
						$text = $oDetails['details'][0]['title']." + $total";
					}else{
						$text = $oDetails['details'][0]['title'];
					}

					$this->Sms->orderSend($oDetails['shippingPhone'], $orderNumber, $oDetails['paymentAmount'], $oDetails['paymentMethodName'], $text);
					$oDetails['customerId'] = $userId;
					$this->Customer->trackPage($userId);

					if( $this->CommonLogic->getDmainEmailStatus() ){
						$this->getMailer('Customer')->send('orderConfirmed', [$oDetails]);	
					}
				}
			}
			else
			{
				$message	= 'Invalid status of order!';
			}
			$status					= true;					
		}
		else
		{
			$message	= "Sorry, invalid request!";
		}
		$data['orderStatus']	= $message;
		$data['returnStatus']	= $getStatus;
		$data['orderNumber']	= $orderNumber;
		
		$response	= ['message' => $message, 'status' =>$status, 'data' =>$data];
    	echo json_encode($response);
		die;
	}
	
	public function sendPickupRequest()
	{
		$total_package	= 0;
		$orderTable 	= TableRegistry::get('Orders');
		$orderData 		= $orderTable->find('all', ['fields' => ['id'], 'conditions' => ['status' => 'accepted', 'is_pickup_request_sent' => '0'], 'order' => ['id' => 'DESC']])->toArray();
		$total_package	= count($orderData);
		
		if($total_package > 0)
		{
			$result	= $this->Delhivery->sendPickupRequest($total_package, date('Ymd'), '23:00:00');
			if(isset($result['pickup_id']) && $result['pickup_id'] != '')
			{
				foreach($orderData as $temp_id)
				{
					$temp_array	= (array)json_decode($temp_id);
					$order		= $orderTable->get($temp_array['id']);
					$order->is_pickup_request_sent	= 1;
					$order->delhivery_pickup_id		= $result['pickup_id'];
					$orderTable->save($order);
				}
			}
		}
		die;
	}
	
	public function changeOrderStatusDelhivery()
	{
		$getRequest	= $this->request->input('json_decode');
		file_put_contents('/var/www/html/pb/src/Files/log_file/delhivery.txt', PHP_EOL.json_encode($getRequest), FILE_APPEND);
		file_put_contents('/var/www/html/pb/src/Files/log_file/delhivery.txt', PHP_EOL.json_encode($_POST), FILE_APPEND);
		if(isset($getRequest->Shipment->AWB))
		{
			$waybill		= $getRequest->Shipment->AWB;
			$statusType		= $getRequest->Shipment->Status->StatusType;
			$status			= $getRequest->Shipment->Status->Status;
			if($waybill != '')
			{
				$orderTable 	= TableRegistry::get('Orders');
				$orderData 		= $orderTable->find('all', ['fields' => ['id'], 'conditions' => ['tracking_code' => $waybill], 'order' => ['id' => 'DESC']])->toArray();
				$total_package	= count($orderData);
				
				if($total_package > 0)
				{
					foreach($orderData as $temp_id)
					{
						$newStatus	= '';
						$temp_array	= (array)json_decode($temp_id);
						$order		= $orderTable->get($temp_array['id']);
						if($status == 'Delivered')
						{
							$newStatus		= 'delivered';
							$order->status	= 'delivered';

							$orderTable->save($order);

							$log = json_decode($order->order_log,true);
							$voucher = isset($log['credits']['voucher']) ? $log['credits']['voucher'] : 0;
							if( $voucher && ($voucher%VOUCHER_501) ){
								$customerTable = TableRegistry::get('Customers');
								$customer	   = $customerTable->get($order->customer_id);
								$customer->scentshot_active = 1;
								$customerTable->save($customer);			
							}
							//for membership
							$prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
							if( $prive && ($order->payment_mode == 'postpaid') ){
								$memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
								$this->Membership->add($memberData);
							}		
																	
							$this->Store->updateWalletAfterDelivery($order->id);
							$this->Store->orderDelivered($order->id);
						}
						
						if($status == 'In Transit')
						{
							$currentOrderStatus = $order->status;
							$newStatus		= 'intransit';
							$order->status	= 'intransit';
							$orderTable->save($order);
							if( ($currentOrderStatus != 'intransit') && ($statusType != 'RT') ){
								$this->Store->orderIntransit($order->id);
							}
						}
						
						if($status == 'Manifested')
						{
							// $newStatus		= 'proccessing';
							// $order->status	= 'proccessing';
							// $orderTable->save($order);
						}
						
						if($status == 'Dispatched')
						{
							$newStatus		= 'dispatched';
							$order->status	= 'dispatched';
							$orderTable->save($order);
						}
						
						if($status == 'Cancelled')
						{
							$newStatus		= 'cancelled';
							$order->status	= 'cancelled';
							$orderTable->save($order);
						}
						
						if($status == 'RTO')
						{
							$newStatus		= 'rto';
							$order->status	= 'rto';
							$orderTable->save($order);
						}
						
						if($status == 'DTO')
						{
							$newStatus		= 'dto';
							$order->status	= 'dto';
							$orderTable->save($order);
						}
						
						if($newStatus != '')
						{
							$this->Store->changeInvoiceStatus($order->id, $newStatus);
						}
							
						$shippingLogTable				= TableRegistry::get('OrderShippingLogs');
						$shipping_log 					= $shippingLogTable->newEntity();
						$shipping_log->order_id			= $order->id;
						$shipping_log->response_data	= json_encode($getRequest);
						$shippingLogTable->save($shipping_log);
					}
				}
			}
		}
		echo true;
		die;
	}

	public function awb($id_order){
		if( $id_order > 0 ){
			$this->Store->sendOrderToDelhivery($id_order);
		}
		die;
	}
	
	public function getPkg($awb=0){
		if( $awb > 0 ){
			$this->Delhivery->getPackingSlipTest($awb);
		}
		die;
	}
	
	public function updatePkg($orderId=0, $awb=0){
		if( ($orderId > 0) && ($awb > 0) ){
			$status = $this->Store->sendOrderToDelhiveryTest($orderId, $awb);
			echo ( $status ==  1 ) ? 'Package updated.':'Sorry, record not update!';
		}
		die;
	}
	
	public function pgResponse(){
		$status = 0;
		if( $this->request->is('post') ){
			$orderId     = $this->request->getData('orderId');
			$pgName 	 = $this->request->getData('pgName');
			$pgData  	 = $this->request->getData('pgResponse');
			if( !empty($orderId) && !empty($pgName) ){
				$status = $this->Store->pgResposes($orderId, $pgName, $pgData);
			}
		}
		echo $status;
		die;
	}
	
}