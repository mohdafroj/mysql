<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception;
use Cake\Network\Exception\NotFoundException;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\Customer;
use App\Controller\Component\Delhivery;
use Cake\I18n\Date;
use Cake\Mailer\MailerAwareTrait;

/**
 * Admin component
 */
class StoreComponent extends Component
{
	use MailerAwareTrait;
	public function isAuth($userId)
	{
		$status 	= false;
		$data 		= [];
		$dbToken 	= '';
		$dbPassToken = '';
		$userToken 	= $this->request->getHeader('Authorization');
		$userToken 	= isset($userToken[0]) ? $userToken[0]:NULL;
		$dataTable 	= TableRegistry::get('Customers');
		$q 			= $dataTable->findByIdAndIsActive($userId, 'active')->toArray();
		if( isset($q[0]) )
		{
			$data 		= $q[0];
			$dbToken 	= $q[0]['api_token'];
			$dbPassToken = $q[0]['password'];
		}
		if( !empty($userToken) && ( ($dbToken === $userToken) || ($dbPassToken === $userToken) ) )
		{
			$status 	= true;
		}
		return ['status'=>$status, 'data'=>$data];
	}
	
	public function getCart($userId)
	{
		$data 		= [];
		$dataTable 	= TableRegistry::get('Products');
		$data 		= $dataTable->find('all', ['conditions'=>['is_stock' => 'in_stock', 'is_active'=>'active']])
				->matching("Carts", function($q) use ($userId){
					return $q->where(['Carts.customer_id'=>$userId]);
				})
				->toArray();
		return $data;
    }
	
	public function getItems($skus=[])
	{  
		$items = [];
		$orderSku = array_column($skus, 'sku_code');
		if(count($skus)){
			$productTable 		= TableRegistry::get('Products');
			$productImageTable	= TableRegistry::get('ProductsImages');
			$query		  		= $productTable->find('all', ['fields'=>['id','sku_code','short_description'],'conditions'=>['sku_code IN'=>$orderSku,'is_active'=>'active']])
								->hydrate(false)->toArray();
			foreach($query as $item)
			{
				$productImages 		= $productImageTable->find('all', ['fields'=>['id', 'title', 'alt'=>'alt_text', 'url'=>'img_large'], 'conditions' => ['is_large'=>'1', 'is_active' => 'active', 'product_id' => $item['id']]])->hydrate(false)->toArray();
				for($i=0; $i < count($skus); $i++){
					if( ($item['sku_code'] == $skus[$i]['sku_code']) && ($item['sku_code'] != 'PB00000122' ) ){
						$skus[$i]['images'] = $productImages;
						$skus[$i]['short_description'] = $item['short_description'];
						$items[] = $skus[$i];
						break;
					}
				}
			}
		}
		return $items;
    }
	
	public function getActiveCart($userId)
	{
		$cart_data 				= [];
		$cart_total				= 0;
		$cart_quantity			= 0;
		$product_cart_id		= 0;
		$has_voucher_product	= 0;
		$has_refill_product		= 0;
		$credit_voucher_501		= 0;
		$credit_voucher_100		= 0;
		$this->Product 			= new ProductComponent(new ComponentRegistry());
		$productImageTable		= TableRegistry::get('ProductsImages');
		$cartTable 				= TableRegistry::get('Carts');
		$productTable 			= TableRegistry::get('Products');
		$cartData				= $cartTable->find('all', ['conditions' => ['customer_id' => $userId]])->hydrate(false)->toArray();
		foreach($cartData as $temp_cart)
		{
			$product_cart_id = $temp_cart['id'];
			$tempProduct	 = $productTable->get($temp_cart['product_id'], ['contain'=>['Brands','ProductsCategories']])->toArray();
			//pr($tempProduct); die;
			if($tempProduct)
			{   
				if($tempProduct['is_stock'] == 'in_stock' && $tempProduct['is_active'] == 'active' && $tempProduct['qty'] >= $temp_cart['qty'] && $temp_cart['qty'] > 0)
				{
					$images 							= $productImageTable->find('all', ['fields' => ['id', 'title', 'alt'=>'alt_text', 'url'=>'img_small'], 'conditions' => ['is_small'=>'1', 'is_active' => 'active', 'product_id' => $tempProduct['id']]])->hydrate(false)->toArray();
					$category 							= array_column($tempProduct['products_categories'], 'category_id');
					$category 							= $this->Product->getCategory($category);
					$counter							= count($cart_data);
					$cart_data[$counter]['id']			= $tempProduct['id'];
					$cart_data[$counter]['name']		= $tempProduct['name'];
					$cart_data[$counter]['title']		= $tempProduct['title'];
					$cart_data[$counter]['sku_code']	= $tempProduct['sku_code'];
					$cart_data[$counter]['url_key']		= $tempProduct['url_key'];
					$cart_data[$counter]['size']		= $tempProduct['size'];
					$cart_data[$counter]['size_unit']	= $tempProduct['size_unit'];
					$cart_data[$counter]['cart_id']		= $temp_cart['id'];
					$cart_data[$counter]['cart_qty']	= $temp_cart['qty'];
					$cart_data[$counter]['price']		= $tempProduct['price'];
					$cart_data[$counter]['is_stock']	= true;
					$cart_data[$counter]['offer_price']	= $tempProduct['offer_price'];
					$cart_data[$counter]['offer_from']	= $tempProduct['offer_from'];
					$cart_data[$counter]['offer_to']	= $tempProduct['offer_to'];
					$cart_data[$counter]['gender']		= $tempProduct['gender'];
					$cart_data[$counter]['description']	= $tempProduct['short_description'];
					$cart_data[$counter]['category']	= $category;
					$cart_data[$counter]['images']		= $images;
					$cart_data[$counter]['brand']		= [];
					if( count($tempProduct['brand']) ){
						$cart_data[$counter]['brand']	= [
							'id'=>$tempProduct['brand']['id'],
							'title'=>$tempProduct['brand']['title'],
							'countryName'=>$tempProduct['brand']['country_name'],
							'image'=>$tempProduct['brand']['image']
						];
					}
					

					$cart_total							+= $temp_cart['qty']*$tempProduct['price'];
					$cart_quantity						+= $temp_cart['qty'];
					
					//only for perfume category and 1000 >= price
					foreach ( $tempProduct['products_categories'] as $ca ){
						if( ($ca['category_id'] == 5) && ($tempProduct['price'] >= VALID_VOUCHER_PRODUCT) ) { $has_voucher_product = 1; }
						if( $ca['category_id'] == REFILL_ID ) { $has_refill_product = $temp_cart['qty']; }
					}
					//For selfie products credits voucher
					if(in_array($tempProduct['id'], array(2, 3, 4, 5, 6, 7)))
					{
						$credit_voucher_501	+= $temp_cart['qty'];
					}

					//For scent shot products credits voucher
					if( in_array($tempProduct['id'], array(487, 488, 489, 490, 491, 492)) )
					{
						$credit_voucher_501		+= $temp_cart['qty'];
						$credit_voucher_100     += $temp_cart['qty'] * 3;
					}
				}
				else
				{
					$this->revomeItemFromCart($product_cart_id);
				}
			}
		}
		if( (count($cart_data) == 1) && ($cart_data[0]['id'] == PRIVE_PRODUCT_ID) ){
			$this->revomeItemFromCart($product_cart_id);
			$cart_data 				= [];
			$cart_total				= 0;
			$cart_quantity			= 0;
			$product_cart_id		= 0;
			$has_voucher_product	= 0;
			$has_refill_product		= 0;
			$credit_voucher_501		= 0;
			$credit_voucher_100		= 0;
		}
		return [
			'cart' => $cart_data, 
			'cart_total' => $cart_total, 
			'cart_quantity' => $cart_quantity, 
			'has_voucher_product' => $has_voucher_product, 
			'credit_voucher_quantity' => $credit_voucher_501,
			'has_refill_product' => $has_refill_product, 
			'credit_voucher_100' => $credit_voucher_100
		];
    }
	
