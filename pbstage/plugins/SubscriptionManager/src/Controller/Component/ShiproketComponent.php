<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\Customer;
use Cake\Network\Http\Client;
use Cake\I18n\Time;
use Cake\Filesystem\File;

class ShiproketComponent extends Component
{
	private $seller_tin;
	private $company = [];
	private $token 			 = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjExNzMxMSwiaXNzIjoiaHR0cHM6XC9cL2FwaXYyLnNoaXByb2NrZXQuaW5cL3YxXC9leHRlcm5hbFwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NTc3MzAyNDcsImV4cCI6MTU1ODU5NDI0NywibmJmIjoxNTU3NzMwMjQ3LCJqdGkiOiJhY2ZjZmIwMWNiNjk2YjYwMWFhNjRhNzJjYThkMTljZCJ9.xQyy5rn3TwPFm0dnI_Dr5XvCv5yp8L1UGfTRMO1KWgk';
	private $base_url 		 = 'https://apiv2.shiprocket.in/v1/external/';
	private $channel_id		 = 160468; //IN18
	private $client_name 	 = '';
	private $pickup_location = '';
	private $live = 0;

	public function __construct(){
        // Change for live account
		$this->seller_tin = PC['SELLER_GST'];
		$this->company = PC['COMPANY'];
        if( $this->live ) {
            $this->token = PC['SROKET']['token'];
            $this->base_url = PC['SROKET']['base_url'];
            $this->channel_id = PC['SROKET']['channel_id'];
            $this->client_name = PC['SROKET']['client_name'];
            $this->pickup_location = PC['SROKET']['pickup_location'];
		}
	}
	
	public function createToken(){
		$token = '';
		try{
			$http 		= new Client();
			$response 	= $http->post($this->base_url.'auth/login', json_encode(['email'=>'mohd.afroj@perfumersclub.com','password'=>'786afroj']), ['headers'=>['Content-Type'=>'application/json']]);
			$response 	= $response->json;
			$token      = $response['token'] ?? '';			
		}catch(\Exception $e){
		}
		if( !empty($token) ){
			$token      = "Bearer ".$token;
		}
		return $token;
	}
	
	public function getToken(){
		$file = new File(WWW_ROOT.'Shiprocket'.DS.'shiprocket.txt'); 
		$file->open($mode = 'r', $force = false);
		$token = $file->read();
		if( $token == false ){
			$token = $file->read();
			if( $token == false ){
				$token = $this->createToken();
				if( !empty($token) ){
					$fp = fopen(WWW_ROOT.'Shiprocket'.DS.'shiprocket.txt', "w");
					fwrite($fp, $token);
					fclose($fp);
				}
			}
		}
		$file->close();
		return $token;
	}
	
	public function getCouriers(){
		$couriers = [];
		$couriers = TableRegistry::get('SubscriptionManager.Couriers')->find('all',[])->hydrate(false)->toArray();
		return $couriers;
	}
	
	public function findCourier($title){
		$courier = [];
		if( !empty($title) ){
			$courier = TableRegistry::get('SubscriptionManager.Couriers')->find('all',['conditions'=>['title'=>$title]])->hydrate(false)->toArray();
		}
		$courier = $courier[0] ?? [];
		return $courier;
	}
	
	public function checkPincode($pincode){
		$data 		= [];
		$status 	= 0;
		$message 	= 'Sorry, service not available at pincode: '.$pincode;
		$checkPincode = TableRegistry::get('SubscriptionManager.Systems')->checkInvalidPincodes($pincode);
		if ( $checkPincode !== 1 ) {
			$params		= ['cod'=>0,'weight'=>1,'pickup_postcode'=>110015,'delivery_postcode'=>$pincode];
			$http 		= new Client();
			$this->token = $this->getToken();
			$response 	= $http->get($this->base_url.'courier/serviceability/', $params, ['headers'=>['Authorization'=>$this->token, 'Content-Type'=>'application/json']]);
			$response 	= $response->json; //pr($response);
			$response 	= $response['data']['available_courier_companies'] ?? [];
			if( count($response) ){
				$status = 1;
				$message = 'Prepaid and Postpaid both are available!';
				$companyId = [41,1,10,33,14];
				foreach($companyId as $id){
					foreach($response as $value){
						if( $value['courier_company_id'] == $id ){
							$data[] = [
								'id'=>$value['courier_company_id'],
								'name'=>$value['courier_name'],
								'cod'=>$value['cod'],
								'delivery_days'=>$value['estimated_delivery_days']
							];
							break;
						}
					}
				}
				foreach($response as $value){
					if( !in_array($value['courier_company_id'], $companyId) ){
						$data[] = [
							'id'=>$value['courier_company_id'],
							'name'=>$value['courier_name'],
							'cod'=>$value['cod'],
							'delivery_days'=>$value['estimated_delivery_days']
						];
					}
				}
			}
		} else {
			$message = 'Sorry, Service not provided by '.PC['COMPANY']['tag'].' at pincode: '.$pincode;
		}
		return ['message'=>$message,'status'=>$status,'data'=>$data];
	}
	
