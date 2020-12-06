<?php
namespace App\Controller\Api;

use Cake\ORM\TableRegistry;
use Cake\Controller\Component;
use Cake\Event\Event;
use Cake\Utility\Security;
use Cake\I18n\Time;
use Cake\Filesystem\File;
use Cake\View\View;
use Cake\View\ViewBuilder;
//use Cake\I18n\Date;
//use Cake\Network\Exception\UnauthorizedException;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\Datasource\ConnectionManager;
class CronsController extends AppController
{
    use MailerAwareTrait;
    private $skus = ['PB00000051','PB00000052','PB00000053','PB00000054','PB00000055','PB00000056','PB00000057','PB00000058','PB00000059','PB00000060','PB00000061','PB00000062'];    
	public function initialize()
    {
        parent::initialize();
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		$this->loadComponent('Delhivery');
		$this->loadComponent('Sms');
		$this->loadComponent('Store');
		$this->loadComponent('Membership');
		$this->loadComponent('Drift');
		$this->loadComponent('Customer');
		$this->loadComponent('Facebook');
		$this->loadComponent('Shiproket');
		$this->loadComponent('Shipvendor');
    }
    
    public function index(){
		$now  			= Time::now();
		$now->timezone  = 'Asia/Kolkata';
		$value['start'] 	= 0 - 1;
		//$test = $now->modify('- '.$value['start'].' days')->format('Y-m-d');
		//$res = $this->Facebook->addAllItem();
		$res = $this->Drift->checkEmail('mohd.afroj@gmail.com');
		pr($res);
		die;
    }
    
    //update facebook catalogue
    public function facebookCatalogue(){
        $this->Facebook->addAllItem();
        die;
	}
	
    public function pushorder($vender,$id=0){
		switch( $vender ){
			case 'pb': $this->Delhivery->sendOrderNewTest($id);
				break;
			case 'sr':
				break;
			default:
		}
        die;
	}
	