	public function getAbendedCart($userId)
	{
		$cart_data 				= array();
		$cart_total				= 0;
		$has_voucher_product	= 0;
		$cart_quantity			= 0;
		$total_voucher_quantity	= 0;
		
		$cartTable 				= TableRegistry::get('Carts');
		$productTable 			= TableRegistry::get('Products');
		$cartData				= $cartTable->find('all', ['conditions' => ['customer_id' => $userId]])->toArray();
		foreach($cartData as $temp_cart)
		{
			$tempProduct	= $productTable->get($temp_cart['product_id']);
			if($tempProduct)
			{
				if($tempProduct->is_stock == 'in_stock' && $tempProduct->is_active == 'active' && $tempProduct->qty >= $temp_cart['qty'] && $temp_cart['qty'] > 0)
				{
					$productImageTable	= TableRegistry::get('ProductsImages');
					$productImages 		= $productImageTable->find('all', ['fields' => ['id', 'title', 'alt_text', 'img_base'], 'conditions' => ['is_base'=>'1', 'is_active' => 'active', 'product_id' => $tempProduct->id]])->toArray();
					
					$counter							= count($cart_data);
					$cart_data[$counter]['id']			= $tempProduct->id;
					$cart_data[$counter]['name']		= $tempProduct->name;
					$cart_data[$counter]['title']		= $tempProduct->title;
					$cart_data[$counter]['sku_code']	= $tempProduct->sku_code;
					$cart_data[$counter]['url_key']		= $tempProduct->url_key;
					$cart_data[$counter]['size']		= $tempProduct->size;
					$cart_data[$counter]['size_unit']	= $tempProduct->size_unit;
					$cart_data[$counter]['cart_id']		= $temp_cart['id'];
					$cart_data[$counter]['cart_qty']	= $temp_cart['qty'];
					$cart_data[$counter]['price']		= $tempProduct->price;
					if($tempProduct->is_stock == 'in_stock')
					{
						$cart_data[$counter]['is_stock']	= true;
					}
					else
					{
						$cart_data[$counter]['is_stock']	= false;
					}
					$cart_data[$counter]['tax_title']	= 'GST';
					$cart_data[$counter]['tax_value']	= 18;
					$cart_data[$counter]['tax_type']	= '%';
					$cart_data[$counter]['offer_price']	= $tempProduct->offer_price;
					$cart_data[$counter]['offer_from']	= $tempProduct->offer_from;
					$cart_data[$counter]['offer_to']	= $tempProduct->offer_to;
					$cart_data[$counter]['gender']		= $tempProduct->gender;
					$cart_data[$counter]['description']	= $tempProduct->short_description;
					
					$imageArray	= array();
					foreach($productImages as $temp_image)
					{
						$imageCounter	= 0;
						$imageArray[$imageCounter]['id']	= $temp_image->id;
						$imageArray[$imageCounter]['alt']	= $temp_image->alt_text;
						$imageArray[$imageCounter]['title']	= $temp_image->title;
						$imageArray[$imageCounter]['url']	= $temp_image->img_base;
					}
					
					$cart_data[$counter]['images']		= $imageArray;
					$cart_total							+= $temp_cart['qty']*$tempProduct->price;
					if($tempProduct->price >= VALID_VOUCHER_PRODUCT)
					{
						$has_voucher_product	= 1;
					}
					$cart_quantity						+= $temp_cart['qty'];
					
					if(in_array($tempProduct->id, array(2, 3, 4, 5, 6, 7)))
					{
						$total_voucher_quantity	+= $temp_cart['qty'];
					}
				}
				else
				{
					$this->revomeItemFromCart($temp_cart['id']);
				}
			}
		}
		return array('cart' => $cart_data, 'cart_total' => $cart_total, 'has_voucher_product' => $has_voucher_product, 'cart_quantity' => $cart_quantity, 'credit_voucher_quantity' => $total_voucher_quantity);
    }
	
	public function addItemIntoCart($userId, $itemId, $qty) {
		$message = 'Sorry, Invalid data!'; $status = 0;
		if( ($userId > 0) && ($itemId > 0) && ($qty > 0) ){
			$cartTable = TableRegistry::get('Carts');
			$query = $cartTable->find('all', ['conditions'=>['customer_id'=>$userId,'product_id'=>$itemId]])->toArray();
			if(count($query) > 0){
				$message = 'Sorry, Item already exists into your cart!';
			}else{
				$checkRefillStatus = 1;
				$customerTable 	= TableRegistry::get('Customers');
				$customer 		= $customerTable->get($userId);
				if( !$customer->scentshot_active ){
					$productTable 	= TableRegistry::get('Products');
					$product 		= $productTable->get($itemId, ['contain'=>['ProductsCategories'],'fields'=>['id']])->toArray();
					foreach($product['products_categories'] as $va){
						if($va['category_id'] == REFILL_ID ){ $checkRefillStatus = 0; }
					}
				}
				if( $checkRefillStatus ){
					$cart = $cartTable->newEntity();
					$cart->customer_id = $userId;
					$cart->product_id = $itemId;
					$cart->qty = $qty;
					$status = ($cartTable->save($cart)) ? 1 : 0;
					$message = $status ? 'One item added into cart!':'Sorry, try aogain!';
				}else{
					$message = 'Sorry, you can not buy a refill, Please buy Scent Shot!';
				}
			}
		}
		return ['message'=>$message, 'status'=>$status];
    }	
	
	public function updateItemIntoCart($id, $qty)
	{
		$dataTable 	= TableRegistry::get('Carts');
		$cart 		= $dataTable->get($id);
		$cart->qty	= $qty;
		return ($dataTable->save($cart)) ? true : false;
    }
	
	public function revomeItemFromCart($id)
	{
		$status = false;
		try{
			$dataTable 	= TableRegistry::get('Carts');
			$cart 		= $dataTable->get($id);
			$status = ($dataTable->delete($cart)) ? true : false;
		}catch( \Exception $ex ){

		}
		return $status;
    }
	