	public function sendOrder($orderId, $courierId){
		$status     = 0;
		$orderArray	= [];
		try{
			$orderTable = TableRegistry::get('SubscriptionManager.Orders');
			$orders 	= $orderTable->find('all',['fields'=>['id','created','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_email','shipping_pincode','shipping_phone','payment_mode','ship_amount','discount','payment_amount'],'conditions'=>['id'=>$orderId]])
			->contain(['OrderDetails'=>function($q){
				return $q->select(['order_id','product_id','title','sku_code','qty','price','tax_amount','discount']);
			}])
			->hydrate(false)->toArray();
			foreach($orders as $order){
				$addr = trim($order['shipping_address']);
				$addlen = strlen(addr);
				if( $addlen > 80 ){
					$addr1 = substr($addr, 0, 60);
					$addr2 = substr($addr, 60, $addlen);
				}else{
					$addr1 = $addr2 = $addr;
				}
				$orderArray['billing_address']		= $addr1;
				$orderArray['billing_address_2']	= $addr2;
				$orderArray['billing_city']			= $order['shipping_city'];
				$orderArray['billing_country']		= $order['shipping_country'];
				$orderArray['billing_customer_name']= $order['shipping_firstname'].' '.$order['shipping_lastname'];
				$orderArray['billing_email']		= $order['shipping_email'];
				$orderArray['billing_isd_code']		= "+91";
				$orderArray['billing_last_name']	= $order['shipping_lastname'];
				$orderArray['billing_phone']		= $order['shipping_phone'];
				$orderArray['billing_pincode']		= $order['shipping_pincode'];
				$orderArray['billing_state']		= $order['shipping_state'];
				$orderArray['breadth']				= "15";
				$orderArray['channel_id']			= $this->channel_id;
				$orderArray['courier_id']			= $courierId;
				$orderArray['giftwrap_charges']		= 0;
				$orderArray['height']				= "8.9";
				$orderArray['isd_code']				= "+91";
				$orderArray['length']				= "17.5";
				$orderArray['mode']					= "air";
				$orderArray['order_date']			= date('Y-m-d h:m:s A', strtotime($order['created']));
				$orderArray['order_id']				= (int)$orderId;
				$orderItems							= [];
				foreach($order['order_details'] as $value){
					$orderItems[] = [
						'discount'=> (float)$value['discount'],
						'hsn'=> $value['product_id'],
						'name' => $value['title'],
						'selling_price'=> (float)$value['price'],
						'sku'=> $value['sku_code'],
						'tax'=> (float)$value['tax_amount'],
						'units'=> (int)$value['qty']
					];
				}							
				$orderArray['order_items']			= $orderItems;
				if($order['payment_mode'] == 'postpaid'){
					$orderArray['shipping_charges']	= (int)$order['ship_amount'];
					$orderArray['payment_method'] 	= "COD";
				}else{
					$orderArray['shipping_charges']	= (int)$order['ship_amount'];
					$orderArray['payment_method']	= "Prepaid";
				}
				$orderArray['shipping_charges']     = 0;
				$orderArray['pickup_location']		= "Primary";	
				$orderArray['request_pickup']		= boolval(true);
				$orderArray['reseller_name']		= $this->client_name;
				$orderArray['shipping_address']		= $addr1;
				$orderArray['shipping_address_2']	= $addr2;
				$orderArray['shipping_city']		= $order['shipping_city'];
				$orderArray['shipping_country']		= $order['shipping_country'];
				$orderArray['shipping_customer_name']= $order['shipping_firstname'];
				$orderArray['shipping_email']		= $order['shipping_email'];
				$orderArray['shipping_is_billing']	= boolval(true);
				$orderArray['shipping_last_name']	= $order['shipping_lastname'];
				$orderArray['shipping_phone']		= $order['shipping_phone'];	
				$orderArray['shipping_pincode']		= $order['shipping_pincode'];
				$orderArray['shipping_state']		= $order['shipping_state'];
				$orderArray['sub_total']			= (float)$order['payment_amount'];
				$orderArray['total_discount']		= 0.0; //(float)$order['discount'];
				$orderArray['transaction_charges']	= 0;
				$orderArray['weight']				= "0.45";
			}
			$orderArray = json_encode($orderArray);
			//pr($orderArray);//die;
			$http 		  = new Client();
			$this->token  = $this->getToken();
			$result 	  = $http->post($this->base_url.'shipments/create/forward-shipment', $orderArray, ['headers'=>['Accept'=>'application/json', 'Content-Type'=>'application/json', 'Authorization'=>$this->token]]);
			$saveRes 	  = $result->json;
			//pr($saveRes); 
			if( isset($saveRes['status']) && ($saveRes['status'] == 1) ) {
				$status     				     = 1;
				$awb 							 = $saveRes['payload']['awb_code'] ?? '';
				$orderUpdate                	 = $orderTable->get($orderId);
				$orderUpdate->tracking_code 	 = $awb;
				$orderUpdate->delhivery_pickup_id = $courierId;
				$orderUpdate->delhivery_response = json_encode($saveRes);
				if( $orderTable->save($orderUpdate) ){
					$invoiceTable		    			= TableRegistry::get('SubscriptionManager.Invoices');
					$invoice   							= $invoiceTable->find('all', ['fields'=>['id'],'conditions'=>['order_id'=>$orderId]])->hydrate(false)->toArray();
					$invoiceId 							= $invoice[0]['id'] ?? 0;
					if( $invoiceId > 0 ){
						$invoiceUpdate						= $invoiceTable->get($invoiceId);
						$invoiceUpdate->tracking_code		= $awb;
						$invoiceUpdate->pickup_id 			= $courierId;
						$invoiceUpdate->delhivery_response	= json_encode($saveRes);
						$invoiceTable->save($invoiceUpdate);
					}
				}	
			}
		}catch(\Exception $e){}
		return $status;
	}
	