    public function sendMailers(){
        //$shipvendorTable= TableRegistry::get('Shipvendors');
        //$shipvendorTable->query()->update()->set(['set_default' => 0])->execute();
        $customerTable 	= TableRegistry::get('Customers');
        $orderTable 	= TableRegistry::get('Orders');
        $dataTable 	    = TableRegistry::get('DriftMailers');  // 7 for delivered, 8 for cart, 10 for membership 
		$mailers 	    = $dataTable->find('all', ['conditions'=>['bucket_id !='=>1,'status'=>'active']])->hydrate(false)->toArray(); 
		//pr($mailers);die;
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
            if($status){
                //echo 'ok'; die;
                $senderList     			= [];
				$senderList['bucket_id'] 	= $value['bucket_id'];
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
				foreach($schedule_type as $key=>$value){
					if( $value != 0 ){ $schedule_type1[$key]=$value; }
				}
				$start1 = array_intersect_key($start, $schedule_type1);
				$end1 = array_intersect_key($end, $schedule_type1);
				$conditions = [];
				foreach($schedule_type1 as $key=>$value){
					if ( !empty($start[$key]) && !empty($end[$key]) ) {
						$conditions[$key] = [
							'schedule_type'=>$schedule_type[$key],
							'start'=>$start[$key],
							'end'=>$end[$key]
						];
					}
				}
			
				$customersIds = [];
				foreach($conditions as $key=>$value){
                    $now  			= Time::now();
					$now->timezone  = 'Asia/Kolkata';
					$value['start'] = ( $value['start'] < 0 ) ? 0 :$value['start'];
					if( $value['schedule_type'] == 2 ){ //calculate according to hours
						$value['end'] = ( $value['end'] < 0 ) ? 0 :$value['end'];
						$createdTo   	= $now->modify('- '.$value['start'].' hours')->format('Y-m-d H:m:s');
						$createdFrom 	= $now->modify('- '.$value['end'].' hours')->format('Y-m-d H:m:s');
					}else{ // calculate according to days
						$value['end'] 	= $value['end'] - 1;
						$value['end'] = ( $value['end'] < 0 ) ? 0 :$value['end'];
						$createdTo   = $now->modify('- '.$value['start'].' days')->format('Y-m-d');
						$createdFrom = $now->modify('- '.$value['end'].' days')->format('Y-m-d');
						$createdFrom = $createdFrom.' 00:00:01';
						$createdTo   = $createdTo.' 23:59:59';						
					}
                    
					//echo $key;
					$categoryId = 0;
					$custIds = [];
					switch($key){
						case 'delivered':
							$delivered = $orderTable->find('all', ['fields'=>['id','email'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Orders.modified', $createdFrom, $createdTo);
							})
							->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter', 'valid_email']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','qty']);
								}
							])
                            ->hydrate(false)->toArray();
							$this->Drift->getDeliveredProducts($delivered, $senderList);
							break;
						case 'repeated':
							$repeated = $orderTable->find('all',['fields'=>['id','email'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered'], 'having'=>['count(*) >'=>1]])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Orders.modified', $createdFrom, $createdTo);
							})
							->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter', 'valid_email']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','qty']);
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
							->innerJoinWith('Carts', function($q) use ($createdFrom, $createdTo) {
								return $q->where(['Carts.created >'=>$createdFrom, 'Carts.created <'=>$createdTo]);
							})
							->where(['is_active'=>'active','newsletter'=>1, 'valid_email'=>'1'])
							->group(['Customers.id'])
							->order(['Carts.created'=>'DESC'])
							->hydrate(false)->toArray();
                            $this->Drift->getCartProducts($cart, $senderList);
							break;
						case 'perfume':
							$categoryId = 5;
							break;
						case 'scent_shot':
							$categoryId = 7;
							break;
						case 'refill':
							$categoryId = 4;
							break;
						case 'deo':
							$categoryId = 9; //deo category
							break;
						case 'member':
							$custIds = $customerTable->find('all',['fields'=>['id'],'group'=>['Customers.id'],'conditions'=>['is_active'=>'active','newsletter'=>1,'valid_email'=>'1']])
							->innerJoinWith('Memberships')
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Memberships.created', $createdFrom, $createdTo);
							})
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //84960
							break;
						case 'never':
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','valid_email'=>'1']])
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
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>'1']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('logdate', $createdFrom, $createdTo);
							})							
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //115391
							break;
						default:	
					}
					if ( $categoryId > 0 ) {
						$pids = TableRegistry::get('ProductsCategories')->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$categoryId],'group'=>['product_id']])->hydrate(false)->toArray();
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
		$customerTable 	= TableRegistry::get('Customers');
		//$customersIds = [12345];
		//$senderList['dynamic'] = $customerTable->find('all', ['fields'=>['customerEmail'=>'email', 'customerName'=>'concat("firstname", " ", "lastname")'],'conditions'=>['id IN'=>$customersIds,'newsletter'=>1]])->hydrate(false)->toArray();
		//pr($senderList);die;
        $orderTable 	= TableRegistry::get('Orders');
        $dataTable 	    = TableRegistry::get('DriftMailers'); // 7 for delivered, 8 for cart, 10 for membership 
        $mailers 	    = $dataTable->find('all', ['conditions'=>['id'=>10,'status'=>'active']])->hydrate(false)->toArray();
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
            if($status){
                //echo 'ok'; die;
                $senderList     			= [];
				$senderList['bucket_id'] 	= $value['bucket_id'];
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
				foreach($schedule_type as $key=>$value){
					if( $value != 0 ){ $schedule_type1[$key]=$value; }
				}
				$start1 = array_intersect_key($start, $schedule_type1);
				$end1 = array_intersect_key($end, $schedule_type1);
				$conditions = [];
				foreach($schedule_type1 as $key=>$value){
					$conditions[$key] = [
						'schedule_type'=>$schedule_type[$key],
						'start'=>$start[$key],
						'end'=>$end[$key]
					];
				}
			
				$prodSend = $perfume = $scent_shot = $refill = $deo = $member = $never = $pending = $logout = [];
				foreach($conditions as $key=>$value){
                    $now  			= Time::now();
					$now->timezone  = 'Asia/Kolkata';
					if( $value['schedule_type'] == 2 ){ //calculate according to hours
						$createdTo   	= $now->modify('- '.$value['start'].' hours')->format('Y-m-d H:m:s');
						$createdFrom 	= $now->modify('- '.$value['end'].' hours')->format('Y-m-d H:m:s');
					}else{ // calculate according to days
						$value['end'] 	= $value['end'] - 1;
						$createdTo   = $now->modify('- '.$value['start'].' days')->format('Y-m-d');
						$createdFrom = $now->modify('- '.$value['end'].' days')->format('Y-m-d');
						$createdFrom = $createdFrom.' 00:00:01';
						$createdTo   = $createdTo.' 23:59:59';						
					}
                    
					//echo $key;
					switch($key){
						case 'delivered':
							$prodSend = $orderTable->find('all', ['fields'=>['id','email'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Orders.modified', $createdFrom, $createdTo);
							})
							->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','qty']);
								}
							])
                            ->hydrate(false)->toArray();
							$this->Drift->getDeliveredProducts($prodSend, $senderList);
							break;
						case 'repeated':
							$prodSend = $orderTable->find('all',['fields'=>['id','email'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered'], 'having'=>['count(*) >'=>1]])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Orders.modified', $createdFrom, $createdTo);
							})
							->contain([
								'Customers'=>function($q){
									return $q->select(['firstname','lastname','email','mobile','newsletter']);
								},
								'OrderDetails'=>function($p){
									return $p->select(['order_id','product_id','price','title','size','qty']);
								}
							])
                            ->hydrate(false)->toArray();
							$this->Drift->getDeliveredProducts($prodSend, $senderList);
							break;
                        case 'cart':                            
							$prodSend  = $customerTable->find();
							$prodSend  = $prodSend->select(['id','email', 'name'=>$prodSend->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
							->contain(['Carts'=>[
									'fields'=>['customer_id','product_id', 'Carts.created'],
								]
							])
							->innerJoinWith('Carts', function($q) use ($createdFrom, $createdTo) {
								return $q->where(['Carts.created >'=>$createdFrom, 'Carts.created <'=>$createdTo]);
							})
							->where(['is_active'=>'active','newsletter'=>1])
							->group(['Customers.id'])
							->order(['Carts.created'=>'DESC'])
							->hydrate(false)->toArray();
                            $this->Drift->getCartProducts($prodSend, $senderList);
							break;
						case 'perfume':
							$categoryId = 5;
							$pids = TableRegistry::get('ProductsCategories')->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$categoryId],'group'=>['product_id']])->hydrate(false)->toArray();
							$pids = array_column($pids,'product_id');
							if( count($pids) ){
								$perfume = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']])
								->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('modified', $createdFrom, $createdTo);
								})
								->innerJoinWith('OrderDetails', function($q) use($pids){
									return $q->where(['product_id IN'=>$pids]);
								})
								->hydrate(false)->toArray();
								$perfume = array_column($perfume, 'customer_id'); //5498
							}
							break;
						case 'scent_shot':
							$categoryId = 7;
							$pids = TableRegistry::get('ProductsCategories')->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$categoryId],'group'=>['product_id']])->hydrate(false)->toArray();
							$pids = array_column($pids,'product_id');
							if( count($pids) ){
								$scent_shot = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']])
								->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('modified', $createdFrom, $createdTo);
								})
								->innerJoinWith('OrderDetails', function($q) use($pids){
									return $q->where(['product_id IN'=>$pids]);
								})
								->hydrate(false)->toArray();
								$scent_shot = array_column($scent_shot, 'customer_id'); //2452
							}
							break;
						case 'refill':
							$categoryId = 4;
							$pids = TableRegistry::get('ProductsCategories')->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$categoryId],'group'=>['product_id']])->hydrate(false)->toArray();
							$pids = array_column($pids,'product_id');
							if( count($pids) ){
								$refill = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']])
								->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('modified', $createdFrom, $createdTo);
								})
								->innerJoinWith('OrderDetails', function($q) use($pids){
									return $q->where(['product_id IN'=>$pids]);
								})
								->hydrate(false)->toArray();
								$refill = array_column($refill, 'customer_id'); //2452
							}
							break;
						case 'deo':
							$categoryId = 9; //deo category
							$pids = TableRegistry::get('ProductsCategories')->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$categoryId],'group'=>['product_id']])->hydrate(false)->toArray();
							$pids = array_column($pids,'product_id');
							if( count($pids) ){
								$deo = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']])
								->where(function ($exp, $q) use($createdFrom, $createdTo) {
									return $exp->between('modified', $createdFrom, $createdTo);
								})
								->innerJoinWith('OrderDetails', function($q) use($pids){
									return $q->where(['product_id IN'=>$pids]);
								})
								->hydrate(false)->toArray();
								$deo = array_column($deo, 'customer_id'); //5498
							}
							break;
						case 'member':
							$member = $customerTable->find('all',['fields'=>['id'],'group'=>['Customers.id'],'conditions'=>['is_active'=>'active','newsletter'=>1]])
							->innerJoinWith('Memberships')
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('Memberships.created', $createdFrom, $createdTo);
							})
							->hydrate(false)->toArray();
							$member = array_column($member, 'id'); //84960
							break;
						case 'never':
							$never = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('logdate NOT', $createdFrom, $createdTo);
							})
							->hydrate(false)->toArray();
							$never = array_column($never, 'id');
							break;
						case 'pending':
							$pending = $orderTable->find('all',['fields'=>['customer_id'],'group'=>['customer_id'], 'conditions'=>['status'=>'pending']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('modified', $createdFrom, $createdTo);
							})
							->hydrate(false)->toArray();
							$pending = array_column($pending, 'customer_id'); //2664
							break;
						case 'logout':
							$logout = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1]])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('logdate', $createdFrom, $createdTo);
							})							
							->hydrate(false)->toArray();
							$logout = array_column($logout, 'id'); //115391
							break;
						default:	
					}
				}

				$customersIds = array_merge($perfume, $deo, $scent_shot, $refill, $member, $never, $pending, $logout);
				$customersIds = array_unique($customersIds);
				if( count($customersIds) ){
					$res = $customerTable->find();
					$senderList['dynamic'] = $res->select(['customerEmail'=>'email', 'customerName'=>$res->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
						   ->where(['id IN'=>$customersIds, 'newsletter'=>1])->limit(10)->hydrate(false)->toArray();
					$this->Drift->sendMail($senderList);
				}                
            }
        }
        die;
	}
	
	public function sendMailersToList(){
		$this->Drift->sendMailToStoredList();
		die;
	}
    
    public function notifyMe(){
        $notifyMeTable = TableRegistry::get('NotifyMe');
        $productsTable = TableRegistry::get('Products');
        $conn = ConnectionManager::get('default');
        $query = $conn->execute('SELECT `id`, `email`, GROUP_CONCAT(DISTINCT CONCAT(`product_id`)) AS `product_id` FROM `notify_me` WHERE `status` = "1" GROUP by `email`')->fetchAll('assoc');
        if(!empty($query)){
            foreach( $query as $value ){
                if( !empty($value['product_id']) ){
                    //echo json_encode(explode(',',$value['product_id']));
                    $productsList = $productsTable->find('all', ['fields'=>['id','title','qty','description'=>'short_description', 'url'=>'url_key'],'conditions'=>['Products.id IN'=>explode(',',$value['product_id']),'Products.is_stock'=>'in_stock','Products.is_active'=>'active']])
                    ->contain([
                        'ProductsImages'=>[
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
                        $this->getMailer('Customer')->send('abendedCart', [$data]);
                        $notifyMeTable->query()->update()->set(['status'=>'0'])->where(['email'=>$value['email']])->where(['product_id IN'=>$productIds])->execute();
                    }
                }
            }
        }
        die;
    }
    
    public function productStock()
    {
		////############ Restore Product from cart after one day ###########///
		$now            = Time::now();
		$now->timezone  = 'Asia/Kolkata';
		$createdTo   	= $now->modify('- 1 hours')->format('Y-m-d H:m:s');
		$createdFrom 	= $now->modify('- 24 hours')->format('Y-m-d H:m:s');
		$cartTable 	= TableRegistry::get('Carts');		
		$res = $cartTable->find('all',['fields'=>['productId'=>'product_id', 'qty']])
				->where(function ($exp, $q) use($createdFrom, $createdTo) {
					return $exp->between('created', $createdFrom, $createdTo);
				})
				->limit(10)
			   ->hydrate(false)->toArray();
		if( !empty($res) ){
			$this->Store->updateStockAfterOrderCancel($res);
		}
		die;
		////############ Restore Product when order created as pending status after one day ###########///
        $time = Time::now('Asia/Kolkata');
		$createdTo = $time->format('Y-m-d H:i:s');
        $time->modify('-1 day');
		$createdFrom = $time->format('Y-m-d H:i:s');
        $filters = [
            //'modified'=>'0000-00-00 00:00:00',
            'status'=>'pending'
        ];
        $dataTable = TableRegistry::get('Orders');
        $productsTable = TableRegistry::get('Products');
        $query = $dataTable->find('all',['contain'=>['OrderDetails'],'fields'=>['id'],'conditions'=>$filters])
                 ->where(function ($exp, $q) use($createdFrom,$createdTo) {
                    return $exp->between('created', $createdFrom, $createdTo);
                  })
                 ->toArray();
        if( !empty($query) ){
            foreach($query as $value){
                foreach($value->order_details as $v){
                    //echo "$v->id | $v->product_id | $v->sku_code | $v->qty | ";
                    $product = $productsTable->get($v->product_id);
                    $remainQty = $product->qty + $v->qty;
                    if( $remainQty > $product->out_stock_qty){
                        $product->is_stock = 'in_stock';
                    }
                    $product->qty = $remainQty;
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
        $dataTable = TableRegistry::get('Products');
        $data = $dataTable->find('all',['fields'=>['id','name','title','sku_code','qty','is_stock','is_active','modified'],'conditions'=>$filters])
                 ->where(function ($exp, $q) use($createdFrom,$createdTo) {
                    return $exp->between('modified', $createdFrom, $createdTo);
                  })
                 ->toArray();                 
        //pr($query);
        if(!empty($data)){
            $this->response->withDownload('exports.csv');
            $_serialize='data';
            $_header = ['ID', 'SKU Code', 'Title', 'name', 'Quantity', 'Stock', 'Stock', 'Modified'];
            $_extract = ['id', 'sku_code', 'title', 'name', 'qty', 'is_stock', 'is_active', 'modified'];
            
            // Create the builder
            $builder = new ViewBuilder;
            $builder->layout = false;
            $builder->setClassName('CsvView.Csv');
    
            // Then the view
            $view = $builder->build($data);
            $view->set(compact('data', '_serialize', '_header', '_extract'));
    
            // And Save the file
            $file = new File(APP.'Files'.DS.'products'.DS.'OutOfStockProduct.csv', true, 0777);
            $file->write($view->render());

            $email = new Email('Sendgrid');
            $email->from(['connect@perfumebooth.com'=>'Web Manager'])
                  ->to('customerservice@perfumebooth.com','Stock Manager')
                  ->cc(['mohd.afroj@perfumebooth.com','rohit@perfumebooth.com'])
                  ->subject('List of out of stock products')
                  ->attachments(APP.'Files'.DS.'products'.DS.'OutOfStockProduct.csv')
                  ->send('PFA out of stock products from last 24 hours!');
        }
        die;
    }

    public function updatePackingSlip($waybill)
	{
		$id_order			= '';		
        $packingSlip				= $this->Delhivery->getPackingSlip($waybill);
        //pr($packingSlip);
        //die;
        $orderTable 				= TableRegistry::get('Orders');
		$orderData		= $orderTable->find('all', ['conditions' => ['tracking_code' => $waybill]])->toArray();
		if(!empty($orderData) && isset($orderData[0]))
		{
			$id_order						= $orderData[0]->id;
            $order						    = $orderTable->get($id_order);		
            $order->packing_slip		    = serialize($packingSlip);
            $orderTable->save($order);
        }
        
		$invoiceTable		= TableRegistry::get('Invoices');
		$invoiceData		= $invoiceTable->find('all', ['conditions' => ['order_number'=>$id_order]])->toArray();
		if(!empty($invoiceData) && isset($invoiceData[0]))
		{
			$id_invoice						= $invoiceData[0]->id;
			$invoice						= $invoiceTable->get($id_invoice);
			$invoice->tracking_code			= $waybill;
			$invoice->packing_slip			= serialize($packingSlip);
			$invoiceTable->save($invoice);
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
		$orders 		= TableRegistry::get('Orders')->find('all', ['fields'=>['id','tracking_code'],'conditions'=>['status'=>'accepted'],'limit'=>2,'order'=>['id'=>'DESC']])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
		//pr($orders);
		foreach( $orders as $value ){
			if( empty($value['tracking_code']) ){
				//echo $value['id'];
				$this->Shipvendor->pushOrderByAdmin($value['id']);
			}
		}
		die;
	}

}