	public function updateStockAfterOrderPlaced($details)
	{
		$productsTable = TableRegistry::get('Products');
		
		foreach( $details as $value ){
			$product		= $productsTable->get($value['productId']);
			
			$remainQty		= $product->qty - $value['qty'];			
			if( $remainQty <= $product->out_stock_qty){
				$product->is_stock = 'out_of_stock';
			}
			$product->qty = $remainQty;
			$product->best_seller = $product->best_seller + 1;
			$productsTable->save($product);
		}
		return true;
	}
	
	public function updateStockAfterOrderCancel($details)
	{
		$productsTable = TableRegistry::get('Products');
		
		foreach( $details as $value ){
			$product		= $productsTable->get($value['productId']);
			
			$remainQty		= $product->qty + $value['qty'];			
			if( $remainQty > $product->out_stock_qty){
				$product->is_stock = 'in_stock';
			}
			$product->qty = $remainQty;
			$productsTable->save($product);
		}
		return true;
	}
	
	public function calculateShippingFromQuantity($quantity)
	{
		if($quantity == 1)
		{
			return 45;
		}
		else if($quantity == 2)
		{
			return 75;
		}
		else if($quantity == 3)
		{
			return 100;
		}
		else if($quantity > 3)
		{
			return 100 + ($quantity - 3)*30;
		}
		else
		{
			return 0;
		}
	}
	
	public function calculateDiscountFromQuantity($quantity)
	{
		$total_discount	= (int)($quantity/3);
		$total_discount	= $total_discount * 120;
		return $total_discount;
	}

	public function calculateTaxes($amount)
	{
		$getTax	= ($amount*18/100);
		return round($getTax, 2);
	}
	
	public function updateCartAccount($userId, $newUserId)
	{
		$cartTable 	= TableRegistry::get('Carts');
		$cartData	= $cartTable->find('all', ['conditions' => ['customer_id' => $userId]])->toArray();
		foreach($cartData as $temp_cart)
		{
			$cart 				= $dataTable->get($temp_cart['id']);
			$cart->customer_id	= $newUserId;
			$updateStatus		= $dataTable->save($cart);
			if(!$updateStatus)
				return false;
		}
		return true;
    }
	
	public function getCreditDetailsAfterOrder($order_amount, $credit_voucher_quantity)
	{
		$gift_voucher_amount	= $credit_voucher_quantity * 501;
		$pb_points_amount		= round(($order_amount*PB_POINTS_REUTRN)/100, 2);
		$pb_cash_amount			= 0; //round(($order_amount*PB_CASH_REUTRN)/100, 2);
		
		return array('gift_voucher_amount' => $gift_voucher_amount, 'pb_points_amount' => $pb_points_amount, 'pb_cash_amount' => $pb_cash_amount);
	}
	
	public function getCODAmount($order_amount)
	{
		if($order_amount > 0)
			return COD_AMOUNT;
		return 0;
	}
	
	public function getDefaultPaymentGateway()
	{
		$paymentTable 	= TableRegistry::get('PaymentMethods');
		$paymentData 	= $paymentTable->find('all', ['fields' => ['id'], 'conditions' => ['status'=>'1', 'active_default' => '1']])->toArray();
		foreach($paymentData as $temp_method)
		{
			return $temp_method['id'];
		}
		return 0;
	}
	
	public function getActivePaymentGatewayData()
	{
		$payment_gateway	= array();
		$paymentTable 		= TableRegistry::get('PaymentMethods');
		$paymentData		= $paymentTable->find('all', ['conditions' => ['status' => '1']])->order(['sort_order'=>'ASC'])->toArray();
		$counter			= 0;
		foreach($paymentData as $temp_method)
		{
			$payment_gateway[$counter]['id']	= $temp_method['id'];
			$payment_gateway[$counter]['title']	= $temp_method['title'];
			$payment_gateway[$counter]['fees']	= $temp_method['fees'];
			$counter++;
		}
		return $payment_gateway;
	}
	
	function getPaymentFee($payment_id)
	{
		$paymentTable 	= TableRegistry::get('PaymentMethods');
		$paymentData	= $paymentTable->find('all', ['conditions' => ['status' => '1', 'id' => $payment_id]])->toArray();
		if(count($paymentData) > 0)
		{
			return $paymentData[0]->fees;
		}
		return 0;
	}
	
