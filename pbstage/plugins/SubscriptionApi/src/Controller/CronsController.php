<?php
namespace SubscriptionApi\Controller;

use Cake\ORM\TableRegistry;
use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Utility\Security;
use Cake\I18n\Time;
use Cake\Filesystem\File;
use Cake\View\View;
use Cake\View\ViewBuilder;
//use Cake\I18n\Date;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
class CronsController extends AppController
{
    use MailerAwareTrait;
    private $skus = ['PB00000051','PB00000052','PB00000053','PB00000054','PB00000055','PB00000056','PB00000057','PB00000058','PB00000059','PB00000060','PB00000061','PB00000062'];    
	public function initialize()
    {
        parent::initialize();
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$this->loadComponent('SubscriptionApi.Store');
		$this->loadComponent('SubscriptionApi.Membership');
		$this->loadComponent('SubscriptionApi.Drift');
		$this->loadComponent('SubscriptionApi.Customer');
		$this->loadComponent('SubscriptionApi.Shiproket');
		$this->loadComponent('SubscriptionApi.Shipvendor');
		$this->loadComponent('SubscriptionApi.Sms');
    }
    
    public function index(){
		//$this->Sms->registerOtp(8178614313, 123456);
        die;
    }
    
    public function sendMailers(){
		//https://www.perfumersclub.com/pb/subscription-api-v1.0/crons/send-mailers?customer_id=115759&mailer_id=3
		$customerId 	= $this->request->getQuery('customer_id', 0);
		$mailerId 		= $this->request->getQuery('mailer_id', 0);
		$where 			= ['bucket_id !='=>1,'status'=>'active'];
		if ( $mailerId > 0 ) {
			//$this->response->type('text/html');
			$where['id'] = $mailerId;
		}
        $customerTable 	= TableRegistry::get('SubscriptionApi.Customers');
        $orderTable 	= TableRegistry::get('SubscriptionApi.Orders');
        $dataTable 	    = TableRegistry::get('SubscriptionApi.DriftMailers');  // 7 for delivered, 8 for cart, 10 for membership 
        $mailers 	    = $dataTable->find('all', ['conditions'=>$where])->hydrate(0)->toArray();
        foreach($mailers as $value){
            $status         = 0;
            $now1           = Time::now();
            $now1->timezone = 'Asia/Kolkata';
            $currentMinutes = $now1->minute;
            $currentHours   = $now1->hour;
            switch($value['schedule_id']){
                case 1:// for One time in a day
                    //$value['send_at'] = '10:46 AM';
                    if( $currentHours ==  date('H', strtotime($value['send_at'])) ){
                        $givenMinutes = (int) substr($value['send_at'], 3,2); //find minutes
                        if( ($currentMinutes <=  $givenMinutes) && ($givenMinutes < ($currentMinutes + 5)) ){
                            $status = 1;
                        }
                    }
                    break;
                case 2: // for custom hours
                    $givenHours = (int) $value['send_at'];
                    if( ($givenHours <= $currentHours) ){
                        if( ($currentHours % $givenHours == 0) && ($currentMinutes < 5) ){
                            $status = 1;
                        }
                    }else{
                        if( ($currentHours == 23) && ($currentMinutes > 54) && ($currentMinutes < 60) ){
                            $status = 1;
                        }
                    }
                    break;
                default:
			}
			if ( $customerId > 0 ) { $status = 1; }			
            if ( $status ) {
                //echo 'ok'; die;
                $senderList     			= [];
                $senderList['mailer_id'] 	= $value['id'];
                $senderList['subject'] 		= $value['subject'];
                $senderList['content'] 		= $value['content'];
				$senderList['sender']  		= !empty($value['sender_name']) ? $value['sender_name']:'Connect';
				$senderList['sender_email'] = $value['sender_email'];
                $senderList['utm_source'] 	= $value['utm_source'];
                $senderList['utm_medium'] 	= $value['utm_medium'];
                $senderList['utm_campaign'] = $value['utm_campaign'];
                $senderList['utm_term'] 	= $value['utm_term'];
                $senderList['utm_content']  = $value['utm_content'];
				$conditions 		   		= empty($value['conditions']) ? '[]':json_decode($value['conditions'], true);
				$schedule_type 		   		= $conditions['schedule_type'] ?? [];
				$start 				   		= $conditions['start'] ?? [];
				$end 				   		= $conditions['end'] ?? [];
				
				$schedule_type1 = $start1 = $end1 = [];
				foreach($schedule_type as $key=>$value1){
					if( $value1 != 0 ){ $schedule_type1[$key]=$value1; }
				}
				$start1 = array_intersect_key($start, $schedule_type1);
				$end1 = array_intersect_key($end, $schedule_type1);
				$conditions = [];
				foreach($schedule_type1 as $key=>$value1){
					$conditions[$key] = [
						'schedule_type'=>$schedule_type[$key],
						'start'=>$start[$key],
						'end'=>$end[$key]
					];
				}
			
				$customersIds = [];
				foreach($conditions as $key=>$value1){
                    $now  			= Time::now();
					$now->timezone  = 'Asia/Kolkata';
					if( $value1['schedule_type'] == 2 ){ //calculate according to hours
						$createdTo   	= $now->modify('- '.$value1['start'].' hours')->format('Y-m-d H:m:s');
						$createdFrom 	= $now->modify('- '.$value1['end'].' hours')->format('Y-m-d H:m:s');
					}else{ // calculate according to days
						$value1['end'] 	= $value1['end'] - 1;
						$createdTo   = $now->modify('- '.$value1['start'].' days')->format('Y-m-d');
						$createdFrom = $now->modify('- '.$value1['end'].' days')->format('Y-m-d');
						$createdFrom = $createdFrom.' 00:00:01';
						$createdTo   = $createdTo.' 23:59:59';						
					}
					$custIds = []; //$key = 'delivered';
					switch($key){
						case 'delivered':
							$delivered = $orderTable->find('all', ['fields'=>['id','location_id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered']]);
							if ( $customerId > 0 ) {
								$delivered = $delivered->where(['customer_id'=>$customerId]);
							} else {
								$delivered = $delivered->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('Orders.modified', $createdFrom, $createdTo);
								});	
							}
							$delivered = $delivered->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter','valid_email']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','quantity']);
								},
								'Locations'=>function($q){
									return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
								}
							])
                            ->hydrate(false)->toArray(); //pr($delivered);die;
							$this->Drift->getDeliveredProducts($delivered, $senderList);
							break;
						case 'repeated':
							$repeated = $orderTable->find('all',['fields'=>['id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered'], 'having'=>['count(*) >'=>1]]);
							if ( $customerId > 0 ) {
								$repeated = $repeated->where(['customer_id'=>$customerId]);
							} else {
								$repeated = $repeated->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('Orders.modified', $createdFrom, $createdTo);
								});
							}
							$repeated = $repeated->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter', 'valid_email']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','quantity']);
								},
								'Locations'=>function($q){
									return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
								}
							])
                            ->hydrate(false)->toArray();
							$this->Drift->getDeliveredProducts($repeated, $senderList);
							break;
                        case 'cart':                            
							$cart  = $customerTable->find();
							$cart  = $cart->select(['id','email', 'name'=>$cart->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
							->contain(['Carts'=>[
									'fields'=>['customer_id','product_id', 'Carts.created'],
								]
							])
							->innerJoinWith('Carts', function($q) use ($createdFrom, $createdTo, $customerId){
								if ( $customerId > 0 ) {
									$q = $q->where(['Carts.customer_id'=>$customerId]);
								} else {
									$q = $q->where(['Carts.created >'=>$createdFrom, 'Carts.created <'=>$createdTo]);
								}
								return $q;
							})
							->where(['is_active'=>'active','newsletter'=>1,'valid_email'=>'1'])
							->group(['Customers.id'])
							->hydrate(false)->toArray();
                            $this->Drift->getCartProducts($cart, $senderList);
							break;
						case 'member':
							$custIds = $customerTable->find('all',['fields'=>['id'],'group'=>['Customers.id'],'conditions'=>['is_active'=>'active','newsletter'=>1]]);
							if ( $customerId > 0 ) {
								$custIds = $custIds->where(['id'=>$customerId]);
							} else {
								$custIds = $custIds->innerJoinWith('Memberships')
								->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('Memberships.created', $createdFrom, $createdTo);
								});
							}
							$custIds = $custIds->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //84960
							break;
						case 'never':
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active']]);
							if ( $customerId > 0 ) {
								$custIds = $custIds->where(['id'=>$customerId]);
							} else {
								$custIds = $custIds->where(['logdate >'=>$createdFrom, 'logdate <'=>$createdTo]);
							}
							/*->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('logdate NOT', $createdFrom, $createdTo);
							})*/
							$custIds = $custIds->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id');
							break;
						case 'pending':
							$custIds = $orderTable->find('all',['fields'=>['customer_id'],'group'=>['customer_id'], 'conditions'=>['status'=>'pending']]);
							if ( $customerId > 0 ) {
								$custIds = $custIds->where(['customer_id'=>$customerId]);
							} else {
								$custIds = $custIds->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('modified', $createdFrom, $createdTo);
								});
							}
							$custIds = $custIds->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'customer_id'); //2664
							break;
						case 'logout':
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1]]);
							if ( $customerId > 0 ) {
								$custIds = $custIds->where(['id'=>$customerId]);
							} else {
								$custIds = $custIds->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('logdate', $createdFrom, $createdTo);
								});							
							}
							$custIds = $custIds->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //115391
							break;
						default:
							if ( $key > 0 ) {
								$pids = TableRegistry::get('SubscriptionApi.ProductCategories')->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$key],'group'=>['product_id']])->hydrate(0)->toArray();
								$pids = array_column($pids,'product_id');
								if( count($pids) ){
									$custIds = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']]);
									if ( $customerId > 0 ) {
										$custIds = $custIds->where(['customer_id'=>$customerId]);
									} else {
										$custIds = $custIds->where(function ($exp, $q) use($createdFrom, $createdTo) {
											return $exp->between('modified', $createdFrom, $createdTo);
										});
									}
									$custIds = $custIds->innerJoinWith('OrderDetails', function($q) use($pids){
										return $q->where(['product_id IN'=>$pids]);
									})
									->hydrate(false)->toArray();
									$custIds = array_column($custIds, 'customer_id'); //5498
								}	
							}
					}
					$customersIds = array_merge($customersIds, $custIds);
					$customersIds = array_unique($customersIds);
				}

				if( count($customersIds) ){
					$res = $customerTable->find();
					$senderList['dynamic'] = $res->select(['customerEmail'=>'email', 'customerName'=>$res->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
						   ->where(['id IN'=>$customersIds, 'newsletter'=>1, 'valid_email'=>'1'])->hydrate(false)->toArray();
					$this->Drift->sendMail($senderList);
				}                
			}			
        }
        die;
    }
    
    public function sendMailersTest(){
		//$this->response->type('text/html');
        //$shipvendorTable= TableRegistry::get('SubscriptionApi.Shipvendors');
        //$shipvendorTable->query()->update()->set(['set_default' => 0])->execute();
        $customerTable 	= TableRegistry::get('SubscriptionApi.Customers');
        $orderTable 	= TableRegistry::get('SubscriptionApi.Orders');
        $dataTable 	    = TableRegistry::get('SubscriptionApi.DriftMailers');  // 7 for delivered, 8 for cart, 10 for membership 
        $mailers 	    = $dataTable->find('all', ['conditions'=>['bucket_id !='=>1,'status'=>'active']])->hydrate(false)->toArray();
		//pr($mailers);
		foreach($mailers as $value){
            $status         = 0;
            $now1           = Time::now();
            $now1->timezone = 'Asia/Kolkata';
            $currentMinutes = $now1->minute;
            $currentHours   = $now1->hour;
            switch($value['schedule_id']){
                case 1:// for One time in a day
                    //$value['send_at'] = '10:46 AM';
                    if( $currentHours ==  date('H', strtotime($value['send_at'])) ){
                        $givenMinutes = (int) substr($value['send_at'], 3,2); //find minutes
                        if( ($currentMinutes <=  $givenMinutes) && ($givenMinutes < ($currentMinutes + 5)) ){
                            $status = 1;
                        }
                    }
                    break;
                case 2: // for custom hours
                    $givenHours = (int) $value['send_at'];
                    if( ($givenHours <= $currentHours) ){
                        if( ($currentHours % $givenHours == 0) && ($currentMinutes < 5) ){
                            $status = 1;
                        }
                    }else{
                        if( ($currentHours == 23) && ($currentMinutes > 54) && ($currentMinutes < 60) ){
                            $status = 1;
                        }
                    }
                    break;
                default:
			} $status = 1;
            if($status){
                //echo 'ok'; die;
                $senderList     			= [];
                $senderList['mailer_id'] 	= $value['id'];
                $senderList['mailer_id'] 	= $value['id'];
                $senderList['subject'] 		= $value['subject'];
                $senderList['content'] 		= $value['content'];
				$senderList['sender']  		= !empty($value['sender_name']) ? $value['sender_name']:'Connect';
				$senderList['sender_email'] = $value['sender_email'];
                $senderList['utm_source'] 	= $value['utm_source'];
                $senderList['utm_medium'] 	= $value['utm_medium'];
                $senderList['utm_campaign'] = $value['utm_campaign'];
                $senderList['utm_term'] 	= $value['utm_term'];
                $senderList['utm_content']  = $value['utm_content'];
				$conditions 		   		= empty($value['conditions']) ? '[]':json_decode($value['conditions'], true);
				$schedule_type 		   		= $conditions['schedule_type'] ?? [];
				$start 				   		= $conditions['start'] ?? [];
				$end 				   		= $conditions['end'] ?? [];
				
				$schedule_type1 = $start1 = $end1 = [];
				foreach($schedule_type as $key=>$value1){
					if( $value1 != 0 ){ $schedule_type1[$key]=$value1; }
				}
				$start1 = array_intersect_key($start, $schedule_type1);
				$end1 = array_intersect_key($end, $schedule_type1);
				$conditions = [];
				foreach($schedule_type1 as $key=>$value1){
					$conditions[$key] = [
						'schedule_type'=>$schedule_type[$key],
						'start'=>$start[$key],
						'end'=>$end[$key]
					];
				}
				echo 'Condition: '; pr($conditions);
				$customersIds = [];
				foreach($conditions as $key=>$value1){
                    $now  			= Time::now();
					$now->timezone  = 'Asia/Kolkata';
					if( $value1['schedule_type'] == 2 ){ //calculate according to hours
						$createdTo   	= $now->modify('- '.$value1['start'].' hours')->format('Y-m-d H:m:s');
						$createdFrom 	= $now->modify('- '.$value1['end'].' hours')->format('Y-m-d H:m:s');
					}else{ // calculate according to days
						$value1['end'] 	= $value1['end'] - 1;
						$createdTo   = $now->modify('- '.$value1['start'].' days')->format('Y-m-d');
						$createdFrom = $now->modify('- '.$value1['end'].' days')->format('Y-m-d');
						$createdFrom = $createdFrom.' 00:00:01';
						$createdTo   = $createdTo.' 23:59:59';						
					}
					//echo "$createdFrom <> $createdTo";
					$custIds = [];
					switch($key){
						case 'delivered':
							$delivered = $orderTable->find('all', ['fields'=>['id','location_id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Orders.modified', $createdFrom, $createdTo);
							})
							->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter','valid_email']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','quantity']);
								},
								'Locations'=>function($q){
									return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
								}
							])
                            ->hydrate(false)->toArray(); //pr($delivered);die;
							//$this->Drift->getDeliveredProducts($delivered, $senderList);
							echo 'Delivered: '; pr($delivered);
							break;
						case 'repeated':
							$repeated = $orderTable->find('all',['fields'=>['id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered'], 'having'=>['count(*) >'=>1]])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Orders.modified', $createdFrom, $createdTo);
							})
							->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter', 'valid_email']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','quantity']);
								},
								'Locations'=>function($q){
									return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
								}
							])
                            ->hydrate(false)->toArray();
							//$this->Drift->getDeliveredProducts($repeated, $senderList);
							echo 'Repeated: '; pr($repeated);
							break;
                        case 'cart':                            
							$cart  = $customerTable->find(); echo "$createdFrom <> $createdTo";
							$cart  = $cart->select(['id','email', 'name'=>$cart->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
							->contain(['Carts'=>[
									'fields'=>['customer_id','product_id', 'Carts.created'],
								]
							])
							->innerJoinWith('Carts')
							->where(['is_active'=>'active','newsletter'=>1,'valid_email'=>'1'])
							->where(function ($exp) use($createdFrom, $createdTo) {
								return $exp->between('Carts.created', $createdFrom, $createdTo);
							})
							->group(['Customers.id'])
							->hydrate(false)->toArray();
                            //$this->Drift->getCartProducts($cart, $senderList);
							echo 'Cart: '; pr($cart);
							break;
						case 'member':
							$custIds = $customerTable->find('all',['fields'=>['id'],'group'=>['Customers.id'],'conditions'=>['is_active'=>'active','newsletter'=>1]])
							->innerJoinWith('Memberships')
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Memberships.created', $createdFrom, $createdTo);
							})
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //84960
							break;
						case 'never':
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active']])
							->where(['logdate >'=>$createdFrom, 'logdate <'=>$createdTo])
							/*->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('logdate NOT', $createdFrom, $createdTo);
							})*/
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id');
							break;
						case 'pending':
							$custIds = $orderTable->find('all',['fields'=>['customer_id'],'group'=>['customer_id'], 'conditions'=>['status'=>'pending']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('modified', $createdFrom, $createdTo);
							})
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'customer_id'); //2664
							break;
						case 'logout':
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1]])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('logdate', $createdFrom, $createdTo);
							})							
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //115391
							break;
						default:
							if ( $key > 0 ) {
								$pids = TableRegistry::get('SubscriptionApi.ProductCategories')->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$key],'group'=>['product_id']])->hydrate(0)->toArray();
								$pids = array_column($pids,'product_id');
								if( count($pids) ){
									$custIds = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']])
									->where(function ($exp, $q) use($createdFrom, $createdTo) {
										return $exp->between('modified', $createdFrom, $createdTo);
									})
									->innerJoinWith('OrderDetails', function($q) use($pids){
										return $q->where(['product_id IN'=>$pids]);
									})
									->hydrate(false)->toArray();
									$custIds = array_column($custIds, 'customer_id'); //5498
								}	
							}
					}
					$customersIds = array_merge($customersIds, $custIds);
					$customersIds = array_unique($customersIds);
				}
				//echo 'All Cust: '; pr($customersIds);
				if( count($customersIds) && (2 > 3) ){
					$res = $customerTable->find();
					$senderList['dynamic'] = $res->select(['customerEmail'=>'email', 'customerName'=>$res->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
						   ->where(['id IN'=>$customersIds, 'newsletter'=>1, 'valid_email'=>'1'])->limit(10)->hydrate(false)->toArray();
					$this->Drift->sendMail($senderList);
				}                
			}			
        }
        
    }
    
	public function sendMailersToList(){
		$this->Drift->sendMailToStoredList();
		die;
	}
    
    public function notifyMe(){
        $notifyMeTable = TableRegistry::get('SubscriptionApi.NotifyMe');
        $productsTable = TableRegistry::get('SubscriptionApi.Products');
        $conn = ConnectionManager::get('usd_manager');
        $query = $conn->execute('SELECT `id`, `email`, GROUP_CONCAT(DISTINCT CONCAT(`product_id`)) AS `product_id` FROM `notify_me` WHERE `status` = "1" GROUP by `email`')->fetchAll('assoc');
        if(!empty($query)){
            foreach( $query as $value ){
                if( !empty($value['product_id']) ){
                    $productsList = $productsTable->find('all', ['fields'=>['id','quantity','url'=>'url_key'],'conditions'=>['Products.id IN'=>explode(',',$value['product_id']),'Products.is_stock'=>'in_stock','Products.is_active'=>'active']])
                    ->contain([
                        'ProductPrices'=>[
                            'queryBuilder' => function($q){
                                return $q->select(['product_id','title','price','description'=>'short_description', ])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                            }
                        ],
                        'ProductImages'=>[
                            'queryBuilder'=>function($q){
                                return $q->select(['product_id','url'=>'img_large'])->where(['is_large'=>'1']);
                            }
                        ]
                    ])
                    ->hydrate(false)
                    ->toArray();
                    $productIds =  array_column($productsList, 'id'); //echo json_encode($productsList);
                    if( !empty($productIds) ){
                        $data = ['subject'=>'Hurry…your Wait List item is now available','template'=>'notify_me','customer'=>['email'=>$value['email']], 'products'=>$productsList];
                        $this->getMailer('SubscriptionApi.Customer')->send('abendedCart', [$data]);
                        $notifyMeTable->query()->update()->set(['status'=>'0'])->where(['email'=>$value['email']])->where(['product_id IN'=>$productIds])->execute();
                    }
                }
            }
        }
        die;
    }
    
    public function productStock()
    {
        $time = Time::now('Asia/Kolkata');
		$createdTo = $time->format('Y-m-d H:i:s');
        $time->modify('-1 day');
		$createdFrom = $time->format('Y-m-d H:i:s');
        $filters = [
            //'modified'=>'0000-00-00 00:00:00',
            'status'=>'pending'
        ];
        $dataTable = TableRegistry::get('SubscriptionApi.Orders');
        $productsTable = TableRegistry::get('SubscriptionApi.Products');
        $query = $dataTable->find('all',['contain'=>['OrderDetails'],'fields'=>['id'],'conditions'=>$filters])
                 ->where(function ($exp, $q) use($createdFrom,$createdTo) {
                    return $exp->between('created', $createdFrom, $createdTo);
                  })
                 ->toArray();
        if( !empty($query) ){
            foreach($query as $value){
                foreach($value->order_details as $v){
                    $product = $productsTable->get($v->product_id);
                    $remainQty = $product->quantity + $v->quantity;
                    if( $remainQty > $product->out_stock_qty){
                        $product->is_stock = 'in_stock';
                    }
                    $product->quantity = $remainQty;
                    $productsTable->save($product);
                }
            }
        }
        //pr($query);
        die;
    }

    public function productOutOfStock()
    {   //echo APP.'Files'.DS.'products'.DS; die;
        $time = Time::now('Asia/Kolkata');
		$createdTo = $time->format('Y-m-d H:i:s');
        $time->modify('-1 day');
		$createdFrom = $time->format('Y-m-d H:i:s');
        $filters = [
            'is_stock'=>'out_of_stock'
        ];
        $dataTable = TableRegistry::get('SubscriptionApi.Products');
        $data = $dataTable->find('all',['fields'=>['id','sku_code','quantity','is_stock','is_active','modified'],'conditions'=>$filters])
                ->where(function ($exp, $q) use($createdFrom,$createdTo) {
                    return $exp->between('modified', $createdFrom, $createdTo);
				})
				->contain([
					'ProductPrices'=>[
						'queryBuilder' => function($q){
							return $q->select(['product_id','name','title','price'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
						}
					]
				])
				->toArray();
		$dataList = [];
		foreach($data as $value){
			$dataList[] = [
				'sku_code' => $value['sku_code'],
				'title' => $value['product_prices'][0]['title'],
				'name' => $value['product_prices'][0]['name'],
				'quantity' => $value['quantity'],
				'is_stock' => $value['is_stock'],
				'is_active' => $value['is_active'],
				'modified' => $value['modified']
			]; 
		}
        if(!empty($dataList)){
            $this->response->withDownload('exports.csv');
            $_serialize='dataList';
            $_header = ['SKU Code', 'Title', 'name', 'Quantity', 'Stock', 'Stock', 'Modified'];
            $_extract = ['sku_code', 'title', 'name', 'quantity', 'is_stock', 'is_active', 'modified'];
            
            // Create the builder
            $builder = new ViewBuilder;
            $builder->layout = false;
            $builder->setClassName('CsvView.Csv');
    
            // Then the view
            $view = $builder->build($dataList);
            $view->set(compact('dataList', '_serialize', '_header', '_extract'));
    
            // And Save the file
            $file = new File(ROOT. DS.'plugins'. DS .'SubscriptionApi'. DS .'webroot'.DS.'server'.DS.'OutOfStockProduct.csv', true, 0777);
            $file->write($view->render());
			
            $email = new Email('Sendgrid');
            $email->from([PC['COMPANY']['email']=>PC['COMPANY']['tag']])
                  ->to(PC['COMPANY']['email'],'Stock Manager')
                  ->subject('List of out of stock products')
                  ->attachments(APP.'Files'.DS.'products'.DS.'OutOfStockProduct.csv')
                  ->send('PFA out of stock products from last 24 hours!');
			
		}
        die;
    }

    public function updateTokenShiprocket(){
        $token           = $this->Shiproket->createToken();
        if( !empty($token) ){
            $file = fopen(WWW_ROOT.'Shiprocket'.DS.'shiprocket.txt', "w");
            fwrite($file, $token);
            fclose($file);
        }
        die;
    }

	//run every 5 minutes
    public function updateOrderStatusShiprocket(){
        $this->Shiproket->syncStatus();
        die;
	}
	
	public function pushOrderToCouriers(){
		$now  			= Time::now();
		$now->timezone  = 'Asia/Kolkata';
		$createdTo   	= $now->format('Y-m-d H:m:s');
		$createdFrom 	= $now->modify('- 10 days')->format('Y-m-d H:m:s');
		$orders 		= TableRegistry::get('SubscriptionApi.Orders')->find('all', ['fields'=>['id','tracking_code'],'conditions'=>['status'=>'accepted'],'limit'=>2,'order'=>['id'=>'DESC']])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
		//pr($orders);
		foreach( $orders as $value ){
			if( empty($value['tracking_code']) ){
				//echo $value['id'];
				$this->Shipvendor->pushOrder($value['id']);
			}
		}
		die;
	}

	//tracking delhivewry orders
	public function updateDevlhiveryOrderStatus() {
		$this->loadComponent('SubscriptionApi.Delhivery');
		$time = Time::now('Asia/Kolkata');
		$currentHours = $time->format('H');
		$time->modify("-$currentHours day");
		$createdTo = $time->format('Y-m-d H:i:s');		
		$time->modify('-1 day');
		$createdFrom = $time->format('Y-m-d H:i:s');
        $filters = [
			'status !='=>'pending',
			'tracking_code !='=>'',
			'courier_id'=>3
		];
		$code = $this->request->getQuery('awbs','');
		$count = 0;
		if ( empty($code) ) {
			$dataTable = TableRegistry::get('SubscriptionApi.Orders');
			$query = $dataTable->find('all',['fields'=>['tracking_code'],'conditions'=>$filters])->where(function ($exp, $q) use($createdFrom,$createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					})->toArray();
			$codes = array_column($query,'tracking_code');
			if ( !empty($codes) ) {
				$count = count($codes);
				$codes = array_chunk($codes, 40);
				foreach ( $codes as $code ) {
					$code = implode(',',$code);
					$this->Delhivery->trackOrders($code);
				}
			}
		} else {
			$this->Delhivery->trackOrders($code);
		}

		$res = ( $count > 0 ) ? "Order sync from $createdFrom to $createdTo, Total: $count" : "Order sync from $createdFrom to $createdTo";
		die($res);
	} 

}
