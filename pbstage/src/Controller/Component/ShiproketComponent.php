<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\Customer;
use Cake\Network\Http\Client;
use Cake\I18n\Time;
use Cake\Filesystem\File;

class ShiproketComponent extends Component
{
	private $token 				= 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjExNzMxMSwiaXNzIjoiaHR0cHM6XC9cL2FwaXYyLnNoaXByb2NrZXQuaW5cL3YxXC9leHRlcm5hbFwvYXV0aFwvbG9naW4iLCJpYXQiOjE1NTYwODc0MDIsImV4cCI6MTU1Njk1MTQwMiwibmJmIjoxNTU2MDg3NDAyLCJqdGkiOiIyMTQ0ZWZjODI2ZmUwYjQzMjQzNWI5MTM2ODk3NDFiYSJ9.VATfp-IXtVAdHvxVFNbCErZGPxG1SClKs8wWYXgRkPs';
	private $base_url 			= 'https://apiv2.shiprocket.in/v1/external/';
	private $seller_tin 		= '7267134092';
	private $channel_id			= 44104;
	private $client_name 		= 'PERFUMEBOOTH';
	private $pickup_location 	= 'PERFUMEBOOTH';
	//100066425, 100085361, 100066429, 100113101
	public function createToken(){
		$token = '';
		try{
			$http 		= new Client();
			$response 	= $http->post($this->base_url.'auth/login', json_encode(['email'=>'mohd.afroj@perfumebooth.com','password'=>'786afroj']), ['headers'=>['Content-Type'=>'application/json']]);
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
		return $this->createToken();
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
		$couriers = TableRegistry::get('Couriers')->find('all',[])->hydrate(false)->toArray();
		return $couriers;
	}
	
	public function getDefaultPrepaidCourier(){
		$query = TableRegistry::get('Couriers')->find('all', ['fields'=>['id'], 'conditions'=>['prepaid'=>1]])->hydrate(false)->toArray();
		return $query[0]['id'] ?? 0;
	}
	
	public function findCourier($title){
		$courier = [];
		if( !empty($title) ){
			$courier = TableRegistry::get('Couriers')->find('all',['conditions'=>['title'=>$title]])->hydrate(false)->toArray();
		}
		$courier = $courier[0] ?? [];
		return $courier;
	}
	
	public function testPincode($pincode){
		$params		= ['cod'=>0,'weight'=>1,'pickup_postcode'=>110015,'delivery_postcode'=>$pincode];
		$http 		= new Client();
		$this->token = $this->createToken();
		$response 	= $http->get($this->base_url.'courier/serviceability/', $params, ['headers'=>['Authorization'=>$this->token, 'Content-Type'=>'application/json']]);
		return $response->json;
	}
	
	public function checkPincode($pincode){
		$data = $couriers = [];
		$status 	= 0;
		$service 	= '';
		$message 	= 'Sorry, service not available at pincode: '.$pincode;
        $message = 'Sorry, service not available at pincode: ' . $pincode;
		$checkPincode = TableRegistry::get('Systems')->checkInvalidPincodes($pincode);
		if ( $checkPincode !== 1 ) {
			$params		= ['cod'=>0,'weight'=>1,'pickup_postcode'=>110015,'delivery_postcode'=>$pincode];
			$http 		= new Client();
			$this->token = $this->getToken();		
			$response 	= $http->get($this->base_url.'courier/serviceability/', $params, ['headers'=>['Authorization'=>$this->token, 'Content-Type'=>'application/json']]);
			$response 	= $response->json; //pr($response); die;
			$response 	= $response['data']['available_courier_companies'] ?? [];
			if( count($response) ){
				$status = 1;
				$service 	= 'both';
				$message = 'Prepaid and Postpaid both are available!';
				$companyId = [50,1,48,10,33,14]; //wow,bluedart,ekart,delhivery..., couriers company id
				foreach($companyId as $id){
					foreach($response as $value){
						if( $value['courier_company_id'] == $id ){
							$couriers[] = [
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
						$couriers[] = [
							'id'=>$value['courier_company_id'],
							'name'=>$value['courier_name'],
							'cod'=>$value['cod'],
							'delivery_days'=>$value['estimated_delivery_days']
						];
					}
				}
			}
			$data['couriers'] = $couriers;
			$data['service'] = $service;
		} else {
			$message = 'Sorry, Service not provided by PerfumeBooth at pincode: '.$pincode;
		}

		return ['message'=>$message, 'status'=>$status, 'data'=>$data];
	}
	
	public function calculateWeightSize(array $items){
		$deadWeight = $boxWeight = 0; //dead weight in grms and box weight in kg
		$orderProducts = TableRegistry::get('Products')->find('all', ['fields'=>['id','dead_weight','box_weight'],'conditions'=>['id IN'=>array_column($items, 'product_id')]])->hydrate(false)->toArray();
		foreach($orderProducts as $value){
			foreach($items as $v){
				if( $v['product_id'] == $value['id'] ){
					$deadWeight += $value['dead_weight'] * $v['qty'];
					$boxWeight = ($boxWeight > $value['box_weight']) ? $boxWeight : $value['box_weight'];
					break;
				}
			}
		}
		$deadWeight = (float)($deadWeight / 1000); 
		$weight = ($boxWeight > $deadWeight) ? $boxWeight : $deadWeight;
		$weight = ($weight > 0.45) ? $weight : 0.450; //in kg
		//debug($weight); die;
		return $weight;
	}

	public function sendOrder($orderId, $courierId){
		$status     = 0;
		$orderArray	= [];
		try{
			$orderTable = TableRegistry::get('Orders');
			$orders 	= $orderTable->find('all',['fields'=>['id','created','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_email','shipping_pincode','shipping_phone','payment_mode','ship_amount','discount','payment_amount'],'conditions'=>['id'=>$orderId]])
			->contain(['OrderDetails'=>function($q){
				return $q->select(['order_id','product_id','title','sku_code','qty','price','tax_amount','discount']);
			}])
			->hydrate(false)->toArray();
			foreach($orders as $order){
				$addr = str_replace([':','.'], " ", $order['shipping_address']);
				$addr = trim($addr);
				$addlen = strlen($addr);
				if( $addlen > 80 ){
					$addr1 = substr($addr, 0, 77);
					$addr2 = substr($addr, 77, 79);
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
				$orderArray['height']				= "8.8"; //8.9
				$orderArray['isd_code']				= "+91";
				$orderArray['length']				= "17.4";  //17.5
				$orderArray['mode']					= "air";
				$orderArray['order_date']			= date('Y-m-d h:m:s A', strtotime($order['created']));
				$orderArray['order_id']				= (int)$orderId;
				$orderArray['order_items']   		= array_map(function($value){
					return [
						'discount'=> (float)$value['discount'],
						'hsn'=> $value['product_id'],
						'name' => $value['title'],
						'selling_price'=> (float)$value['price'],
						'sku'=> $value['sku_code'],
						'tax'=> (float)$value['tax_amount'],
						'units'=> (int)$value['qty']
					];
				},$order['order_details']);

				$orderArray['payment_method']	    = ($order['payment_mode'] == 'postpaid') ? "COD":"Prepaid";
				$orderArray['shipping_charges']     = 0; //(int)$order['ship_amount'];
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
				$orderArray['weight']				= $this->calculateWeightSize($order['order_details']); //in kg
			} 
			//pr($orderArray);die;
			$orderArray = json_encode($orderArray);
			$http 		  = new Client();
			$this->token  = $this->getToken();
			$result 	  = $http->post($this->base_url.'shipments/create/forward-shipment', $orderArray, ['headers'=>['Accept'=>'application/json', 'Content-Type'=>'application/json', 'Authorization'=>$this->token]]);
			$saveRes 	  = $result->json;
			//pr($saveRes); 
			if( isset($saveRes['status']) && ($saveRes['status'] == 1) ) {
				$status     				     = 1;
				$awb 							 = $saveRes['payload']['awb_code'] ?? '';
				$courierId 						 = $saveRes['payload']['courier_company_id'] ?? 0;
				$orderUpdate                	 = $orderTable->get($orderId);
				$orderUpdate->tracking_code 	 = $awb;
				$orderUpdate->delhivery_pickup_id = $courierId;
				$orderUpdate->delhivery_response = serialize($saveRes); //https://www.perfumebooth.com/pb/api/zipcodes/api/100066429
				if( $orderTable->save($orderUpdate) ){
					$invoiceTable		    			= TableRegistry::get('Invoices');
					$invoice   							= $invoiceTable->find('all', ['fields'=>['id'],'conditions'=>['order_number'=>$orderId]])->hydrate(false)->toArray();
					$invoiceId 							= $invoice[0]['id'] ?? 0;
					if( $invoiceId > 0 ){
						$invoiceUpdate						= $invoiceTable->get($invoiceId);
						$invoiceUpdate->tracking_code		= $awb;
						$invoiceUpdate->pickup_id 			= $courierId;
						$invoiceUpdate->delhivery_response	= serialize($saveRes);
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
			$orderTable = TableRegistry::get('Orders');
			$orders 	= $orderTable->find('all',['fields'=>['id','created','shipping_firstname','shipping_lastname','shipping_address','shipping_city','shipping_state','shipping_country','shipping_email','shipping_pincode','shipping_phone','payment_mode','ship_amount','discount','payment_amount'],'conditions'=>['id'=>$orderId]])
			->contain(['OrderDetails'=>function($q){
				return $q->select(['order_id','product_id','title','sku_code','qty','price','size','tax_amount','discount']);
			}])
			->hydrate(false)->toArray();
			foreach($orders as $order){
				$addr = str_replace([':','.'], " ", $order['shipping_address']);
				$addr = trim($addr);
				$addlen = strlen($addr);
				if( $addlen > 80 ){
					$addr1 = substr($addr, 0, 77);
					$addr2 = substr($addr, 77, 79);
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
				$orderArray['height']				= "8.8"; //8.9
				$orderArray['isd_code']				= "+91";
				$orderArray['length']				= "17.4"; //17.5
				$orderArray['mode']					= "air";
				$orderArray['order_date']			= date('Y-m-d h:m:s A', strtotime($order['created']));
				$orderArray['order_id']				= (int)$orderId;
				$orderArray['order_items']   		= array_map(function($value){
					return [
						'discount'=> (float)$value['discount'],
						'hsn'=> $value['product_id'],
						'name' => $value['title'],
						'selling_price'=> (float)$value['price'],
						'sku'=> $value['sku_code'],
						'tax'=> (float)$value['tax_amount'],
						'units'=> (int)$value['qty']
					];
				},$order['order_details']);

				$orderArray['payment_method']	    = ($order['payment_mode'] == 'postpaid') ? "COD":"Prepaid";
				$orderArray['shipping_charges']     = 0; //(int)$order['ship_amount'];
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
				$orderArray['weight']				= $this->calculateWeightSize($order['order_details']); //in kg
			}
			$orderArray = json_encode($orderArray);
			//pr($orderArray);die;
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
					$courierId 	= $saveRes['payload']['courier_company_id'] ?? 0;
				}
			}
			//pr($saveRes); 
			if( $status > 0 ) {
				$orderUpdate                	 = $orderTable->get($orderId);
				$orderUpdate->tracking_code 	 = $awb;
				$orderUpdate->delhivery_pickup_id = $courierId;
				$orderUpdate->delhivery_response = serialize($saveRes); //https://www.perfumebooth.com/pb/api/zipcodes/api/100066429
				if( $orderTable->save($orderUpdate) ){
					$invoiceTable		    			= TableRegistry::get('Invoices');
					$invoice   							= $invoiceTable->find('all', ['fields'=>['id'],'conditions'=>['order_number'=>$orderId]])->hydrate(false)->toArray();
					$invoiceId 							= $invoice[0]['id'] ?? 0;
					if( $invoiceId > 0 ){
						$invoiceUpdate						= $invoiceTable->get($invoiceId);
						$invoiceUpdate->tracking_code		= $awb;
						$invoiceUpdate->pickup_id 			= $courierId;
						$invoiceUpdate->delhivery_response	= serialize($saveRes);
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
					$orderTable = TableRegistry::get('Orders');
					$order	    = $orderTable->get($orderId);
					$order->cancel_response	= serialize($response);
					$orderTable->save($order);
				}
			}
		}catch(\Exception $e){}
		return $status;
	}

	public function syncStatus($orderNumbers){
		$shippingLogTable	= TableRegistry::get('OrderShippingLogs');
		$orderTable 		= TableRegistry::get('Orders');
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
		$orders 			= []; 
		//pr($ordersId); die;
		try {
			if ( empty($orderNumbers) ) {
				$result 	= $http->get($this->base_url.'orders', ['channel_id' => $this->channel_id,'page'=>1,'per_page'=>5000,'from'=>$createdFrom,'to'=>$createdTo], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
				$saveRes 	= $result->json;
				$orders     = $saveRes['data'] ?? [];
			} else {
				foreach($orderNumbers as $search) {
					$result = $http->get($this->base_url.'orders', ['channel_id' => $this->channel_id,'page'=>1,'per_page'=>5000,'search'=>$search], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
					$sav 	= $result->json;
					$res    = $sav['data'] ?? [];
					if( !empty($res) ) {
						$orders[] = [
							'channel_order_id' => $res[0]['channel_order_id'], 'status_code' => $res[0]['status_code']
						];
					}
				}
			}
		} catch(\Exception $e) { 
		}
		//pr($orders); die;
		foreach( $orders as $value ){
			$changeStatus  = '';
			$id 		   = $value['channel_order_id'];
			$newStatus 	   = (int)$value['status_code']; //if( $id == 100127471 ){ echo $newStatus; die; }
			try { 
				$order = $orderTable->get($id);
				switch( $newStatus ){
					case 7: //'for Delivered':
						if( $order->status != 'delivered' ){
							$changeStatus  = 'delivered';
							//for membership
							$log = json_decode($order->order_log,true);
							$prive = $log['prive']['apply'] ?? 0;
							if( $prive && ($order->payment_mode == 'postpaid') ){
								$memberData = $this->Membership->getPlanData($order->customer_id, $id);
								$this->Membership->add($memberData);
							}																
							$this->Store->updateWalletAfterDelivery($id);
							$this->Store->orderDelivered($id);
						}
						break;
					case 6: //'for shipped to In Transit': 
						if( $order->status != 'intransit' ){ 
							$changeStatus   = 'intransit';
							$this->Store->orderIntransit($id);
						}
						break;
					case 20: //'for In Transit': 
						if( $order->status != 'intransit' ){ 
							$changeStatus   = 'intransit';
							$this->Store->orderIntransit($id);
						}
						break;
					case 18: //'for Cancelled':	
						if( $order->status != 'cancelled' ){
							$changeStatus   = 'cancelled';
						}
						break;
					case 19: //'for Dispatched':
						if( $order->status != 'dispatched' ){
							$changeStatus   = 'dispatched';
						}
						break;
					case 15: //'for RTO':	
						if( $order->status != 'rto' ){
							$changeStatus   = 'rto';
						}
						break;
					case 11111111: //'for DTO':	
						if( $order->status != 'dto' ){
							$changeStatus   = 'dto';
						}
						break;
					default:
				}
			} catch (\Exception $e) { 
			}
			if( !empty($changeStatus) ){
				$order->status	= $changeStatus;
				$orderTable->save($order);
				$this->Store->changeInvoiceStatus($id, $changeStatus);
				$shipping_log 					= $shippingLogTable->newEntity();
				$shipping_log->order_id			= $id;
				$shipping_log->response_data	= json_encode($value, true);
				$shippingLogTable->save($shipping_log);
			}
		}			
		return 1;
	}

	public function getStatus($vendorOrderId=0)
	{
		$res 	= [];
		try{
			$this->token = $this->createToken();
			$http 		 = new Client();
			$now           		= Time::now();
            $now->timezone 		= 'Asia/Kolkata';
			$createdTo   		= $now->format('Y-m-d');
			$createdFrom 		= $now->modify('-10 days')->format('Y-m-d');
			$createdFrom 		= $createdFrom;
			$createdTo   		= $createdTo;
			$result 	 = $http->get($this->base_url.'orders', ['order_id'=>$vendorOrderId,'channel_id'=>$this->channel_id,'page'=>1,'per_page'=>5000,'from'=>$createdFrom,'to'=>$createdTo], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			//$result 	 = $http->get($this->base_url.'orders', ['order_id'=>$vendorOrderId,'channel_id'=>$this->channel_id,'page'=>1,'per_page'=>1], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$res 		 = $result->json;
		}catch(\Exception $e){}
		return $res;
	}
			
	public function getChannels()
	{
		$res 	= [];
		try{
			$this->token = $this->createToken();
			$http 		 = new Client();
			$result 	 = $http->get($this->base_url.'channels', [], ['headers' => ['Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$res 		 = $result->json;
		}catch(\Exception $e){}
		return $res;
	}
			
}