	public function getActiveCartDetails($inputData)
	{
		$userId 			= $inputData['customer_id'];
		$payment_method 	= $inputData['payment_method'];
		$couponCode 		= $inputData['coupon_code'];
		$giftVoucherStatus 	= $inputData['gift_voucher_status'];
		$pbPointsStatus 	= $inputData['pb_points_status'];
		$pbCashStatus 		= $inputData['pb_cash_status'];
		$pbPrive 			= $inputData['pb_prive'];
		$number_format 		= isset($inputData['number_format'])    ? $inputData['number_format'] :0;
		
		$this->Customer 	= new CustomerComponent(new ComponentRegistry());		
		$data				= array();
		$customerTable 		= TableRegistry::get('Customers');
		$getCustomerData	= $customerTable->get($userId);
		
		$voucher_message				= '';
		$gift_voucher_amount			= $getCustomerData->voucher_amount;
		$pb_points_amount				= $getCustomerData->pb_points;
		$pb_cash_amount					= $getCustomerData->pb_cash;
		$discount_amount				= 0;
		$discount_points				= 0;
		$discount_cash					= 0;
		$credit_points_amount			= 0;
		$credit_cash_amount				= 0;
		$grand_total_before_shipping	= 0;
		$shipping_amount				= 0;
		$grand_total					= 0;
		$payment_fees					= 0;
		$grand_final_total				= 0;
		
		if( $pbPrive ){ //pr('add membership');
			$this->addItemIntoCart($userId, PRIVE_PRODUCT_ID, 1); //Add membership data to cart			
		}else{
			//Remove membership data from cart
			$cartTable 	= TableRegistry::get('Carts');
			$cartTable->query()->delete()->where(['customer_id'=>$userId,'product_id'=>PRIVE_PRODUCT_ID])->execute();
		}

		$cart_data 						= $this->getActiveCart($userId);
		$total_amount_of_cart			= $cart_data['cart_total'];
		$total_amount_after_discount	= $total_amount_of_cart;

		$products = [];
		foreach($cart_data['cart'] as $p ){
			$products[] = ['id'=>$p['id'],'qty'=>$p['cart_qty'],'price'=>$p['price']];
		}
		$this->Membership 	= new MembershipComponent(new ComponentRegistry());
		$prive				= $this->Membership->getMembership($userId,$products);
		
		if($total_amount_of_cart > 0)
		{
			//check voucher and refill quantity in customer wallets
			//$gift_voucher_amount = 601;
			$discount_voucher_qty_501 = 0;
			$discount_voucher_qty_100 = 0; 
			$customer_voucher_501 = $gift_voucher_amount%VOUCHER_100; ///
			$customer_voucher_100 =  ($gift_voucher_amount - $customer_voucher_501*VOUCHER_501)/VOUCHER_100;
			if ( $customer_voucher_100 && $cart_data['has_refill_product'] ) {
				$discount_voucher_qty_100 		 = ($cart_data['has_refill_product'] > $customer_voucher_100) ? $customer_voucher_100 : $cart_data['has_refill_product'];
				$discount_amount				+= $discount_voucher_qty_100 * VOUCHER_100;
				$total_amount_after_discount	-= $discount_voucher_qty_100 * VOUCHER_100;
			}
			//if request gift voucher 501 then apply
			if($giftVoucherStatus) {
				if ( $customer_voucher_501 > 0 ) {
					if($cart_data['has_voucher_product']){
						$discount_voucher_qty_501 		 = ($cart_data['has_voucher_product'] > $customer_voucher_501) ? $customer_voucher_501 : $cart_data['has_voucher_product'];
						$discount_amount				+= $discount_voucher_qty_501 * VOUCHER_501;
						$total_amount_after_discount	-= $discount_voucher_qty_501 * VOUCHER_501;						
						$voucher_message = "Gift Voucher applied successfully.";
					} else {
						$voucher_message = "Unable to apply Gift Voucher. Your cart doesn't have valid product to use voucher.";
					}
				} else {
					$voucher_message	 = "Unable to apply Gift Voucher. Your Gift Voucher balance is 0.";
				}
			}
			
			if( ($giftVoucherStatus && ($discount_voucher_qty_501 == 0) ) || ($pbPointsStatus && $pb_points_amount > 0 && $discount_voucher_qty_501 == 0))
			{
				if($total_amount_of_cart > 0 && $total_amount_of_cart < 500)
				{
					$discount_points	= ($total_amount_of_cart*PB_POINTS_DISCOUNT_1)/100;
				}
				else if($total_amount_of_cart >= 500 && $total_amount_of_cart < 1000)
				{
					$discount_points	= ($total_amount_of_cart*PB_POINTS_DISCOUNT_2)/100;
				}
				else if($total_amount_of_cart >= 1000 && $total_amount_of_cart < 2000)
				{
					$discount_points	= ($total_amount_of_cart*PB_POINTS_DISCOUNT_3)/100;
				}
				else if($total_amount_of_cart >= 2000)
				{
					$discount_points	= ($total_amount_of_cart*PB_POINTS_DISCOUNT_4)/100;
				}
				
				if($discount_points > $pb_points_amount)
				{
					$discount_points	= $pb_points_amount;
				}
				if($discount_points > $total_amount_after_discount)
				{
					$discount_points	= $total_amount_after_discount;
				}
				$discount_amount				+= $discount_points;
				$total_amount_after_discount	-= $discount_points;				
			}
			
			if($pbCashStatus && $pb_cash_amount > 0)
			{
				$discount_cash	= $pb_cash_amount;
				if($discount_cash > $total_amount_after_discount)
				{
					$discount_cash	= $total_amount_after_discount;
				}
				$discount_amount				+= $discount_cash;
				$total_amount_after_discount	-= $discount_cash;
			}
			
			if($cart_data['cart_quantity'] > 0)
			{
				$shipping_amount	= $this->calculateShippingFromQuantity($cart_data['cart_quantity']);
				$discount_extra		= 0;//$this->calculateDiscountFromQuantity($cart_data['cart_quantity']);
				$discount_amount	+= $discount_extra;
				$total_amount_after_discount	-= $discount_extra;
			}
			
			//$credit_details			= $this->getCreditDetailsAfterOrder($total_amount_after_discount, $cart_data['credit_voucher_quantity']);
			$credit_voucher_501		= $cart_data['credit_voucher_quantity'];
			$credit_voucher_100		= $cart_data['credit_voucher_100'];
			$credit_points_amount	= 0;//$credit_details['pb_points_amount'];
			$credit_cash_amount		= 0;//$credit_details['pb_cash_amount'];
			
			// $tax_amount		= $this->calculateTaxes($total_amount_after_discount + $shipping_amount);
			//open tag for coupon code
			$discount_coupon						= 0;
			$couponMsg								= '';
			if( !empty($couponCode) ){
				$this->Coupon 	= new CouponComponent(new ComponentRegistry());
				$coupon = $this->Coupon->getRulesByCoupon($couponCode, $getCustomerData->email, $products);
				if($coupon['status']){
					$discount_coupon					= $coupon['couponDiscount'];
					$total_amount_after_discount       -= $discount_coupon;
					$discount_amount					= $discount_amount + $discount_coupon;
					if( $coupon['freeShip'] == 'yes' ){
						$shipping_amount 				= 0; 
					}
				}else{
					$couponCode 	= '';
				}
				$couponMsg								= $coupon['msg'];
			}
			//close tag for coupon code
			$discount_prive = 0;
			$credit_prive_points = 0;
			$prive['apply'] = $pbPrive;
			$prive['product_id'] = PRIVE_PRODUCT_ID;
			if( $prive['status'] ){
				$shipping_amount 				= 0;
				//$discount_prive					= $prive['discount'];
				//$discount_amount			   += $discount_prive;
				$credit_prive_points			= $prive['points'];
				//$total_amount_after_discount   -= $discount_prive;
			}else{
				if( $pbPrive ){
					$shipping_amount 				= 0;
					//$discount_prive					= $prive['discount'];
					//$discount_amount			   += $discount_prive;
					$credit_prive_points			= $prive['points'];
					//$total_amount_after_discount   -= $discount_prive;
					//$total_amount_after_discount   += $prive['charge'];
				}
			}

			$payment_fees							= $this->getPaymentFee($payment_method);

			$data['cart']							= $cart_data;
			$data['total_amount_after_discount']	= $total_amount_after_discount;
			$data['coupon_code']					= $couponCode;
			$data['coupon_msg']						= $couponMsg;
			$data['prive']							= $prive;
			$data['payment_method']					= $payment_method;
			$data['payment_method_data']			= $this->getActivePaymentGatewayData();
			$data['payment_fees']					= $payment_fees;
			$data['shipping_amount']				= $shipping_amount;
			$data['voucher_message']				= $voucher_message;

			$data['grand_total']					= ceil($total_amount_after_discount + $payment_fees);
			$data['grand_total_at_cart']			= ceil($total_amount_after_discount + $shipping_amount);
			$data['grand_final_total']				= $total_amount_after_discount + $payment_fees + $shipping_amount;
			
			if( $prive['status'] ){
				$prive['points'] = $data['grand_final_total']/10;
				$credit_prive_points			= $prive['points'];
			}else{
				if( $pbPrive ){
					$prive['points'] = $data['grand_final_total']/10;
					$credit_prive_points		= $prive['points'];
				}
			}

			$data['customer'] = [
				'voucher_501' 	  => $customer_voucher_501,
				'voucher_100'     => $customer_voucher_100,
				'voucher_amount'  => $gift_voucher_amount,
				'points' 	      => $pb_points_amount,
				'cash'            => $pb_cash_amount
			];

			$creditMsg = '<span class="font-14"><b>Cash Back :</b></span>';
			if( $credit_voucher_501 > 0 ){
				$creditMsg = $creditMsg.' PB <i class="fa fa-rupee"></i> 501 Voucher ('.$credit_voucher_501.')';
			}

			if( $credit_voucher_100 > 0 ){
				if( $credit_voucher_501 > 0 ){
					$creditMsg = $creditMsg.', ';
				}else{
					if( $credit_prive_points == 0 ){
						$creditMsg = $creditMsg.' and';
					}
				}
				$creditMsg = $creditMsg.' PB <i class="fa fa-rupee"></i> 100 Voucher ('.$credit_voucher_100.')';
			}

			if( $credit_prive_points > 0 ){
				if( $credit_voucher_501 > 0 || $credit_voucher_100 > 0 ){
					$creditMsg = $creditMsg.' and';
				}
				$creditMsg = $creditMsg.' PB Points  ('.$credit_prive_points.')';
			}else{

			}

			if($creditMsg == '<span class="font-14"><b>Cash Back :</b></span>'){
				$creditMsg = '';
			}else{
				$creditMsg = $creditMsg.' will reflect in your account once the transaction is completed.';
			}
			$test = '<span class="font-14"><b>Cash Back :</b></span>
								PB <i class="fa fa-rupee"></i> 501 Voucher ('.$credit_voucher_501.'),
								PB <i class="fa fa-rupee"></i> 100 Voucher ('.$credit_voucher_100.') and 
								PB Points '.($credit_prive_points).' 
								will reflect in your account once the transaction is completed.';
			$data['credits'] = [
				'voucher'	 => ($credit_voucher_501 * VOUCHER_501 + $credit_voucher_100 * VOUCHER_100),
				'points'     => ($credit_points_amount + $credit_prive_points),
				'cash'       => $credit_cash_amount,
				'prive'      => $credit_prive_points,
				'message'	 => $creditMsg
			];

			$data['discounts'] = [
				'amount' 	  	  => $discount_amount,
				'coupon' 	 	  => $discount_coupon,
				'voucher_qty_501' => $discount_voucher_qty_501,
				'voucher_qty_100' => $discount_voucher_qty_100,
				'voucher_501' 	  => $discount_voucher_qty_501 * VOUCHER_501,
				'voucher_100'     => $discount_voucher_qty_100 * VOUCHER_100,
				'voucher'    	  => ($discount_voucher_qty_501 * VOUCHER_501 + $discount_voucher_qty_100 * VOUCHER_100),
				'points'          => $discount_points,
				'cash'            => $discount_cash,
				'extra' 	      => $discount_extra,
				'prive' 	      => $discount_prive
			];
		}
		return $data;
	}
	
