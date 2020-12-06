<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use App\Controller\Component\Store;
/**
 * Admin component
 */
class CustomerComponent extends Component
{
	public $REG_MOBILE = '/^[1-9]{1}[0-9]{9}$/';
	public $REG_ALPHA_SPACE = '/^[a-zA-Z ]*$/';
	public $REG_DATE = '/^\d{4}-\d{2}-\d{2}$/';
	public $REG_PINCODE = '/^\d{6}$/';
	
	public function isAuth($userId) {
		$status = false;
		$data = [];
		$dbToken = '';
		$dbPassToken = '';
		$userToken = $this->request->getHeader('Authorization');
		$userToken = isset($userToken[0]) ? $userToken[0]:NULL;
		$dataTable = TableRegistry::get('Customers');
		$q = $dataTable->findByIdAndIsActive($userId, 'active')->toArray();
		if( isset($q[0]) ){
			$data = $q[0];
			$dbToken = $q[0]['api_token'];
			$dbPassToken = $q[0]['password'];
		}
		if( !empty($userToken) && ( ($dbToken === $userToken) || ($dbPassToken === $userToken) ) ){
			$status = true;
		}
		return ['status'=>$status, 'data'=>$data];
    }
	
	public function trackPage($customerId, $page=NULL){
		if( $customerId > 0 ){
			$dataTable = TableRegistry::get('Customers');
			$data = $dataTable->get($customerId, ['fields'=>['id','email','track_page']]);
			$data->track_page = $page;
			$dataTable->save($data);
		}
	}
	
	public function getSesssionData($customerId, $md5Pass=''){
		$data = $cart = [];
		$this->Membership	= new MembershipComponent(new ComponentRegistry());
		$this->Store	= new StoreComponent(new ComponentRegistry());
		$cart 			= $this->Store->getActiveCart($customerId);
		$cart 			= $cart['cart'] ?? [];
		try{
			$dataTable = TableRegistry::get('Customers');
			$data = $dataTable->get($customerId, ['fields'=>['id','firstname','lastname','email','gender','dob','profession','address','city','pincode','mobile','is_group','image','location_id','api_token'=>'password'],'conditions'=>['is_active'=>'active']])->toArray();
			if( !empty($data) ){
				$data['cart'] = $cart;
				$data['member'] = $this->Membership->getMembership($customerId);
			}
		}catch(\Exception $e){
			$data = [];
		}
		return $data;
	}
	
	public function generatePassword($length = 8 ) {
       //$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
       $chars = "0123456789";
       $password = substr( str_shuffle( $chars ), 0, $length );
       return $password;
    }
	
