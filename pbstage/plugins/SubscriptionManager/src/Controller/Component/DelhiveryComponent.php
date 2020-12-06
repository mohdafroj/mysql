<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use SubscriptionManager\Controller\Component\Customer;

//use Cake\Http\Client;
use Cake\Network\Http\Client;
/**
 * Admin component
 */
class DelhiveryComponent extends Component
{
	// Demo Details
	private $seller_tin;
	private $company = [];
	private $token 				= 'dbb0127bb1600515d09ac0425d9fbb32886cffa0';
	private $api 				= 'https://staging-express.delhivery.com/c/api/';
	private $api_base_url 		= 'https://staging-express.delhivery.com/';
	private $client_name 		= 'SANGRILASURFACE-B2C';
	private $user_name 			= 'SANGRILASURFACE';
	private $pickup_location 	= 'SANGRILA SURFACE';	
	private $live = 1;
	private $covidZones = ['R' => 'Red Zone', 'G' => 'Green Zone', 'O' => 'Orange Zone'];
	public function __construct(){
        // Change for live account
		$this->seller_tin = PC['SELLER_GST'];
		$this->company = PC['COMPANY'];
        if( $this->live ) {
            $this->token = PC['DLYVERY']['token'];
            $this->api = PC['DLYVERY']['api'];
            $this->api_base_url = PC['DLYVERY']['api_base_url'];
            $this->client_name = PC['DLYVERY']['client_name'];
            $this->user_name = PC['DLYVERY']['user_name'];
            $this->pickup_location = PC['DLYVERY']['pickup_location'];
		}
    }
	
	public function getPincode($pincode){
		$http = new Client();
		$data = [];
		$status = 0;		
		$message = 'Sorry, service not available at pincode: '.$pincode;
		$checkPincode = TableRegistry::get('SubscriptionManager.Systems')->checkInvalidPincodes($pincode);
		if ( $checkPincode !== 1 ) {
			$res = $http->get($this->api.'pin-codes/json/', ['token'=>$this->token,'filter_codes'=>$pincode], ['headers'=>['Accept'=>'application/json']]);
			$res = $res->json;
			//pr($res); die;
			if( isset($res['delivery_codes'][0]['postal_code']) ){
				$res = $res['delivery_codes'][0]['postal_code'];
				switch($res['state_code']){
					case 'KL_test':
						break;
					default:	
						if( $res['pre_paid'] == 'Y' && $res['cash'] == 'Y' ){
							$status = 1;
							$message = 'Prepaid and Postpaid both are available!';
						}else if( $res['pre_paid'] == 'Y' ){
							$status = 2;
							$message = 'Only Prepaid are available!';
						}else if ( $res['cash'] == 'Y' ) {
							$status = 3;
							$message = 'Only Postpaid are available!';
						}
				}
				$data['covid_zone'] = $res['covid_zone'];
				$data['district'] = $res['district'];
				$data['pincode'] = $pincode;
			}
		} else {
			$message = "Sorry, Service not provided by ".PC['COMPANY']['tag']." at pincode: ".$pincode;
		}
		
		return ['message'=>$message,'status'=>$status,'data'=>$data];
	}
	
	public function getPincodeTest($pincode){
		$http = new Client();
		$data = [];
		$status = 0;
		$message = 'Sorry, service not available at pincode: '.$pincode;
		$res = $http->get($this->api.'pin-codes/json/', ['token'=>$this->token,'filter_codes'=>$pincode], ['headers'=>['Accept'=>'application/json']]);
		$res = $res->json;
		//pr($res['delivery_codes']); die;
		if( isset($res['delivery_codes'][0]['postal_code']) ){
			$res = $res['delivery_codes'][0]['postal_code'];
			switch($res['state_code']){
				case 'KL':
					break;
				default:	
					if( $res['pre_paid'] == 'Y' && $res['cash'] == 'Y' ){
						$status = 1;
						$message = 'Prepaid and Postpaid both are available!';
					}else if( $res['pre_paid'] == 'Y' ){
						$status = 2;
						$message = 'Only Prepaid are available!';
					}else if ( $res['cash'] == 'Y' ) {
						$status = 3;
						$message = 'Only Postpaid are available!';
					}
			}
			$data['covid_zone'] = $res['covid_zone'];
			$data['district'] = $res['district'];
			$data['pincode'] = $pincode;
		}
		return ['message'=>$message,'status'=>$status,'data'=>$data];
	}