	public function updateWalletAfterPayment($order_id)
	{
		$orderTable = TableRegistry::get('Orders');
		$order		= $orderTable->get($order_id);
		if($order)
		{
			$id_customer			= $order->customer_id;
			$transaction_type		= 0;
			$id_referrered_customer	= 0;
			$id_order				= $order_id;
			$pb_cash				= $order->pb_cash_amount;
			$pb_points				= $order->pb_points_amount;
			$voucher_amount			= $order->gift_voucher_amount;
			$comments				= "Wallet deducted for placing Order #".$id_order;
			$transaction_ip			= $order->transaction_ip;
			$this->logPBWallet($id_customer, $transaction_type, $id_referrered_customer, $id_order, $pb_cash, $pb_points, $voucher_amount, $comments, $transaction_ip);
		}
	}
	
	public function updateWalletAfterDelivery($order_id)
	{
		$orderTable = TableRegistry::get('Orders');
		$order		= $orderTable->get($order_id);
		if($order && $order->is_points_credited == 0)
		{
			$order->is_points_credited	= 1;
			$orderTable->save($order);
			
			$id_customer			= $order->customer_id;
			$id_order				= $order_id;
			
			$transaction_type		= 1;
			$id_referrered_customer	= 0;
			$pb_cash				= $order->credit_cash_amount;
			$pb_points				= $order->credit_points_amount;
			$voucher_amount			= $order->credit_gift_amount;
			$comments				= "Wallet credited after placing Order #".$id_order;
			$transaction_ip			= $order->transaction_ip;
			$this->logPBWallet($id_customer, $transaction_type, $id_referrered_customer, $id_order, $pb_cash, $pb_points, $voucher_amount, $comments, $transaction_ip);
			
			$customerTable 	= TableRegistry::get('Customers');
			$customer		= $customerTable->get($id_customer);
			if($customer)
			{
				$id_referrer	= $customer->id_referrer;
				if($id_referrer > 0)
				{
					$pbPointsTable	= TableRegistry::get('PbCashPoints');
					$getCurrentYear	= date('Y');
					$getData 		= $pbPointsTable->find('all', ['conditions' => ['id_referrered_customer' => $id_referrer, 'Year(transaction_date)' => $getCurrentYear]]);
					if(count($getData) < 10)
					{
						$transaction_type		= 1;
						$id_referrered_customer	= 0;
						$pb_cash				= ($order->product_total*5)/100;
						$pb_points				= ($order->product_total*10)/100;
						$voucher_amount			= 0;
						$comments				= "Wallet credited for reffering a customer who placed successfull Order #".$id_order;
						$transaction_ip			= $order->transaction_ip;
						$this->logPBWallet($id_referrer, $transaction_type, $id_customer, $id_order, $pb_cash, $pb_points, $voucher_amount, $comments, $transaction_ip);
					}
				}
			}
		}
	}
	
	public function logPBWallet($id_customer, $transaction_type, $id_referrered_customer = 0, $id_order = 0, $pb_cash = 0, $pb_points = 0, $voucher_amount = 0, $comments = '', $transaction_ip)
	{
		if($pb_cash > 0 || $pb_points > 0 || $voucher_amount > 0)
		{
			if($id_customer > 0)
			{
				if($transaction_type == 1)
				{
					$customerTable 				= TableRegistry::get('Customers');
					$customer					= $customerTable->get($id_customer);
					$customer->voucher_amount	= $customer->voucher_amount + $voucher_amount;
					$customer->pb_points		= $customer->pb_points + $pb_points;
					$customer->pb_cash			= $customer->pb_cash + $pb_cash;
					$customerTable->save($customer);
				}
				else if($transaction_type == 0)
				{
					$customerTable 				= TableRegistry::get('Customers');
					$customer					= $customerTable->get($id_customer);
					$customer->voucher_amount	= $customer->voucher_amount - $voucher_amount;
					$customer->pb_points		= $customer->pb_points - $pb_points;
					$customer->pb_cash			= $customer->pb_cash - $pb_cash;
					$customerTable->save($customer);
				}
				
				$pbPointsTable	= TableRegistry::get('PbCashPoints');
				$wallet_log 	= $pbPointsTable->newEntity();
				$wallet_log->id_customer			= $id_customer;
				$wallet_log->id_referrered_customer	= $id_referrered_customer;
				$wallet_log->id_order				= $id_order;
				$wallet_log->pb_cash				= $pb_cash;
				$wallet_log->pb_points				= $pb_points;
				$wallet_log->voucher_amount			= $voucher_amount;
				$wallet_log->transaction_type		= $transaction_type;
				$wallet_log->comments				= $comments;
				$wallet_log->transaction_ip			= $transaction_ip;
				$pbPointsTable->save($wallet_log);
			}
		}
	}
	