	public function getReviews($userId, $limit=20, $page=1){
		$data = [];
		$reviewTable = TableRegistry::get('Reviews');
		$dataTable = TableRegistry::get('Products');
		$query = $dataTable->find('all', ['fields'=>['id','title','sku_code','url_key','size','size_unit','price','is_stock','goods_tax','offer_price','offer_from','offer_to','gender','short_description'],'limit'=>$limit,'conditions'=>['Products.is_active'=>'active']])
				->distinct(['Products.id'])
				->matching("Reviews", function($q) use ($userId){
					return 	$q->where(['Reviews.customer_id'=>$userId]);
				})
				->contain([
					'ProductsImages' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','product_id','title','alt_text','img_small'])->where(['ProductsImages.is_small'=>1,'ProductsImages.is_active'=>'active']);
						}
					]	
				])
				
				->page($page)
				->order(['Products.created'=>'DESC'])
				->toArray();
		//pr($query);die;
		foreach($query as $value){
			$reviews = $images = [];
			foreach($value->products_images as $v){
				$images = [
					'id'=>$v->id,
					'alt'=>$v->alt_text,
					'title'=>$v->title,
					'url'=>$v->img_small
				];
			}
			$r = $reviewTable->find();
			$r = $r->select(['id','product_id','title','customer_name','description','rating','created','total'=>$r->func()->count('*'),'totalRating'=>$r->func()->sum('rating')])
				->where(['customer_id'=>$userId,'product_id'=>$value->id])
				->group(['product_id'])
				->contain([
					'Customers' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','firstname','lastname','image']);
						}
					]	
				])
				->toArray();
			//pr($r);die;
			foreach($r as $v){
				$customer = [];
				if($v->customer){
					$customer = [
						'id'=>$v->customer->id,
						'name'=>!empty($v->customer_name) ? $v->customer_name:$v->customer->firstname.' '.$v->customer->lastname,
						'image'=>$v->customer->image
					];
				}
				$reviews = [
					'id'=>$v->id,
					'title'=>$v->title,
					'description'=>$v->description,
					'totalReviews'=>$v->total,
					'totalRating'=>$v->totalRating,
					'finalRating'=>ceil($v->totalRating/$v->total),
					'created'=>date('Y-m-d',strtotime($v->created)),
					'customer'=>$customer
				];
			}
			$data[] = [
				'id'=>$value->id,
				'title'=>$value->title,
				'skuCode'=>$value->sku_code,
				'urlKey'=>$value->url_key,
				'size'=>$value->size,
				'sizeUnit'=>$value->size_unit,
				'price'=>$value->price,
				'isStock'=>$value->is_stock,
				'offerPrice'=>$value->offer_price,
				'offerFrom'=>$value->offer_from,
				'offerTo'=>$value->offer_to,
				'gender'=>$value->gender,
				'description'=>$value->short_description,
				'images'=>$images,
				'reviews'=>$reviews
			];
		} 
		return $data;
	}
	
	public function changeAccount($currentCustomerId, $newCustomerId){
		if($currentCustomerId > 0 && $newCustomerId > 0){
			$dataTable = TableRegistry::get('Carts');
			$cart = $this->getMiniCart($currentCustomerId);
			$items = [];
			foreach($cart as $value){
				$items[] = [
					'customer_id'=>$newCustomerId,
					'product_id'=>$value['id'],
					'qty'=>$value['cart_qty']
				];
			}
			if(count($items) > 0){
				$dataTable->query()->delete()->where(['customer_id'=>$newCustomerId])->execute();
				$items = $dataTable->newEntities($items);
				$dataTable->saveMany($items);
				
				$dataTable = TableRegistry::get('Customers');
				$customer = $dataTable->get($currentCustomerId);
				$customer->api_token = NULL;
				$dataTable->save($customer);				
			}
		}
		return true;
	}
	
	public function getWalletData($userId){
		$voucherAmount = 1002;
		$pbPointsAmount = 200;
		$pbCash = 1000;
		
		return [
			'voucherAmount'=>$voucherAmount,
			'pbPointsAmount'=>$pbPointsAmount,
			'pbCash'=>$pbCash
		];
	}
	
	public function addCustomerReviews($userId, $itemId, $rating, $title, $description) {
		$status = false;
		if( ($userId > 0) && ($itemId > 0) && ($rating > 0) && !empty($title) && !empty($description) ){
			$dataTable = TableRegistry::get('Reviews');
			$review = $dataTable->newEntity();
			$review->customer_id = $userId;
			$review->product_id = $itemId;
			$review->title = $title;
			$review->description = $description;
			$review->rating = $rating;
			$review->offer = 1;
			$review->location_ip = $this->request->clientIp();
			$status = ($dataTable->save($review)) ? true : false;
		}
		return $status;
    }
	
	public function getMiniCart($userId) {
		$data = [];
		$dataTable = TableRegistry::get('Products');
		$query = $dataTable->find('all', ['fields'=>['id','title','sku_code','url_key','size','size_unit','price','is_stock','goods_tax','offer_price','offer_from','offer_to','gender','short_description'],'conditions'=>['Products.is_active'=>'active']])
				->contain([
					'ProductsImages' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','product_id','title','alt_text','img_thumbnail'])->where(['ProductsImages.is_thumbnail'=>1,'ProductsImages.is_active'=>'active']);
						}
					]	
				])
				->matching("Carts", function($q) use ($userId){
					return $q->select(['id','qty'])->where(['Carts.customer_id'=>$userId]);
				})
				->toArray();
		//pr($query);
		foreach($query as $value){
			$url = '';
			foreach($value->products_images as $v){
				if(!empty($v->img_thumbnail)){ $url = $v->img_thumbnail; }
			}
			$tax = explode('_',$value->goods_tax);
			//echo $qty = $value->_matchingData['Carts']['qty'];
			$data[] = [
				'id'=>$value->id,
				'title'=>$value->title,
				'sku_code'=>$value->sku_code,
				'url_key'=>$value->url_key,
				'size'=>$value->size,
				'size_unit'=>$value->size_unit,
				'cart_id'=>$value->_matchingData['Carts']['id'],
				'cart_qty'=>$value->_matchingData['Carts']['qty'],
				'price'=>number_format($value->price, 2),
				'qty_price'=>$value->price * $value->_matchingData['Carts']['qty'],
				'is_stock'=>($value->is_stock == 'in_stock') ? true: false,
				'tax_title'=>$tax[0],
				'tax_value'=>$tax[1],
				'tax_type'=>$tax[2],
				'offer_price'=>$value->offer_price,
				'offer_from'=>$value->offer_from,
				'offer_to'=>$value->offer_to,
				'gender'=>$value->gender,
				'description'=>$value->short_description,
				'image'=>$url
			];
		}
		return $data;
    }
	
	public function getOrders($userId, $orderBy='', $offset=0) {
		$data = [];
		$limit = 50;
		$productsTable = TableRegistry::get('Products');
		$imagesTable = TableRegistry::get('ProductsImages');
		$dataTable = TableRegistry::get('Orders');
		$filterCondition['Orders.customer_id'] = $userId;
		if(!empty($orderBy)){
			$filterCondition['Orders.status'] = 'cancel';
		}else{
			$filterCondition['Orders.status !='] = 'cancel';
		}
		$query = $dataTable->find('all', ['contain'=>['OrderDetails', 'OrderComments', 'PaymentMethods'],'limit'=>$limit,'order'=>['Orders.id'=>'DESC'],'conditions'=>$filterCondition])->toArray();

		foreach($query as $value){
			$comments = [];
			foreach($value->order_comments as $v){
				$comments = [
					'id'=>$v->id,
					'orderId'=>$v->order_id,
					'givenBy'=>$v->given_by,
					'status'=>$v->status,
					'comment'=>$v->comment
				];
			}
			$details = [];
			foreach($value->order_details as $v){
				$urlKey = '';
				$queryImg = $productsTable->find('all', ['fields'=>['url_key'],'conditions'=>['Products.id'=>$v->product_id]])->toArray();
				foreach($queryImg as $img){
					$urlKey = $img->url_key;
				}
				$image = '';
				$queryImg = $imagesTable->find('all', ['fields'=>['img_small'],'conditions'=>['ProductsImages.product_id'=>$v->product_id,'ProductsImages.is_small'=>1,'ProductsImages.is_active'=>'active']])->toArray();
				foreach($queryImg as $img){
					$image = $img->img_small;
				}
				$details[] = [
					'id'=>$v->id,
					'orderId'=>$v->order_id,
					'productId'=>$v->product_id,
					'title'=>$v->title,
					'skuCode'=>$v->sku_code,
					'urlKey'=>$urlKey,
					'size'=>$v->size,
					'price'=>$v->price,
					'qty'=>$v->qty,
					'discount'=>$v->discount,
					'goods_tax'=>$v->goods_tax,
					'tax_amount'=>$v->tax_amount,
					'image'=>$image,
					'short_description'=>$v->short_description
				];
			}
			$paymentMethodName = isset($value->payment_method->title) ? $value->payment_method->title : '';

			$data[] = [
				'id'=>$value->id,
				'orderNumber'			=>$value->order_number,
				'paymentMethodId'		=>$value->payment_method_id,
				'paymentMethodName'		=>$paymentMethodName,

				'paymentMode'			=>$value->payment_mode,
				'productTotal'			=>$value->product_total,
				'paymentAmount'			=>$value->payment_amount,
				'discount'				=>$value->discount,
				'shipMethod'			=>$value->ship_method,
				'shipAmount'			=>$value->ship_amount,
				'modeAmount'			=>$value->mode_amount,
				'couponCode'			=>$value->coupon_code,
				'trackingCode'			=>$value->tracking_code,
				'mobile'				=>$value->mobile,
				'email'					=>$value->email,
				'created'			    =>date("d F Y",strtotime($value->created)),
				'status'				=>$value->status,
				'shippingName'			=>$value->shipping_firstname.' '.$value->shipping_lastname,
				'shippingAddress'		=>$value->shipping_address,
				'shippingCity'			=>$value->shipping_city,
				'shippingState'			=>$value->shipping_state,
				'shippingCountry'		=>$value->shipping_country,
				'shippingPincode'		=>$value->shipping_pincode,
				'shippingEmail'			=>$value->shipping_email,
				'shippingPhone'			=>$value->shipping_phone,
				'billingName'			=>$value->billing_firstname.' '.$value->billing_lastname,
				'billingAddress'		=>$value->billing_address,
				'billingCity'			=>$value->billing_city,
				'billingState'			=>$value->billing_state,
				'billingCountry'		=>$value->billing_country,
				'billingPincode'		=>$value->billing_pincode,
				'billingEmail'			=>$value->billing_email,
				'billingPhone'			=>$value->billing_phone,
				'giftVoucherAmount'	    =>$value->gift_voucher_amount,
				'pbPointsAmount'		=>$value->pb_points_amount,
				'pbCashAmount'			=>$value->pb_cash_amount,
				'creditGiftAmount'		=>$value->credit_gift_amount,
				'creditPointsAmount'	=>$value->credit_points_amount,
				'creditCashAmount'		=>$value->credit_cash_amount,
				'transactionIP'			=>$value->transaction_ip,
				'message'			    =>'',//!empty($value->delhivery_response) ? $value->delhivery_response:'Processing',
				'details'				=>$details,
				'comments'				=>$comments
			];
		}
		return $data;
    }
	
	public function getOrdersDetails($userId=0, $orderNumber=0) {
		$data = [];
		$productsTable = TableRegistry::get('Products');
		$imagesTable = TableRegistry::get('ProductsImages');
		$dataTable = TableRegistry::get('Orders');
		//$query = $dataTable->find('all', ['contain'=>['OrderDetails', 'OrderComments', 'PaymentMethods'],'conditions'=>['Orders.customer_id'=>$userId,'Orders.order_number'=>$orderNumber]])->toArray();
		try{
			$value = $dataTable->get($orderNumber, ['contain'=>['OrderDetails', 'OrderComments', 'PaymentMethods']]);
		}catch(\Exception $e){ 
			//deleted this query when updated function
			$value = $dataTable->get($userId, ['contain'=>['OrderDetails', 'OrderComments', 'PaymentMethods']]);
		}
		//pr($query);
		if( !empty($value)){
			$comments = [];
			foreach($value->order_comments as $v){
				$comments = [
					'id'=>$v->id,
					'orderId'=>$v->order_id,
					'givenBy'=>$v->given_by,
					'status'=>$v->status,
					'comment'=>$v->comment
				];
			}
			$details = [];
			foreach($value->order_details as $v){
				$urlKey = '';
				$queryImg = $productsTable->find('all', ['fields'=>['url_key'],'conditions'=>['Products.id'=>$v->product_id]])->toArray();
				foreach($queryImg as $img){
					$urlKey = $img->url_key;
				}
				$image = '';
				$queryImg = $imagesTable->find('all', ['fields'=>['img_base'],'conditions'=>['ProductsImages.product_id'=>$v->product_id,'ProductsImages.is_base'=>1,'ProductsImages.is_active'=>'active']])->toArray();
				foreach($queryImg as $img){
					$image = $img->img_base;
				}
				$details[] = [
					'id'=>$v->id,
					'orderId'=>$v->order_id,
					'productId'=>$v->product_id,
					'title'=>$v->title,
					'skuCode'=>$v->sku_code,
					'urlKey'=>$urlKey,
					'size'=>$v->size,
					'price'=>$v->price,
					'qty'=>$v->qty,
					'discount'=>$v->discount,
					'goods_tax'=>$v->goods_tax,
					'tax_amount'=>$v->tax_amount,
					'image'=>$image,
					'short_description'=>$v->short_description
				];
			}
			$data = [
				'id'=>$value->id,
				'customerId'			=>$value->customer_id,
				'orderNumber'			=>$value->order_number,
				'paymentMethodId'		=>$value->payment_method_id,
				'paymentMethodName'		=>$value->payment_method->title ?? '',
				'paymentMode'			=>$value->payment_mode,
				'productTotal'			=>$value->product_total,
				'paymentAmount'			=>$value->payment_amount,
				'discount'				=>$value->discount,
				'shipMethod'			=>$value->ship_method,
				'shipAmount'			=>$value->ship_amount,
				'modeAmount'			=>$value->mode_amount,
				'couponCode'			=>$value->coupon_code,
				'trackingCode'			=>$value->tracking_code,
				'mobile'				=>$value->mobile,
				'email'					=>$value->email,
				'created'			    =>date("d F Y",strtotime($value->created)),
				'status'				=>$value->status,
				'shippingName'			=>$value->shipping_firstname.' '.$value->shipping_lastname,
				'shippingAddress'		=>$value->shipping_address,
				'shippingCity'			=>$value->shipping_city,
				'shippingState'			=>$value->shipping_state,
				'shippingCountry'		=>$value->shipping_country,
				'shippingPincode'		=>$value->shipping_pincode,
				'shippingEmail'			=>$value->shipping_email,
				'shippingPhone'			=>$value->shipping_phone,
				'billingName'			=>$value->billing_firstname.' '.$value->billing_lastname,
				'billingAddress'		=>$value->billing_address,
				'billingCity'			=>$value->billing_city,
				'billingState'			=>$value->billing_state,
				'billingCountry'		=>$value->billing_country,
				'billingPincode'		=>$value->billing_pincode,
				'billingEmail'			=>$value->billing_email,
				'billingPhone'			=>$value->billing_phone,
				'giftVoucherAmount'	    =>$value->gift_voucher_amount,
				'pbPointsAmount'		=>$value->pb_points_amount,
				'pbCashAmount'			=>$value->pb_cash_amount,
				'creditGiftAmount'		=>$value->credit_gift_amount,
				'creditPointsAmount'	=>$value->credit_points_amount,
				'creditCashAmount'		=>$value->credit_cash_amount,
				'transactionIP'			=>$value->transaction_ip,
				'message'			    =>$value->delhivery_response,
				'details'				=>$details,
				'comments'				=>$comments
			];
		}		
		return $data;
    }
	
	public function reorderOrder($userId, $orderNumber) {
		$cartItem = [];
		$cartStatus = true;
		$status = false;
		$message = '';
		$productsTable = TableRegistry::get('Products');
		$cartsTable = TableRegistry::get('Carts');
		$order = $this->getOrdersDetails($userId, $orderNumber);
		if( isset($order['details']) && count($order['details']) > 0 ){
			foreach($order['details'] as $v){
				$prod = $productsTable->find('all', ['conditions'=>['id'=>$v['productId'],'is_stock'=>'in_stock','is_active'=>'active']])->toArray();
				if( empty($prod) ){
					$cartStatus = false;
					$message = 'Sorry, some product are not available!';
				}
				$cartItem[] = [
					'productId'=>$v['productId'],
					'qty'=>$v['qty']
				];
			}
		}
		if( (count($cartItem) > 0) && $cartStatus ){
			$cartsTable->query()->delete()->where(['customer_id' => $userId])->execute();
			foreach($cartItem as $v){
				$cart = $cartsTable->newEntity();
				$cart->customer_id = $userId;
				$cart->product_id = $v['productId'];
				$cart->qty = $v['qty'];
				if($cartsTable->save($cart)){ 
					$status = true;
				};
			}
		}
		return ['status'=>$status, 'message'=>$message];
    }
	
	public function cancelOrder($userId = 0, $orderNumber = 0)
	{
		$this->Store	= new StoreComponent(new ComponentRegistry());
		$data 			= [];
		$status 		= false;
		$message 		= "Sorry, you can not do this!";	
		$dataTable 		= TableRegistry::get('Orders');
		$query 			= $dataTable->find('all', ['conditions'=>['Orders.customer_id'=>$userId,'Orders.order_number'=>$orderNumber]])->toArray();
		foreach($query as $value)
		{
			if( in_array($value->status, ['accepted', 'proccessing']) )
			{
				$order 			= $dataTable->get($value->id);
				$order->status 	= 'cancelled_by_customer';
				if($dataTable->save($order))
				{
					$invoiceTable		= TableRegistry::get('Invoices');
					$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_number' => $value->id]])->toArray();
					if(!empty($invoiceData) && isset($invoiceData[0]))
					{
						$id_invoice						= $invoiceData[0]->id;
						$invoice						= $invoiceTable->get($id_invoice);
						$invoice->status				= 'cancelled';
						$invoiceTable->save($invoice);
					}
					
					$this->Store->reversePointsAfterCancelOrder($value->id);
					$this->Store->cancelOrderInDelhivery($value->id);
					$status 	= true;
					$message 	= "Order cancelled successfully!";
					$data 		= ['status'=>'cancel'];
				}
			}			
		}
		return ['data'=>$data, 'status'=>$status, 'message'=>$message];
    }

	public function getWishlist($userId) {
		$data = [];
		$dataTable = TableRegistry::get('Products');
		$query = $dataTable->find('all', ['fields'=>['id','title','sku_code','url_key','size','size_unit','price','is_stock','goods_tax','offer_price','offer_from','offer_to','gender','short_description'],'conditions'=>['Products.is_active'=>'active']])
				->contain([
					'ProductsImages' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','product_id','title','alt_text','img_base'])->where(['ProductsImages.is_base'=>1,'ProductsImages.is_active'=>'active']);
						}
					]	
				])
				->matching("Wishlists", function($q) use ($userId){
					return $q->where(['Wishlists.customer_id'=>$userId]);
				})
				->toArray();
		
		foreach($query as $value){
			$images = [];
			foreach($value->products_images as $v){
				$images = [
					'id'=>$v->id,
					'alt'=>$v->alt_text,
					'title'=>$v->title,
					'url'=>$v->img_base
				];
			}
			$tax = explode('_',$value->goods_tax);
			$data[] = [
				'id'=>$value->id,
				'title'=>$value->title,
				'skuCode'=>$value->sku_code,
				'urlKey'=>$value->url_key,
				'size'=>$value->size,
				'sizeUnit'=>$value->size_unit,
				'price'=>$value->price,
				'isStock'=>($value->is_stock == 'in_stock') ? true: false,
				'taxTitle'=>$tax[0],
				'taxValue'=>$tax[1],
				'taxType'=>$tax[2],
				'offerPrice'=>$value->offer_price,
				'offerFrom'=>$value->offer_from,
				'offerTo'=>$value->offer_to,
				'gender'=>$value->gender,
				'description'=>$value->short_description,
				'images'=>$images
			];
		}
		return $data;
    }
	
	public function addItemIntoWishlist($userId, $itemId) {
		$message = 'Sorry, Invalid data!'; $status = 0;
		if( ($userId > 0) && ($itemId > 0) ){
			$dataTable = TableRegistry::get('Wishlists');
			$query = $dataTable->find('all', ['conditions'=>['customer_id'=>$userId,'product_id'=>$itemId]])->toArray();
			if(count($query) > 0){
				$message = 'Sorry, Item already exists into your wishlist!';
			}else{
				$wList = $dataTable->newEntity();
				$wList->customer_id = $userId;
				$wList->product_id = $itemId;
				$status = ($dataTable->save($wList)) ? 1 : 0;
				$message = $status ? 'One item added into wishlist!':'Sorry, try aogain!';
			}
		}
		return ['message'=>$message, 'status'=>$status];
    }
	
	public function revomeItemFromWishlists($userId, $itemId=0) {
		$dataTable = TableRegistry::get('Wishlists');
		$wList = $dataTable->query()->delete()->where(['customer_id'=>$userId, 'product_id'=>$itemId])->execute();
		return ($wList) ? true : false;
    }


	//###########Please write code for all customer api after this ///////////////############################################		
	public function validateLogin($username, $password)
	{
		$customerTable 	= TableRegistry::get('Customers');
		$customerData	= array();
		$pass 			= md5($password);
		$customers 		= $customerTable->find('all', ['fields' => ['id', 'firstname', 'lastname', 'email', 'mobile', 'is_active', 'lognum'], 'conditions' => ['email' => $username, 'password' => $pass, 'is_active' => 'active']])->toArray();
		if(empty($customers))
		{
			$customers	= $customerTable->find('all', ['fields' => ['id', 'firstname', 'lastname', 'email', 'mobile', 'is_active', 'lognum'], 'conditions' => ['mobile' => $username, 'password' => $pass, 'is_active' => 'active']])->toArray();
		}
		if(!empty($customers))
		{
			$validDate 						= date("Y-m-d H:i:s", strtotime("+1 hour"));
			$customer						= $customerTable->get($customers[0]['id']);
			$customer->logdate 				= date("Y-m-d H:i:s");
			$customer->lognum 				= $customers[0]['lognum'] + 1;
			$customer->api_token 			= md5($validDate);
			$customer->api_token_created_at = $validDate;
			if ($customerTable->save($customer))
			{
				$customerData['id']						= $customers[0]['id'];
				$customerData['firstname']				= $customers[0]['firstname'];
				$customerData['lastname']				= $customers[0]['lastname'];
				$customerData['email']					= $customers[0]['email'];
				$customerData['mobile']					= $customers[0]['mobile'];
				$customerData['api_token']				= md5($validDate);
				$customerData['api_token_created_at']	= $validDate;
				$customerData['is_active']				= $customers[0]['is_active'];
			}
		}
		return $customerData;
	}

	public function updatePaymentGateway($customer_id, $payment_id)
	{
		if($customer_id > 0 && $payment_id > 0)
		{
			$customerTable 	= TableRegistry::get('Customers');
			$customer		= $customerTable->get($customer_id);
			$customer->cart_payment_method_id	= $payment_id;
			$customerTable->save($customer);
		}
	}
}