	// get client wherehouse location
	public function createClientWhereHouse(){
		$input_string = [
			'name' => '',
			'address' => '',
			'city' => '',
			'country' => '',
			'email' => '',
			'pin' => '',
			'phone' => '',
			'registered_name' => '',
			'return_address' => '',
			'return_city' => '',
			'return_state' => '',
			'return_country' => '',
			'return_pin' => ''
		];
		$http 	 = new Client();
		$api_url = $this->api_base_url.'api/backend/clientwarehouse/create/';
		$result  = $http->post($api_url, json_encode($input_string), ['headers' => ['Accept'=>'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
		$res 	 = $result->json;
		$res 	 = $res->json ?? [];
		return $res;
	}
	
	// get a WayBill Number
	public function getWayBillNumber(){
		$http = new Client();
		$res = $http->get($this->api_base_url.'waybill/api/fetch/json/', ['token'=>$this->token,'client_name'=>$this->client_name], ['headers'=>['Accept'=>'application/json']]);
		$wbn = $res->json ?? 0;
		return $wbn;
	}
	
	public function sendOrder($orderId)
	{
		if($orderId > 0)
		{
			$orderTable 	= TableRegistry::get('SubscriptionManager.Orders');
			$order			= $orderTable->get($orderId);
			
			$orderArray						= [];
			$orderArray['shipments']		= [];
			$orderArray['shipments'][0]		= [];
			$orderArray['pickup_location']	= [];
			$waybill = $this->getWayBillNumber();
			
			# order details #
			$orderArray['shipments'][0]['order']			= $orderId;
			$orderArray['shipments'][0]['waybill']			= $waybill;
			$orderArray['shipments'][0]['order_date']		= date('Y-m-d H:m:s', strtotime($order->created));
			$orderArray['shipments'][0]['total_amount']	= $order->payment_amount;
			if($order->payment_mode == 'postpaid')
			{
				$orderArray['shipments'][0]['cod_amount']	= $order->payment_amount;
				$orderArray['shipments'][0]['payment_mode']= 'COD';
			}
			else
			{
				$orderArray['shipments'][0]['cod_amount']	= 0;
				$orderArray['shipments'][0]['payment_mode']= 'Pre­paid';
			}
			
			$invoiceTable		= TableRegistry::get('SubscriptionManager.Invoices');
			$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_id' => $orderId]])->toArray();
			$invoice			= $invoiceData[0];
			$orderDetailTable	= TableRegistry::get('SubscriptionManager.OrderDetails');
			$orderDetailData	= $orderDetailTable->find('all', ['conditions' => ['order_id' => $orderId]])->toArray();
			$products_desc		= '';
			$total_quantity		= 0;
			foreach($orderDetailData as $order_detail)
			{
				// $products_desc	= $products_desc.", ".$order_detail->title;
				$products_desc	= $order_detail->title;
				$total_quantity	= $total_quantity + $order_detail->qty;
			}
			$products_desc		= trim($products_desc, ', ');
			$orderArray['shipments'][0]['products_desc']	= $products_desc;
			
			# customer details #
			$orderArray['shipments'][0]['client']			= $this->client_name;
			$orderArray['shipments'][0]['name']				= $order->shipping_firstname." ".$order->shipping_lastname;
			$orderArray['shipments'][0]['add']				= $order->shipping_address;
			$orderArray['shipments'][0]['city']				= $order->shipping_city;
			$orderArray['shipments'][0]['state']			= $order->shipping_state;
			$orderArray['shipments'][0]['country']			= 'India';
			$orderArray['shipments'][0]['pin']				= $order->shipping_pincode;
			$orderArray['shipments'][0]['phone']			= $order->shipping_phone;
			
			# return details #
			$orderArray['shipments'][0]['return_name']				= $this->company['name'];
			$orderArray['shipments'][0]['return_add']				= $this->company['add'];
			$orderArray['shipments'][0]['return_city']				= $this->company['city'];
			$orderArray['shipments'][0]['return_state']				= $this->company['state'];
			$orderArray['shipments'][0]['return_country']			= $this->company['country'];
			$orderArray['shipments'][0]['return_pin']				= $this->company['pin'];
			$orderArray['shipments'][0]['return_phone']				= $this->company['phone'];
			$orderArray['shipments'][0]['supplier']					= $this->company['name'];
			
			# seller weight and dimensions #
			$orderArray['shipments'][0]['shipment_width']			= '17.4';
			$orderArray['shipments'][0]['shipment_height']			= '8.7';
			$orderArray['shipments'][0]['weight']					= '300';
			$orderArray['shipments'][0]['quantity']					= $total_quantity;
			
			# seller keys(optional) #
			$orderArray['shipments'][0]['seller_name']				= $this->company['name'];
			$orderArray['shipments'][0]['seller_inv']				= $invoice->id;
			$orderArray['shipments'][0]['seller_inv_date']			= date('Y-m-d H:m:s', strtotime($invoice->created));
			$orderArray['shipments'][0]['seller_add']				= $this->company['add']. ', '. $this->company['city']. ' - '.$this->company['pin'];
			$orderArray['shipments'][0]['seller_cst']				= $this->seller_tin;
			$orderArray['shipments'][0]['seller_tin']				= $this->seller_tin;
			
			# pickup location #
			$orderArray['pickup_location']['name']		= $this->client_name;
			$orderArray['pickup_location']['add']		= $this->company['add'];
			$orderArray['pickup_location']['city']		= $this->company['city'];
			$orderArray['pickup_location']['country']	= $this->company['country'];
			$orderArray['pickup_location']['pin']		= $this->company['pin'];
			$orderArray['pickup_location']['phone']		= $this->company['phone'];
			// pr($orderArray);
			$input_string			= 'format=json&data='.json_encode($orderArray);
			
			$api_url	= $this->api_base_url.'api/cmu/create.json';
			$http 		= new Client();
			$result 	= $http->post($api_url, $input_string, ['headers' => ['Accept'=>'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
			$response 	= $result->json;
			// pr($response);
			
			return $response;
		}
	}
	
	public function sendOrderNew($orderId)
	{
		$status = 0;
		if($orderId > 0)
		{
			$orderTable 	= TableRegistry::get('SubscriptionManager.Orders');
			$order			= $orderTable->get($orderId);
			$invoiceTable		= TableRegistry::get('SubscriptionManager.Invoices');
			$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_id' => $orderId]])->toArray();
			$invoice			= $invoiceData[0] ?? [];
			$orderDetailData	= TableRegistry::get('SubscriptionManager.OrderDetails')->find('all', ['conditions' => ['order_id' => $orderId]])->toArray();
			if ( !empty($invoice) ) {
				$orderArray						= [];
				$orderArray['shipments']		= [];
				$orderArray['shipments'][0]		= [];
				$orderArray['pickup_location']	= [];
				$waybill = $this->getWayBillNumber();
				
				# order details #
				$orderArray['shipments'][0]['order']			= $orderId;
				$orderArray['shipments'][0]['waybill']			= $waybill;
				$orderArray['shipments'][0]['order_date']		= date('Y-m-d H:m:s', strtotime($order->created));
				$orderArray['shipments'][0]['total_amount']	= $order->payment_amount;
				if($order->payment_mode == 'postpaid')
				{
					$orderArray['shipments'][0]['cod_amount']	= $order->payment_amount;
					$orderArray['shipments'][0]['payment_mode']= 'COD';
				}
				else
				{
					$orderArray['shipments'][0]['cod_amount']	= 0;
					$orderArray['shipments'][0]['payment_mode']= 'Pre­paid';
				}
				
				$products_desc		= '';
				$total_quantity		= 0;
				foreach($orderDetailData as $order_detail)
				{
					// $products_desc	= $products_desc.", ".$order_detail->title;
					$products_desc	= $order_detail->title;
					$total_quantity	= $total_quantity + $order_detail->qty;
				}
				$products_desc		= trim($products_desc, ', ');
				$orderArray['shipments'][0]['products_desc']	= $products_desc;
				
				# customer details #
				$orderArray['shipments'][0]['client']			= $this->client_name;
				$orderArray['shipments'][0]['name']				= $order->shipping_firstname." ".$order->shipping_lastname;
				$orderArray['shipments'][0]['add']				= $order->shipping_address;
				$orderArray['shipments'][0]['city']				= $order->shipping_city;
				$orderArray['shipments'][0]['state']			= $order->shipping_state;
				$orderArray['shipments'][0]['country']			= 'India';
				$orderArray['shipments'][0]['pin']				= $order->shipping_pincode;
				$orderArray['shipments'][0]['phone']			= $order->shipping_phone;
				
				# return details #
				$orderArray['shipments'][0]['return_name']				= $this->company['name'];
				$orderArray['shipments'][0]['return_add']				= $this->company['add'];
				$orderArray['shipments'][0]['return_city']				= $this->company['city'];
				$orderArray['shipments'][0]['return_state']				= $this->company['state'];
				$orderArray['shipments'][0]['return_country']			= $this->company['country'];
				$orderArray['shipments'][0]['return_pin']				= $this->company['pin'];
				$orderArray['shipments'][0]['return_phone']				= $this->company['phone'];
				$orderArray['shipments'][0]['supplier']					= $this->company['name'];
				
				# seller weight and dimensions #
				$orderArray['shipments'][0]['shipment_width']			= '17.4';
				$orderArray['shipments'][0]['shipment_height']			= '8.7';
				$orderArray['shipments'][0]['weight']					= '300';
				$orderArray['shipments'][0]['quantity']					= $total_quantity;
				
				# seller keys(optional) #
				$orderArray['shipments'][0]['seller_name']				= $this->company['name'];
				$orderArray['shipments'][0]['seller_inv']				= $invoice->id;
				$orderArray['shipments'][0]['seller_inv_date']			= date('Y-m-d H:m:s', strtotime($invoice->created));
				$orderArray['shipments'][0]['seller_add']				= $this->company['add']. ', '. $this->company['city']. ' - '.$this->company['pin'];
				$orderArray['shipments'][0]['seller_cst']				= $this->seller_tin;
				$orderArray['shipments'][0]['seller_tin']				= $this->seller_tin;
				
				# pickup location #
				$orderArray['pickup_location']['name']		= $this->pickup_location;
				$orderArray['pickup_location']['add']		= $this->company['add'];
				$orderArray['pickup_location']['city']		= $this->company['city'];
				$orderArray['pickup_location']['country']	= $this->company['country'];
				$orderArray['pickup_location']['pin']		= $this->company['pin'];
				$orderArray['pickup_location']['phone']		= $this->company['phone'];
				//pr($orderArray);  //Check data format before api call
				$input_string			= 'format=json&data='.json_encode($orderArray);			
				$api_url	= $this->api_base_url.'api/cmu/create.json';
				$http 		= new Client();
				$result 	= $http->post($api_url, $input_string, ['headers' => ['Accept'=>'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
				$response 	= $result->json;
				//pr($response); die; // get api response
				if( isset($response['success']) ) {
					$status = 1;
					$packingSlip				= $this->getPackingSlip($waybill);
					$order->tracking_code	= $waybill;
					$order->courier_id		= 3; //pickupId
					$order->packing_slip	= json_encode($packingSlip);
					$orderTable->save($order);

					$invoiceU				 = $invoiceTable->get($invoice->id);
					$invoiceU->tracking_code = $waybill;
					$invoiceU->courier_id	 = 3; //pickupId
					$invoiceTable->save($invoiceU);		
				}
			}
		}
		return $status;
	}
	
	public function getPackingSlip($waybill)
	{
		$response = [];
		if($waybill != '')
		{
			$api_url	= $this->api_base_url.'api/p/packing_slip/';
			$http 		= new Client();
			$result 	= $http->get($api_url, ['wbns' => $waybill], ['headers' => ['Accept' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
			$response 	= $result->json;
		}
		return $response;
	}
	
	public function getPackingSlipTest($waybill)
	{
		$response = [];
		if($waybill != '')
		{
			$api_url	= $this->api_base_url.'api/p/packing_slip/';
			$http 		= new Client();
			$result 	= $http->get($api_url, ['wbns' => $waybill], ['headers' => ['Accept' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
			$response 	= $result->json;
		}
		return $response;
	}
	
	public function sendPickupRequest($total_package, $date, $time)
	{
		$data	= [
			'pickup_location' => $this->pickup_location,
			'pickup_time' => $time,
			'pickup_date' => $date,
			'expected_package_count' => $total_package
		];		
		$http 		= new Client();
		$result 	= $http->post($this->api_base_url.'fm/request/new/', json_encode($data), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
		$response 	= $result->json;
		return $response;
	}
	
	public function cancelOrder($waybill)
	{	
		$response = [];
		if($waybill != '')
		{
			$data = [
				'waybill' => $waybill,
				'cancellation' => 'true'
			];
			$http 		= new Client();
			$result 	= $http->post($this->api_base_url.'api/p/edit', json_encode($data), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
			$response 	= $result->json;
		}
		return $response;
	}

	public function cancelOrderNew($orderId)
	{
		$status = 0;
		$orderTable   = TableRegistry::get('SubscriptionManager.Orders');
		$order	      = $orderTable->get($orderId);
		if( !empty($order->tracking_code) )
		{
			$data = [
				'waybill'=>$order->tracking_code,
				'cancellation'=>'true'
			];
			$http 		= new Client();
			$result 	= $http->post($this->api_base_url.'api/p/edit', json_encode($data), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization' => 'Token '.$this->token]]);
			$response 	= $result->json;
			if( isset($response['status']) && ($response['status'] == 1 ) ){
				$status  	= 1;
				$order->cancel_response	= json_encode($response);
				$orderTable->save($order);
			}
		}
		return $status;
	}

	//Track order and update order status
	public function trackOrders ($awbs) {
		$this->Store = new StoreComponent(new ComponentRegistry());
		$data = [
			'waybill' => $awbs,
			'verbose' => 0, 
			'token' => $this->token
		];
		$http 		= new Client();
		$result 	= $http->get($this->api_base_url.'api/v1/packages/json/', $data, ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json']]);
		$response 	= $result->json;
		$response   = $response['ShipmentData'] ?? [];
		//pr($response);
		foreach ($response as $value ) {
			$shipment = $value['Shipment'];
			$orderId = $shipment['ReferenceNo'] ?? ''; //10000224
			$status = $shipment['Status']['Status'] ?? '';
			$statusType = $shipment['Status']['StatusType'] ?? '';
			try {
				$newStatus = '';
				$orderTable = TableRegistry::get('SubscriptionManager.Orders');
				$order = $orderTable->get($orderId);
				$oldStatus = $order->status;
				switch ($status) {
					case 'Delivered' : 
						$newStatus = 'delivered';
						$order->status = $newStatus;
						$orderTable->save($order);
						if ( $oldStatus != $newStatus ) {
							$this->Store->updateWalletAfterDelivery($orderId);
							$this->Store->orderStatusEmails($orderId, 'delivered');
						}
						break;
					case 'In Transit' :
						$newStatus = 'intransit';
						$order->status = $newStatus;
						$orderTable->save($order);
						if ( $oldStatus != $newStatus ) {
							$this->Store->orderStatusEmails($orderId, $newStatus);
						}
						break;
					case 'Dispatched' :
						$newStatus = 'dispatched';
						$order->status = $newStatus;
						$orderTable->save($order);
						if ( $oldStatus != $newStatus ) {
							$this->Store->orderStatusEmails($orderId, $newStatus);
						}
						break;
					case 'Cancelled' :
						$newStatus = 'cancelled';
						$order->status = $newStatus;
						$orderTable->save($order);
						if ( $oldStatus != $newStatus ) {
							$this->Store->orderStatusEmails($orderId, $newStatus);
						}
						break;
					case 'RTO' :
						$newStatus = 'rto';
						$order->status = $newStatus;
						$orderTable->save($order);
						break;
					case 'DTO' :
						$newStatus = 'dto';
						$order->status = $newStatus;
						$orderTable->save($order);
						break;
					default:
				}
				if ( !empty($newStatus) ) {
					$this->Store->changeInvoiceStatus($orderId, $newStatus);
				}
			} catch (\Exception $e) {

			}
		}
		return 1;
	}
}