	public function createInvoiceOld($id_order)
	{
		$orderTable 	= TableRegistry::get('Orders');
		$order			= $orderTable->get($id_order);
		
		$invoiceTable					= TableRegistry::get('Invoices');
		$invoice 						= $invoiceTable->newEntity();
		$invoice->customer_id			= $order->customer_id;
		$invoice->invoice_number		= 0;
		$invoice->payment_method_id		= $order->payment_method_id;
		$invoice->order_number			= $order->order_number;
		$invoice->payment_mode			= $order->payment_mode;
		$invoice->product_total			= $order->product_total;
		$invoice->payment_amount		= $order->payment_amount;
		$invoice->discount				= $order->discount;
		$invoice->ship_method			= $order->ship_method;
		$invoice->ship_amount			= $order->ship_amount;
		$invoice->ship_discount			= $order->ship_discount;
		$invoice->mode_amount			= $order->mode_amount;
		$invoice->coupon_code			= $order->coupon_code;
		$invoice->tracking_code			= $order->tracking_code;
		$invoice->mobile				= $order->mobile;
		$invoice->email					= $order->email;
		$invoice->created				= $order->created;
		$invoice->status				= $order->status;
		$invoice->shipping_firstname	= $order->shipping_firstname;
		$invoice->shipping_lastname		= $order->shipping_lastname;
		$invoice->shipping_address		= $order->shipping_address;
		$invoice->shipping_city			= $order->shipping_city;
		$invoice->shipping_state		= $order->shipping_state;
		$invoice->shipping_country		= $order->shipping_country;
		$invoice->shipping_pincode		= $order->shipping_pincode;
		$invoice->shipping_email		= $order->shipping_email;
		$invoice->shipping_phone		= $order->shipping_phone;
		$invoice->billing_firstname		= $order->billing_firstname;
		$invoice->billing_lastname		= $order->billing_lastname;
		$invoice->billing_address		= $order->billing_address;
		$invoice->billing_city			= $order->billing_city;
		$invoice->billing_state			= $order->billing_state;
		$invoice->billing_country		= $order->billing_country;
		$invoice->billing_pincode		= $order->billing_pincode;
		$invoice->billing_email			= $order->billing_email;
		$invoice->billing_phone			= $order->billing_phone;
		$invoice->gift_voucher_amount	= $order->gift_voucher_amount;
		$invoice->pb_points_amount		= $order->pb_points_amount;
		$invoice->pb_cash_amount		= $order->pb_cash_amount;
		$invoice->credit_gift_amount	= $order->credit_gift_amount;
		$invoice->credit_points_amount	= $order->credit_points_amount;
		$invoice->credit_cash_amount	= $order->credit_cash_amount;
		$invoice->transaction_ip		= $order->transaction_ip;
		$invoice->delhivery_response	= $order->delhivery_response;
		$invoice->packing_slip			= $order->packing_slip;

		//$invoiceArray					= $invoiceTable->save($invoice);
		//$invoiceArray					= $invoiceTable->save($invoice)->toArray();
		//$invoice_id						= $invoiceArray['id'];
		
		$invoice_id = ( $invoiceTable->save($invoice) ) ? $invoice->id : 0;
		$invoice->invoice_number		= $invoice_id;
		pr($invoice_id);
		$invoiceTable->save($invoice);
		
		if($invoice_id > 0)
		{
			$orderDetailsTable 			= TableRegistry::get('OrderDetails');
			$orderDetails				= $orderDetailsTable->find('all', ['conditions' => ['order_id' => $id_order]])->toArray();
			$invoiceDetailsTable		= TableRegistry::get('InvoiceDetails');
			foreach($orderDetails as $temp_row)
			{
				$invoice_detail 					= $invoiceDetailsTable->newEntity();
				$invoice_detail->invoice_id			= $invoice_id;
				$invoice_detail->title				= $temp_row['title'];
				$invoice_detail->sku_code			= $temp_row['sku_code'];
				$invoice_detail->size				= $temp_row['size'];
				$invoice_detail->price				= $temp_row['price'];
				$invoice_detail->qty				= $temp_row['qty'];
				$invoice_detail->short_description	= $temp_row['short_description'];
				$invoiceDetailsTable->save($invoice_detail);
			}
		}
	}
	
	public function createInvoice($id_order)
	{
		$orderTable 	= TableRegistry::get('Orders');
		$order			= $orderTable->get($id_order);
		
		$invoiceTable					= TableRegistry::get('Invoices');
		$invoice 						= $invoiceTable->newEntity();
		$invoice->customer_id			= $order->customer_id;
		$invoice->invoice_number		= 0;
		$invoice->payment_method_id		= $order->payment_method_id;
		$invoice->order_number			= $order->order_number;
		$invoice->payment_mode			= $order->payment_mode;
		$invoice->product_total			= $order->product_total;
		$invoice->payment_amount		= $order->payment_amount;
		$invoice->discount				= $order->discount;
		$invoice->ship_method			= $order->ship_method;
		$invoice->ship_amount			= $order->ship_amount;
		$invoice->ship_discount			= $order->ship_discount;
		$invoice->mode_amount			= $order->mode_amount;
		$invoice->coupon_code			= $order->coupon_code;
		$invoice->tracking_code			= $order->tracking_code;
		$invoice->mobile				= $order->mobile;
		$invoice->email					= $order->email;
		$invoice->created				= $order->created;
		$invoice->status				= $order->status;
		$invoice->shipping_firstname	= $order->shipping_firstname;
		$invoice->shipping_lastname		= $order->shipping_lastname;
		$invoice->shipping_address		= $order->shipping_address;
		$invoice->shipping_city			= $order->shipping_city;
		$invoice->shipping_state		= $order->shipping_state;
		$invoice->shipping_country		= $order->shipping_country;
		$invoice->shipping_pincode		= $order->shipping_pincode;
		$invoice->shipping_email		= $order->shipping_email;
		$invoice->shipping_phone		= $order->shipping_phone;
		$invoice->billing_firstname		= $order->billing_firstname;
		$invoice->billing_lastname		= $order->billing_lastname;
		$invoice->billing_address		= $order->billing_address;
		$invoice->billing_city			= $order->billing_city;
		$invoice->billing_state			= $order->billing_state;
		$invoice->billing_country		= $order->billing_country;
		$invoice->billing_pincode		= $order->billing_pincode;
		$invoice->billing_email			= $order->billing_email;
		$invoice->billing_phone			= $order->billing_phone;
		$invoice->gift_voucher_amount	= $order->gift_voucher_amount;
		$invoice->pb_points_amount		= $order->pb_points_amount;
		$invoice->pb_cash_amount		= $order->pb_cash_amount;
		$invoice->credit_gift_amount	= $order->credit_gift_amount;
		$invoice->credit_points_amount	= $order->credit_points_amount;
		$invoice->credit_cash_amount	= $order->credit_cash_amount;
		$invoice->transaction_ip		= $order->transaction_ip;
		$invoice->delhivery_response	= $order->delhivery_response;
		$invoice->packing_slip			= $order->packing_slip;

		//$invoiceArray					= $invoiceTable->save($invoice);
		//$invoiceArray					= $invoiceTable->save($invoice)->toArray();
		//$invoice_id						= $invoiceArray['id'];

		$invoice_id = ( $invoiceTable->save($invoice) ) ? $invoice->id : 0;
		$invoice->invoice_number		= $invoice_id;
		$invoiceTable->save($invoice);
		
		if($invoice_id > 0)
		{
			$orderDetailsTable 			= TableRegistry::get('OrderDetails');
			$orderDetails				= $orderDetailsTable->find('all', ['conditions' => ['order_id' => $id_order]])->toArray();
			$invoiceDetailsTable		= TableRegistry::get('InvoiceDetails');
			foreach($orderDetails as $temp_row)
			{
				$invoice_detail 					= $invoiceDetailsTable->newEntity();
				$invoice_detail->invoice_id			= $invoice_id;
				$invoice_detail->title				= $temp_row['title'];
				$invoice_detail->sku_code			= $temp_row['sku_code'];
				$invoice_detail->size				= $temp_row['size'];
				$invoice_detail->price				= $temp_row['price'];
				$invoice_detail->qty				= $temp_row['qty'];
				$invoice_detail->short_description	= $temp_row['short_description'];
				$invoiceDetailsTable->save($invoice_detail);
			}
		}
	}
	