	public function sendOrderByAdmin($orderId, $courierId){
		$status     = 0;
		$orderArray	= [];
		try{
			$orderTable = TableRegistry::get('SubscriptionManager.Orders');
			$orders 	= $orderTable->find('all',['fields'=>['id','created','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_email','shipping_pincode','shipping_phone','payment_mode','ship_amount','discount','payment_amount'],'conditions'=>['id'=>$orderId]])
			->contain(['OrderDetails'=>function($q){
				return $q->select(['order_id','product_id','title','sku_code','qty','price','tax_amount','discount']);
			}])
			->hydrate(false)->toArray();
			foreach($orders as $order){
				$addr = trim($order['shipping_address']);
				$addlen = strlen($addr);
				if( $addlen > 80 ){
					$addr1 = substr($addr, 0, 77);
					$addr2 = substr($addr, 77, $addlen);
				}else{
					$addr1 = $addr2 = $addr;
				}
				$city 								= substr(trim($order['shipping_city']), 0, 30);
				$orderArray['billing_address']		= $addr1;
				$orderArray['billing_address_2']	= $addr2;
				$orderArray['billing_city']			= $city;
				$orderArray['billing_country']		= $order['shipping_country'];
				$orderArray['billing_customer_name']= $order['shipping_firstname'].' '.$order['shipping_lastname'];
				$orderArray['billing_email']		= $order['shipping_email'];
				$orderArray['billing_isd_code']		= "+91";
				$orderArray['billing_last_name']	= $order['shipping_lastname'];
				$orderArray['billing_phone']		= $order['shipping_phone'];
				$orderArray['billing_pincode']		= $order['shipping_pincode'];
				$orderArray['billing_state']		= $order['shipping_state'];
				$orderArray['breadth']				= "15";
				$orderArray['channel_id']			= $this->channel_id;
				$orderArray['courier_id']			= $courierId;
				$orderArray['giftwrap_charges']		= 0;
				$orderArray['height']				= "8.9";
				$orderArray['isd_code']				= "+91";
				$orderArray['length']				= "17.5";
				$orderArray['mode']					= "air";
				$orderArray['order_date']			= date('Y-m-d h:m:s A', strtotime($order['created']));
				$orderArray['order_id']				= (int)$orderId;
				$orderItems							= [];
				foreach($order['order_details'] as $value){
					$orderItems[] = [
						'discount'=> (float)$value['discount'],
						'hsn'=> $value['product_id'],
						'name' => $value['title'],
						'selling_price'=> (float)$value['price'],
						'sku'=> $value['sku_code'],
						'tax'=> (float)$value['tax_amount'],
						'units'=> (int)$value['qty']
					];
				}							
				$orderArray['order_items']			= $orderItems;
				if($order['payment_mode'] == 'postpaid'){
					$orderArray['shipping_charges']	= (int)$order['ship_amount'];
					$orderArray['payment_method'] 	= "COD";
				}else{
					$orderArray['shipping_charges']	= (int)$order['ship_amount'];
					$orderArray['payment_method']	= "Prepaid";
				}
				$orderArray['shipping_charges']     = 0;
				$orderArray['pickup_location']		= "Primary";	
				$orderArray['request_pickup']		= boolval(true);
				$orderArray['reseller_name']		= $this->client_name;
				$orderArray['shipping_address']		= $addr1;
				$orderArray['shipping_address_2']	= $addr2;
				$orderArray['shipping_city']		= $city;
				$orderArray['shipping_country']		= $order['shipping_country'];
				$orderArray['shipping_customer_name']= $order['shipping_firstname'];
				$orderArray['shipping_email']		= $order['shipping_email'];
				$orderArray['shipping_is_billing']	= boolval(true);
				$orderArray['shipping_last_name']	= $order['shipping_lastname'];
				$orderArray['shipping_phone']		= $order['shipping_phone'];	
				$orderArray['shipping_pincode']		= $order['shipping_pincode'];
				$orderArray['shipping_state']		= $order['shipping_state'];
				$orderArray['sub_total']			= (float)$order['payment_amount'];
				$orderArray['total_discount']		= 0.0; //(float)$order['discount'];
				$orderArray['transaction_charges']	= 0;
				$orderArray['weight']				= "0.45";
			}
			$orderArray = json_encode($orderArray);
			//pr($orderArray);//die;
			$http 		  = new Client();			
			$this->token  = $this->getToken();
			//check order already pushed
			$result 	 = $http->get($this->base_url.'orders', ['page'=>1,'search'=>$orderId], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$saveRes 	 = $result->json;
			$saveRes     = $saveRes['data'][0]['shipments'] ?? [];
			if( count($saveRes) ){
				$awb = $saveRes[0]['awb'] ?? NULL;
				$courierName = $saveRes[0]['courier'] ?? NULL;
				$courier     = $this->findCourier($courierName);
				$courierId   = $courier['id'] ?? 0;
				$status      = 2;
			}else{
				$result 	  = $http->post($this->base_url.'shipments/create/forward-shipment', $orderArray, ['headers'=>['Accept'=>'application/json', 'Content-Type'=>'application/json', 'Authorization'=>$this->token]]);
				$saveRes 	  = $result->json;
				if( isset($saveRes['status']) && ($saveRes['status'] == 1) ) {
					$status   = 1;
					$awb 	  = $saveRes['payload']['awb_code'] ?? '';
				}
			}
			//pr($saveRes); 
			if( $status > 0 ) {
				$orderUpdate                	 = $orderTable->get($orderId);
				$orderUpdate->tracking_code 	 = $awb;
				$orderUpdate->delhivery_pickup_id = $courierId;
				$orderUpdate->delhivery_response = json_encode($saveRes);
				if( $orderTable->save($orderUpdate) ){
					$invoiceTable		    			= TableRegistry::get('SubscriptionManager.Invoices');
					$invoice   							= $invoiceTable->find('all', ['fields'=>['id'],'conditions'=>['order_id'=>$orderId]])->hydrate(false)->toArray();
					$invoiceId 							= $invoice[0]['id'] ?? 0;
					if( $invoiceId > 0 ){
						$invoiceUpdate						= $invoiceTable->get($invoiceId);
						$invoiceUpdate->tracking_code		= $awb;
						$invoiceUpdate->pickup_id 			= $courierId;
						$invoiceUpdate->delhivery_response	= json_encode($saveRes);
						$invoiceTable->save($invoiceUpdate);
					}
				}	
			}
		}catch(\Exception $e){}
		return $status;
	}

	public function getOrder($id)
	{
		$saveRes 	= [];
		try{
			$now           		= Time::now();
            $now->timezone 		= 'Asia/Kolkata';
			$createdTo   		= $now->format('Y-m-d');
			$createdFrom 		= $now->modify('-60 days')->format('Y-m-d');
			$createdFrom 		= $createdFrom;
			$createdTo   		= $createdTo;

			$this->token = $this->getToken();
			$http 		 = new Client();
			$result 	 = $http->get($this->base_url.'orders', ['search'=>$id,'channel_id'=>$this->channel_id,'page'=>1,'per_page'=>5000,'from'=>$createdFrom,'to'=>$createdTo], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$saveRes 	 = $result->json;
			$result      = $saveRes['data'] ?? [];
		}catch(\Exception $e){}
		return $saveRes;
	}
		
	public function sendPickupRequest($total_package, $date, $time){
		$inputArray								= array();
		$inputArray['pickup_location']			= $this->pickup_location;
		$inputArray['pickup_time']				= $time;
		$inputArray['pickup_date']				= $date;
		$inputArray['expected_package_count']	= $total_package;
		
		$http 		= new Client();
		$result 	= $http->post($base_url, json_encode($inputArray), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization'=>$this->token]]);
		$response 	= $result->json;
		return $response;
	}
	
	public function getLabels($shipmentIds=[]){
		$response = [];
		try{
			//$shipmentIds = [7834556,7834562,7834563,7834567,7834366];
			if( count($shipmentIds) > 0 ){
				$shipmentIds = [
					'shipment_id'=>$shipmentIds
				];
				$shipmentIds = json_encode($shipmentIds);
				$this->token = $this->getToken();
				$http 		 = new Client();
				$result 	 = $http->post($this->base_url.'courier/generate/label', $shipmentIds, ['headers'=>['Accept'=>'application/json', 'Content-Type'=>'application/json', 'Authorization'=>$this->token]]);
				$response 	 = $result->json;
				$response    = $response['label_url'] ?? '';
			}
		}catch(\Exception $e){
		}
		return $response;
	}

	public function getManifest($orderIds=[]){
		$response = [];
		try{
			//$orderIds = [7900625,7900631,7900632,7900636,7900435];
			$orderIds = [
				'order_ids'=>$orderIds
			];
			$orderIds = json_encode($orderIds);
			$this->token = $this->getToken();
			$http 		 = new Client();
			$result 	 = $http->post($this->base_url.'orders/print/manifest', $orderIds, ['headers'=>['Accept'=>'application/json', 'Content-Type'=>'application/json', 'Authorization'=>$this->token]]);
			$response 	 = $result->json;
		}catch(\Exception $e){
		}
		return $response;
	}

	public function getPickupLocation(){
		$response = [];
		try{
			$this->token = $this->getToken();
			$http 		 = new Client();
			$result 	 = $http->get($this->base_url.'settings/company/pickup', [], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$response 	 = $result->json;
		}catch(\Exception $e){
		}
		return $response;
	}

	public function getTrackingDetail($awb=0){
		$response = [];
		try{
			$this->token = $this->getToken();
			$http 		 = new Client();
			$result 	 = $http->get($this->base_url.'courier/track/awb/'.$awb, [], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$data 		 = $result->json;
			$data 		 = $data['tracking_data'] ?? [];
			$response1['shipment'] 	= $data['shipment_track'][0] ?? [];
			$response1['activities'] = $data['shipment_track_activities'] ?? []; 
			$response1['link'] 		= $data['track_url'] ?? []; 

		}catch(\Exception $e){
		}
		return $response;
	}
	
	public function cancelOrder($orderId){
		$status = 0;
		$this->token = $this->getToken();
		$http 		 = new Client();
		try{
			$result 	= $http->get($this->base_url.'orders', ['page'=>1,'per_page'=>1,'search'=>$orderId], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$saveRes 	= $result->json;
			$id 		= $saveRes['data'][0]['id'] ?? 0;
			if( !empty($id) && ($id > 0) ){
				$result 	= $http->post($this->base_url.'orders/cancel', json_encode(['ids'=>[$id]]), ['headers'=>['Content-Type'=>'application/json', 'Accept'=>'application/json', 'Authorization'=>$this->token]]);
				$response 	= $result->json; 
				if( isset($response['status']) && ($response['status'] == 200) ){
					$status  	= 1;
					$orderTable = TableRegistry::get('SubscriptionManager.Orders');
					$order	    = $orderTable->get($orderId);
					$order->cancel_response	= json_encode($response);
					$orderTable->save($order);
				}
			}
		}catch(\Exception $e){}
		return $status;
	}

	public function syncStatus(){
		try{
			$shippingLogTable	= TableRegistry::get('SubscriptionManager.OrderShippingLogs');
			$orderTable 		= TableRegistry::get('SubscriptionManager.Orders');
			$this->Membership 	= new MembershipComponent(new ComponentRegistry());
			$this->Store		= new StoreComponent(new ComponentRegistry());
			$http 				= new Client();
			$this->token 		= $this->getToken();
			$now           		= Time::now();
            $now->timezone 		= 'Asia/Kolkata';
			$createdTo   		= $now->format('Y-m-d');
			$createdFrom 		= $now->modify('-60 days')->format('Y-m-d');
			$createdFrom 		= $createdFrom;
			$createdTo   		= $createdTo;
			$result 	 = $http->get($this->base_url.'orders', ['channel_id'=>$this->channel_id,'page'=>1,'per_page'=>5000,'from'=>$createdFrom,'to'=>$createdTo], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$saveRes 	 = $result->json;
			$orders      = $saveRes['data'] ?? [];
			foreach( $orders as $value ){
				$changeStatus  = '';
				$id 		   = $value['channel_order_id'];
				$newStatus 	   = $value['status_code'];
				switch( $newStatus ){
					case 7: //'for Delivered': 
						$order		   = $orderTable->get($id);
						if( $order->status != 'delivered' ){
							$changeStatus  = 'delivered';
							$order->status	= $changeStatus;
							$orderTable->save($order);
							//for membership
							$log = json_decode($order->order_log,true);
							$prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
							if( $prive && ($order->payment_mode == 'postpaid') ){
								$memberData = $this->Membership->getPlanData($order->customer_id, $id);
								$this->Membership->add($memberData);
							}																
							$this->Store->updateWalletAfterDelivery($id);
							$this->Store->orderStatusEmails($orderId, 'delivered');
						}
						break;
					case 20: //'for In Transit':	
						$order		   = $orderTable->get($id);
						if( $order->status != 'intransit' ){
							$changeStatus   = 'intransit';
							$order->status	= $changeStatus;
							$orderTable->save($order);
							$this->Store->orderStatusEmails($id, 'intransit');
						} 
						break;
					case 18: //'for Cancelled':	
						$order		        = $orderTable->get($id);
						if( $order->status != 'cancelled' ){
							$changeStatus   = 'cancelled';
							$order->status	= $changeStatus;
							$orderTable->save($order);
						}
						break;
					case 19: //'for Dispatched':
						$order		        = $orderTable->get($id);
						if( $order->status != 'dispatched' ){
							$changeStatus   = 'dispatched';
							$order->status	= $changeStatus;
							$orderTable->save($order);
						}
						break;
					case 9: //'for RTO':	
						$order		        = $orderTable->get($id);
						if( $order->status != 'rto' ){
							$changeStatus   = 'rto';
							$order->status	= $changeStatus;
							$orderTable->save($order);
						}
						break;
					case 11111111: //'for DTO':	
						$order		        = $orderTable->get($id);
						if( $order->status != 'dto' ){
							$changeStatus   = 'dto';
							$order->status	= $changeStatus;
							$orderTable->save($order);
						}
						break;
					default:
				}
				if( !empty($changeStatus) ){
					$this->Store->changeInvoiceStatus($id, $changeStatus);
					$shipping_log 					= $shippingLogTable->newEntity();
					$shipping_log->order_id			= $id;
					$shipping_log->response_data	= json_encode($value, true);
					$shippingLogTable->save($shipping_log);
				}
			}
		}catch(\Exception $e){}
		return 1;
	}

	public function getStatus()
	{
		$res 	= [];
		try{
			$this->token = $this->getToken();
			$http 		 = new Client();
			$result 	 = $http->get($this->base_url.'shipments', ['channel_id'=>$this->channel_id], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$res 		 = $result->json;
		}catch(\Exception $e){}
		return $res;
	}
			
}