	public function sendOrderToDelhivery($id_order)
	{
		$waybill			= '';
		$this->Delhivery 	= new DelhiveryComponent(new ComponentRegistry());
		$responseArray		= $this->Delhivery->sendOrder($id_order);
		if($responseArray['success'])
		{
			foreach($responseArray['packages'] as $package_row)
			{
				$getRow		= $package_row;
				if($getRow['status'] == 'Success')
				{
					$waybill	= $getRow['waybill'];
				}
			}
		}
		
		$orderTable 				= TableRegistry::get('Orders');
		$order						= $orderTable->get($id_order);
		$order->tracking_code		= $waybill;
		$order->delhivery_response	= serialize($responseArray);
		
		
		$packingSlip				= $this->Delhivery->getPackingSlip($waybill);
		$order->packing_slip		= serialize($packingSlip);
		$orderTable->save($order);
		
		$invoiceTable		= TableRegistry::get('Invoices');
		$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_number' => $id_order]])->toArray();
		if(!empty($invoiceData) && isset($invoiceData[0]))
		{
			$id_invoice						= $invoiceData[0]->id;
			$invoice						= $invoiceTable->get($id_invoice);
			$invoice->tracking_code			= $waybill;
			$invoice->delhivery_response	= serialize($responseArray);
			$invoice->packing_slip			= serialize($packingSlip);
			$invoiceTable->save($invoice);
		}
	}
	
	public function sendOrderToDelhiveryTest($id_order, $waybill)
	{
		$status = 0;
		$this->Delhivery 	= new DelhiveryComponent(new ComponentRegistry());
		$packingSlip				= $this->Delhivery->getPackingSlip($waybill);
		if( !empty($packingSlip)  )
		{
			$orderTable 				= TableRegistry::get('Orders');
			$order						= $orderTable->get($id_order);					
			$order->packing_slip		= serialize($packingSlip);
			$orderTable->save($order);
			
			$invoiceTable		= TableRegistry::get('Invoices');
			$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_number' => $id_order]])->toArray();
			if(!empty($invoiceData) && isset($invoiceData[0]))
			{
				$id_invoice						= $invoiceData[0]->id;
				$invoice						= $invoiceTable->get($id_invoice);
				$invoice->packing_slip			= serialize($packingSlip);
				$invoiceTable->save($invoice);
			}
			$status = 1;
		}
		return $status;
	}
	
	public function cancelOrder($id_order = 0)
	{
		$dataTable 		= TableRegistry::get('Orders');
		$query 			= $dataTable->find('all', ['conditions' => ['Orders.id' => $id_order]])->toArray();
		foreach($query as $value)
		{
			if(in_array($value->status, ['accepted', 'proccessing']))
			{
				$order 			= $dataTable->get($value->id);
				$order->status 	= 'cancelled';
				if($dataTable->save($order))
				{
					$invoiceTable		= TableRegistry::get('Invoices');
					$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_number' => $id_order]])->toArray();
					if(!empty($invoiceData) && isset($invoiceData[0]))
					{
						$id_invoice						= $invoiceData[0]->id;
						$invoice						= $invoiceTable->get($id_invoice);
						$invoice->status				= 'cancelled';
						$invoiceTable->save($invoice);
					}
		
					$this->reversePointsAfterCancelOrder($value->id);
					$this->cancelOrderInDelhivery($value->id);
					$this->changeInvoiceStatus($value->id, 'cancelled');
					return true;
				}
			}			
		}
		return false;
    }
	
	public function reversePointsAfterCancelOrder($id_order)
	{
		if($id_order > 0)
		{
			$dataTable 		= TableRegistry::get('Orders');
			$order 			= $dataTable->get($id_order);
			if(!empty($order))
			{
				if($order->is_points_reversed == 0)
				{
					$order->is_points_reversed	= 1;
					if($dataTable->save($order))
					{
						if($order->pb_cash_amount > 0 || $order->pb_points_amount > 0 || $order->gift_voucher_amount > 0)
						{
							$transaction_type		= 1;
							$pb_cash				= $order->pb_cash_amount;
							$pb_points				= $order->pb_points_amount;
							$voucher_amount			= $order->gift_voucher_amount;
							$comments				= "Wallet credited after cancelling an Order #".$id_order;
							$transaction_ip			= $_SERVER['REMOTE_ADDR'];
							$this->logPBWallet($order->customer_id, $transaction_type, 0, $id_order, $pb_cash, $pb_points, $voucher_amount, $comments, $transaction_ip);
						}
					}
				}
			}
		}
	}
	
	public function cancelOrderInDelhivery($id_order)
	{
		$this->Delhivery 		= new DelhiveryComponent(new ComponentRegistry());
		
		$orderTable 			= TableRegistry::get('Orders');
		$order					= $orderTable->get($id_order);
		$waybill				= $order->tracking_code;
		if($waybill != '')
		{
			$result					= $this->Delhivery->cancelOrder($waybill);
			$order->cancel_response	= serialize($result);
			$orderTable->save($order);
		}
	}
	
	public function changeOrderStatus($id_order, $status)
	{
		$dataTable 		= TableRegistry::get('Orders');
		$query 			= $dataTable->find('all', ['conditions' => ['Orders.id' => $id_order]])->toArray();
		foreach($query as $value)
		{
			$previousStatus	= $value->status;
			$order 			= $dataTable->get($value->id);
			$order->status 	= $status;
			if($dataTable->save($order))
			{
				if($status == 'cancelled' || $status == 'cancelled_by_customer')
				{
					if($previousStatus != 'cancelled' && $previousStatus != 'cancelled_by_customer')
					{
						$this->reversePointsAfterCancelOrder($value->id);
						$this->cancelOrderInDelhivery($value->id);
						$this->changeInvoiceStatus($value->id, $status);
					}
				}
				return true;
			}
		}
		return false;
	}
	
	public function changeInvoiceStatus($id_order, $status)
	{
		$dataTable 		= TableRegistry::get('Invoices');
		$query 			= $dataTable->find('all', ['conditions' => ['Invoices.order_number' => $id_order]])->toArray();
		foreach($query as $value)
		{
			$invoice 			= $dataTable->get($value->id);
			$invoice->status 	= $status;
			if($dataTable->save($invoice))
			{
				return true;
			}
		}
		return false;
	}

	public function orderDelivered($orderNumber){
		$customerId = 0;		
		$oDetails   = [];
		$orderTable 			= TableRegistry::get('Orders');
		$order					= $orderTable->get($orderNumber);
		if( empty($order) ){
			exit;
		}else{
			$customerId = $order->customer_id;
		}
		$this->Customer 		= new CustomerComponent(new ComponentRegistry());
		$oDetails 	= $this->Customer->getOrdersDetails($customerId, $orderNumber);
		
		if( !empty($oDetails) ){
			$oDetails['customerId'] = $customerId;
			$oDetails['currentDate'] = date("d F Y");
			$this->Sms 		= new SmsComponent(new ComponentRegistry());
			$this->Sms->orderDeliveredSend($oDetails['shippingPhone'], $orderNumber, $oDetails['currentDate']);
			$this->getMailer('Customer')->send('orderDelivered', [$oDetails]);	
		}
		return $oDetails;
	}
	
	public function orderIntransit($orderNumber){
		$customerId = 0;		
		$oDetails   = [];
		$orderTable 			= TableRegistry::get('Orders');
		$order					= $orderTable->get($orderNumber);
		if( empty($order) ){
			exit;
		}else{
			$customerId = $order->customer_id;
		}
		$this->Customer 		= new CustomerComponent(new ComponentRegistry());
		$oDetails 	= $this->Customer->getOrdersDetails($customerId, $orderNumber);
		
		if( !empty($oDetails) ){
			$oDetails['customerId'] = $customerId;
			$oDetails['currentDate'] = date("d F Y");
			$this->Sms 		= new SmsComponent(new ComponentRegistry());
			$this->Sms->orderIntransitSend($oDetails['shippingPhone'], $orderNumber, $oDetails['currentDate']);
			$this->getMailer('Customer')->send('orderIntransit', [$oDetails]);	
		}
		return $oDetails;
	}
	
	public function orderDispatched($orderNumber){
		$customerId = 0;		
		$oDetails   = [];
		$orderTable 			= TableRegistry::get('Orders');
		$order					= $orderTable->get($orderNumber);
		if( empty($order) ){
			exit;
		}else{
			$customerId = $order->customer_id;
		}
		$this->Customer 		= new CustomerComponent(new ComponentRegistry());
		$oDetails 	= $this->Customer->getOrdersDetails($customerId, $orderNumber);
		
		if( !empty($oDetails) ){
			$oDetails['customerId'] = $customerId;
			$oDetails['currentDate'] = date("d F Y");
			$this->Sms 		= new SmsComponent(new ComponentRegistry());
			$this->Sms->orderDispatchedSend($oDetails['shippingPhone'], $orderNumber, $oDetails['currentDate']);
			$this->getMailer('Customer')->send('orderDispatched', [$oDetails]);	
		}
		return $oDetails;
	}
	
	public function orderAccountCredit($orderNumber){
		$customerId = 0;		
		$oDetails   = [];
		$orderTable 			= TableRegistry::get('Orders');
		$order					= $orderTable->get($orderNumber);
		if( empty($order) ){
			exit;
		}else{
			$customerId = $order->customer_id;
		}
		$this->Customer 		= new CustomerComponent(new ComponentRegistry());
		$oDetails 	= $this->Customer->getOrdersDetails($customerId, $orderNumber);
		
		if( !empty($oDetails) ){
			$oDetails['customerId'] = $customerId;
			$this->Sms 		= new SmsComponent(new ComponentRegistry());
			$this->Sms->orderAccountCreditSend($oDetails['shippingPhone'], $oDetails['creditGiftAmount'], $oDetails['creditPointsAmount'], $oDetails['creditCashAmount']);
			$this->getMailer('Customer')->send('accountCredit', [$oDetails]);	
		}
		return $oDetails;
	}

	public function orderCancelled($orderNumber){
		$customerId = 0;		
		$oDetails   = [];
		$orderTable 			= TableRegistry::get('Orders');
		$order					= $orderTable->get($orderNumber);
		if( empty($order) ){
			exit;
		}else{
			$customerId = $order->customer_id;
		}
		$this->Customer 		= new CustomerComponent(new ComponentRegistry());
		$oDetails 	= $this->Customer->getOrdersDetails($customerId, $orderNumber);
		
		if( !empty($oDetails) ){
			$oDetails['customerId'] = $customerId;
			$this->getMailer('Customer')->send('orderCancelled', [$oDetails]);	
		}
		return $oDetails;
	}

	public function orderReview($orderNumber){
		$customerId = 0;		
		$oDetails   = [];
		$orderTable 			= TableRegistry::get('Orders');
		$order					= $orderTable->get($orderNumber);
		if( empty($order) ){
			exit;
		}else{
			$customerId = $order->customer_id;
		}
		$this->Customer 		= new CustomerComponent(new ComponentRegistry());
		$oDetails 	= $this->Customer->getOrdersDetails($customerId, $orderNumber);
		
		if( !empty($oDetails) ){
			$oDetails['customerId'] = $customerId;
			//$this->getMailer('Customer')->send('orderReview', [$oDetails]);	
		}
		return $oDetails;
	}

	public function pgResposes($orderId,$pgName,$pgData){
		$status = 0;
		if( !empty($orderId) && !empty($pgName) && ($pgName != 'cod') ){
			$pgTable 			= TableRegistry::get('PgResponses');
			$pg					= $pgTable->newEntity();
			$pg->order_id		= $orderId;
			$pg->pg_name		= $pgName;
			$pg->pg_data		= $pgData;
			if( $pgTable->save($pg) ){
				$status = 1;
			}
		}
		return $status;
	}
	public function pgResEnDe($string, $action = 'd' ) {
		// you may change these values to your own
		$secret_key = 'pb';
		$secret_iv = 'google';
	 
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	 
		if( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		}
		else if( $action == 'd' ){
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}
	 
		return $output;
	}

}