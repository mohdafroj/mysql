<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Validation\Validator;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;

class MarketsController extends AppController
{
	use MailerAwareTrait;
	public function initialize(){
		parent::initialize();
		$this->loadComponent('Store');
		$this->loadComponent('Drift');
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

	}

	public function index($id=0)
	{
		$mailers = $mailerList = [];
		$this->set('title', 'Drift Marketing');
		try{
			$dataTable 	= TableRegistry::get('Buckets');
			$mailer 	= ($id > 0) ? $dataTable->get($id) : $dataTable->newEntity();
			if($this->request->is(['post','put'])){				
				$mailer->title 	= $this->request->getData('title');
				if( !empty($mailer->title) ){
					if($dataTable->save($mailer)){
						$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
						$this->redirect(['action'=>'index']);
					}else{
						$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
					}
				}else{
					$this->Flash->error(__('Please enter Campaign Name!'), ['key' => 'adminError']);
				}
			}else if($this->request->is(['delete'])){
				$bucketMailers 	= TableRegistry::get('DriftMailers')->find('all', ['feilds'=>['id'],'conditions'=>['bucket_id'=>$id],'limit'=>1])->hydrate(false)->toArray();
				if( empty($bucketMailers) ){
					if( !in_array($id, [2,3]) ){
						if($dataTable->delete($mailer)){
							$this->Flash->success(__('The record has been removed!'), ['key' => 'adminSuccess']);
						}else{
							$this->Flash->error(__('Sorry, try again!'), ['key' => 'adminError']);
						}
					}
				}else{
					$this->Flash->error(__('Sorry, This Campaign contains mailers, Please delete mailers!'), ['key' => 'adminError']);
				}
				$this->redirect(['action'=>'index']);
			}
			$mailerList 	= $dataTable->find('all', ['fields'=>['id','title'], 'order'=>['id'=>'DESC']])
							->contain(['DriftMailers' =>[
								'fields'=>['DriftMailers.bucket_id']
								]
							])
							->hydrate(false)->toArray();
			//pr($mailerList);
		}catch(\Exception $e){}
		$this->set(compact('mailer','mailerList'));
		$this->set('_serialize', ['mailer','mailerList']);
	}

	public function filterCustomers($bucketId=0, $key=null, $md5=null){
		$this->set('queryString', $this->request->getQueryParams());
		$this->set('selected', $this->request->getQuery('selected',''));
		$this->set('schedule_type', $this->request->getQuery('schedule_type',0));
		$this->set('start', $this->request->getQuery('start',0));
		$this->set('end', $this->request->getQuery('end',0));
		$bucket 	= TableRegistry::get('Buckets')->get($bucketId);		
		$this->set('title','Drift Marketing: '.$bucket->title);		
		$this->set(compact('bucketId','bucket'));
		$this->set('_serialize', ['bucketId','bucket']);	
	}

	public function exports($id=null){
		$response = [];
		$key   = $this->request->getQuery('selected');
		$type  = $this->request->getQuery('schedule_type');
		$start = $this->request->getQuery('start');
		$end   = $this->request->getQuery('end');
		if( ($type > 0) && !empty($key) ){
			$customerTable 			= TableRegistry::get('Customers');
			$orderTable 			= TableRegistry::get('Orders');
			$productCategoryTable   = TableRegistry::get('ProductsCategories');
			$now  			= Time::now();
			$now->timezone  = 'Asia/Kolkata';
			if( $type == 2 ){ //calculate according to hours
				$createdTo   	= $now->modify('- '.$start.' hours')->format('Y-m-d H:m:s');
				$createdFrom 	= $now->modify('- '.$end.' hours')->format('Y-m-d H:m:s');
			}else{ // calculate according to days
				$end 	= ($end > 0) ? $end - 1 : $end;
				$createdTo   = $now->modify('- '.$start.' days')->format('Y-m-d');
				$createdFrom = $now->modify('- '.$end.' days')->format('Y-m-d');
				$createdFrom = $createdFrom.' 00:00:01';
				$createdTo   = $createdTo.' 23:59:59';						
			}
			$categoryId = 0;
			switch($key){
				case 'delivered':
					$response = $orderTable->find('all', ['fields'=>['customer_id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered']])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('Orders.modified', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
					$response = array_column($response, 'customer_id'); //84960
					break;
				case 'repeated':
					$response = $orderTable->find('all',['fields'=>['customer_id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered'], 'having'=>['count(*) >'=>1]])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('Orders.modified', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
					$response = array_column($response, 'customer_id'); //84960
					break;
				case 'cart':
					$response  = $customerTable->find()->select(['id'])
					->innerJoinWith('Carts', function($q) use ($createdFrom, $createdTo) {
						return $q->where(['Carts.created >'=>$createdFrom, 'Carts.created <'=>$createdTo]);
					})
					->where(['is_active'=>'active','newsletter'=>1])
					->group(['Customers.id'])
					->order(['Carts.created'=>'DESC'])
					->hydrate(false)->toArray(); 					
					$response = array_column($response, 'id'); //84960
					break;
				case 'member':
					$response = $customerTable->find('all',['fields'=>['id'],'group'=>['Customers.id'],'conditions'=>['is_active'=>'active','newsletter'=>1]])
					->innerJoinWith('Memberships')
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('Memberships.created', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
					$response = array_column($response, 'id'); //84960
					break;
				case 'never':
					$response = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active']])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('logdate NOT', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
					$response = array_column($response, 'id');
					break;
				case 'pending':
					$response = $orderTable->find('all',['fields'=>['customer_id'],'group'=>['customer_id'], 'conditions'=>['status'=>'pending']])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('modified', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
					$response = array_column($response, 'customer_id'); //2664
					break;
				case 'logout':
					$response = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1]])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('logdate', $createdFrom, $createdTo);
					})
					->hydrate(false)->toArray();
					$response = array_column($response, 'id'); //115391
					break;
				case 'all':
					$response = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1]])
					->group(['email'])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					})							
					->hydrate(false)->toArray();
					$response = array_column($response, 'id'); //115391
					break;
				case 'perfume': $categoryId = 5; break;
				case 'scent_shot': $categoryId = 7; break;
				case 'refill': $categoryId = 4; break;
				case 'deo': $categoryId = 9; break;
				case 'bodymist': $categoryId = 8; break;
				case 'perfumeselfie': $categoryId = 6; break;
				default:
			}
			if( $categoryId > 0 ){
				$pids = $productCategoryTable->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$categoryId],'group'=>['product_id']])->hydrate(false)->toArray();
				$pids = array_column($pids,'product_id');
				if( count($pids) ){
					$response = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('modified', $createdFrom, $createdTo);
					})
					->innerJoinWith('OrderDetails', function($q) use($pids){
						return $q->where(['product_id IN'=>$pids]);
					})
					->hydrate(false)->toArray();
					$response = array_column($response, 'customer_id'); //5498
				}
			}
			
			if( empty($response) ){
				$this->Flash->error(__('Sorry, record not found!'), ['key' => 'adminError']);
			}else{
				$res = $customerTable->find();
				$response = $res->select(['id','email', 'mobile', 'name'=>$res->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
					   ->where(['id IN'=>$response, 'newsletter'=>1, 'valid_email'=>'1'])->group(['id'])->hydrate(0)->toArray();
			}
		}else{
			$this->Flash->error(__('Sorry, please select one filter!'), ['key' => 'adminError']);
		}
		$this->response->withDownload('exports.csv');
		$_serialize = 'response';
		$_header = ['ID', 'Name', 'Email', 'Mobile'];
		$_extract = ['id', 'name', 'email', 'mobile'];
		$this->set(compact('response', '_serialize', '_header', '_extract'));
		$this->viewBuilder()->setClassName('CsvView.Csv');
		return;
	}

	public function mailerList($id=0)
	{
		try{
			$driftMailerLists = TableRegistry::get('DriftMailerLists');
			switch(strtolower($this->request->getMethod())){
				case 'post':
					$res = '';
					$id  = $this->request->getData('id', 8);
					$mailer = $driftMailerLists->get($id)->toArray();
					$content = isset($mailer['content']) ? json_decode($mailer['content'], true) : [];
					if(count($content)){
						$totalCustomer = isset($content['dynamic']) ? count($content['dynamic']) : 0;
						$res .= '<div class="row"><div class="col-sm-12">'.$content['content'].'</div></div><div class="row"><div class="col-sm-12"><strong>Customers List ('.$totalCustomer.')</strong></div></div>';
						foreach ($content['dynamic'] as $customer) {
							$res .=	'<div class="row">
									<div class="col-sm-5 text-left">'.$customer['customerName'].'</div>
									<div class="col-sm-1">:</div>
									<div class="col-sm-6 text-left">'.$customer['customerEmail'].'</div>
								</div>';
						}
					}else{
						$res = '<div class="row"><div class="col-sm-12">Sorry, no content found!</div></div>';
					}
					echo $res;
					die;
					break;
				case 'delete':
					$entity = $driftMailerLists->get($id);
					if($driftMailerLists->delete($entity)){
						$this->Flash->success(__('The Mailer List (#'.$id.') has been deleted!'), ['key' => 'adminSuccess']);
					}else{
						$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
					}
					$this->redirect(['action'=>'mailerList']);
					break;
				default:
					$mailerList = []; //$this->Drift->sendMailToStoredList(); die; 
					$this->set('title', 'Drift Mailer List');
					$mailerList = $driftMailerLists->find('all', ['fields'=>['id','drift_mailer_id','size_of_list','created'], 'order'=>['DriftMailerLists.id'=>'DESC']])
									->contain(['DriftMailers' =>[
											'fields'=>['DriftMailers.id','subject','title']
										]
									])
									->hydrate(0)
									->toArray();
					//pr($mailerList); die;
		}
		}catch(\Exception $e){}
		$this->set(compact('mailerList'));
		$this->set('_serialize', ['mailerList']);
	}

	public function view($bucketId=0, $key, $md5)
	{	
		try{
			$limit = $this->request->getQuery('limit', 50);
			$offset = $this->request->getQuery('page', 1);
			$offset = ($offset - 1)*$limit;
			
			$this->paginate = [
					'limit' =>$limit, // limits the rows per page
					'maxLimit' => 2000,
			];
			
			$filterData = [];
			$filterData['bucket_id'] = $bucketId;
			$mailerTitle = $this->request->getQuery('title', '');
			$this->set('mailerTitle', $mailerTitle);
			if(!empty($mailerTitle)) { $filterData['title'] = $mailerTitle; }
	
			$subject = $this->request->getQuery('subject', '');
			$this->set('subject', $subject);
			if(!empty($subject)) { $filterData['subject'] = $subject; }
					
			
			$schedule_id = $this->request->getQuery('schedule_id', '3');
			$this->set('schedule_id', $schedule_id);
			if( !empty($schedule_id) && ($schedule_id != 3) ) { $filterData['schedule_id'] = $schedule_id; }
			
			$content = $this->request->getQuery('content', '');
			$this->set('content', $content);
			if(!empty($content)) { $filterData['content'] = $content; }
			
			$created = $this->request->getQuery('created', '');
			$this->set('created', $created);
			if(!empty($created))
			{
				$date = new Date($created);
				$logDate = $date->format('Y-m-d');
				$filterData['created LIKE'] = "$created%";
			}
			
			$status = $this->request->getQuery('status', '');
			$this->set('status', $status);		
			if( $status !== '' ) { $filterData['status'] = $status; }
	
			$dataTable 	= TableRegistry::get('DriftMailers');
			$query 		= $dataTable->find('all', ['conditions'=>$filterData])->order(['id'=>'DESC']);
			$mailers	= $this->paginate($query)->toArray();
			//pr($queryString);die;
			$bucket 	= TableRegistry::get('Buckets')->get($bucketId);
			$this->set('title','Drift Marketing: '.$bucket->title);
			$this->set('queryString', $this->request->getQueryParams());
			$this->set(compact('bucketId','bucket','mailers'));
			$this->set('_serialize', ['bucketId','bucket','mailers']);	
		}catch(\Exception $e){
			$this->redirect(['action'=>'index']);
		}
	}
	
	public function getStats($catId=1){
		$text      = '';
		if( $this->request->is(['post']) ){
			$catId 		 = $this->request->getData('catId');
			$startDate = $this->request->getData('startDate');
			$endDate   = $this->request->getData('endDate');
			$param     = ['start_date'=>$startDate,'end_date'=>$endDate,'categories'=>'PB-'.$catId];
			$tracks    = $this->Drift->stats($param); 
			$i 		     = 1;
			foreach( $tracks as $value ){
				$flag = $processed = $delivered = $opens = $clicks = $bounces = 0;
				if( isset($value['stats']) && is_array($value['stats']) ){
					foreach($value['stats'] as $stat){
						if( $stat['metrics']['processed'] || $stat['metrics']['delivered'] || $stat['metrics']['opens'] || $stat['metrics']['clicks'] || $stat['metrics']['bounces'] )	{
							$processed += $stat['metrics']['processed'];
							$delivered += $stat['metrics']['delivered'];
							$opens 	   += $stat['metrics']['opens'];
							$clicks    += $stat['metrics']['clicks'];
							$bounces   += $stat['metrics']['bounces'];
							$flag       = 1;
						}
					}
			  }	
				if( $flag ){
					$text = '<tr>
					<td>'.$i++.'.</td>
					<td>'.$value["date"].'</td>
					<td><span class="badge bg-yellow">'.$delivered.'</span></td>
					<td><span class="badge bg-blue">'.$processed.'</span></td>
					<td><span class="badge bg-pink">'.$opens.'</span></td>
					<td><span class="badge bg-green">'.$clicks.'</span></td>
					<td><span class="badge bg-red">'.$bounces.'</span></td>
					</tr>'.$text;
				}
			}
		}
		if( empty($text) ){
			$text = '<tr><td colspan="7">Sorry, record not found for <strong>"'.$startDate.' to '.$endDate.'"</strong>!</td></tr>';
		}
		echo $text;
		die;
	}

	public function mailer($bucketId=0, $key=null, $md5=null, $id=0, $ref=NULL, $refmd5=NULL)
    {
		try{
			$dataTable 	= TableRegistry::get('DriftMailers');
			$mailer = ($id > 0) ? $dataTable->get($id) : $dataTable->newEntity();
			$errors = [];
			if( $this->request->is(['post','put']) ){
				$validator = new Validator();
				$validator
					->notEmpty('title','Please enter mailer title!')
					->add('title', [
						'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The title should be 3 to 500 character long!']
					])
					->notEmpty('sender_email')
          ->add('sender_email', [
							'email' => ['rule' => ['email'], 'message' => 'Please enter valid email id!'],
          ])
					->notEmpty('subject','Please enter mailer subject!')
					->add('subject', [
						'length' => ['rule' => ['lengthBetween', 10, 500], 'message' => 'The subject should be 10 to 500 character long!']
					])
					->notEmpty('content','Please set HTML Template!')
					->add('content', [
						'length' => ['rule' => ['lengthBetween', 200, 50000], 'message' => 'The Template should be 200 to 50000 character long!']
					]);
				$errors = $validator->errors($this->request->getData());
				if( empty($errors) ){ 
					$mailer = $dataTable->patchEntity($mailer, $this->request->getData());
					$myConditions['schedule_type'] = array_combine($this->request->getData('keyword'), $this->request->getData('schedule_type'));
					$myConditions['start'] = array_combine($this->request->getData('keyword'), $this->request->getData('start'));
					$myConditions['end'] = array_combine($this->request->getData('keyword'), $this->request->getData('end'));
					//if( $mailer->schedule_id != 1 ){ $mailer->send_at = 1; }
					$mailer->conditions = json_encode($myConditions);
					if ( $dataTable->save($mailer) ) {
						$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
						$this->redirect(['action'=>'view', $bucketId, 'key', md5($bucketId)]);
					}else{
						$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
					}
				}else{
					$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
				}
			} //echo json_encode(['delivered', 'accepted']);
			//pr($mailer);die;
			$bucket 	= TableRegistry::get('Buckets')->get($bucketId);
			$this->set('title','Drift Marketing: '.$bucket->title);
			$this->set(compact('mailer','bucketId','bucket','id','errors'));
			$this->set('_serialize', ['mailer','bucketId','bucket','id','errors']);	
		}catch(\Exception $e){
			$this->redirect(['action'=>'index']);
		}
	}
	
	public function send($bucketId=0, $key=null, $md5=null)
    {
		try{
			$id = $this->request->getData('id', 0); //55
			if ( $id < 1 ){
				$this->Flash->success(__('Sorry, please try again!'), ['key' => 'adminError']);
			}else{
				$dataTable 			= TableRegistry::get('DriftMailers');
				$customerTable 	= TableRegistry::get('Customers');
				$orderTable 		= TableRegistry::get('Orders');
				$productCategoryTable  = TableRegistry::get('ProductsCategories');
				$email 					= '';
				$sendList				= [];
				$mailer 				= $dataTable->get($id);
				$sendList['bucket_id'] 	= $bucketId;
				$sendList['mailer_id'] 	= $id;
				$sendList['sender'] 	= !empty($mailer->sender_name) ? $mailer->sender_name :'Connect';
				$sendList['sender_email'] = $mailer->sender_email;
				$sendList['content'] 	= $mailer->content ?? '';
				$sendList['subject'] 	= $mailer->subject ?? '';
				$sendList['utm_source'] = $mailer->utm_source ?? '';
				$sendList['utm_campaign'] = $mailer->utm_campaign ?? '';
				$sendList['utm_term']   = $mailer->utm_term ?? '';
				$sendList['utm_content']= $mailer->utm_content ?? '';
				$sendList['utm_medium'] = $mailer->utm_medium ?? '';
				$conditions 			= empty($mailer->conditions) ? '[]':json_decode($mailer->conditions, true);
				$schedule_type 			= $conditions['schedule_type'] ?? [];
				$start 					= $conditions['start'] ?? [];
				$end 					= $conditions['end'] ?? [];
				
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
				//pr($conditions); die;
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
					//echo $createdFrom.' to '.$createdTo.'<br />';
					$categoryId = 0;
					$custIds = [];
					switch($key){
						case 'delivered':
							$delivered = $orderTable->find('all', ['fields'=>['id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered']])
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
							$this->Drift->getDeliveredProducts($delivered, $sendList);
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
									return $p->select(['order_id','product_id','price','title','size','qty']);
								}
							])
							->hydrate(false)->toArray();
							$this->Drift->getDeliveredProducts($repeated, $sendList);
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
							->hydrate(false)->toArray(); //pr($cart);die;		
							$this->Drift->getCartProducts($cart, $sendList);
							break;
						case 'perfume':
							$categoryId = 5;
							break;
						case 'scent_shot':
							$categoryId = 7; //echo $createdFrom.' | '.$createdTo;
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
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1,'valid_email'=>'1']])
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
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1,'valid_email'=>'1']])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('logdate', $createdFrom, $createdTo);
							})
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //115391
							break;
						case 'all':
							$custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1,'valid_email'=>'1']])
							->group(['email'])
							->where(function ($exp, $q) use($createdFrom, $createdTo) {
								return $exp->between('created', $createdFrom, $createdTo);
							})							
							->hydrate(false)->toArray();
							$custIds = array_column($custIds, 'id'); //115391
							break;
						default:	
					}
					if ( $categoryId > 0 ) {
						$pids = $productCategoryTable->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$categoryId],'group'=>['product_id']])->hydrate(false)->toArray();
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

				//pr($customersIds);die;
				if( count($customersIds) ){
					$res = $customerTable->find();
					$sendList['dynamic'] = $res->select(['customerEmail'=>'email', 'customerName'=>$res->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
						   ->where(['id IN'=>$customersIds, 'newsletter'=>1, 'valid_email'=>'1'])->group(['id'])->hydrate(0)->toArray();
					$this->Drift->sendMail($sendList);
					$this->Flash->success(__('Mailer successfully sent!'), ['key' => 'adminSuccess']);
				}
			}
		}catch(\Exception $e){
			$this->Flash->success(__('Sorry, there are some issue, try later!'), ['key' => 'adminError']);
		}
		$this->redirect($this->referer());
	}
	
	public function test($bucketId=0, $key=null, $md5=null, $id, $ref=null, $refmd5=null)
    {
		try{
			$email = $this->request->getData('email');
			if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
				$this->Flash->success(__('Sorry, Please enter a  valid email id!'), ['key' => 'adminError']);
			}else{
				$dataTable 	   = TableRegistry::get('DriftMailers');
				$mailer 	   = $dataTable->get($id);
				$mailer->email = $email;
				$dataTable->save($mailer);
				$sendList['bucket_id'] 	= $bucketId;
				$sendList['mailer_id'] 	= $id;
				$sendList['sender'] 	= $mailer->sender_name ?? 'Connect';
				$sendList['sender_email'] = $mailer->sender_email;
				$sendList['content'] 	= $mailer->content ?? '';
				$sendList['subject'] 	= $mailer->subject ?? '';
				$sendList['utm_source'] = $mailer->utm_source ?? '';
				$sendList['utm_campaign'] = $mailer->utm_campaign ?? '';
				$sendList['utm_term']   = $mailer->utm_term ?? '';
				$sendList['utm_content']= $mailer->utm_content ?? '';
				$sendList['utm_medium'] = $mailer->utm_medium ?? '';
				$sendList['dynamic'][]  = [
					'customerEmail'=>$email,
					'customerName'=>explode('@', $email)[0] ?? ''
				]; 
				$this->Drift->sendMail($sendList);
			$this->Flash->success(__('Mail successfully sent to '.$email), ['key' => 'adminSuccess']);
			}
		}catch(\Exception $e){
			$this->Flash->success(__('Sorry, there are some issue, try later!'), ['key' => 'adminError']);
		}
		$this->redirect($this->referer());
	}
	
	public function delete($id = 0){
			try{
				if( $this->request->is(['delete']) ){
					$dataTable 	= TableRegistry::get('DriftMailers');
					$mailer = $dataTable->get($id);
					if ($dataTable->delete($mailer)) {			
						$this->Flash->success(__('The record has been deleted!'), ['key' => 'adminSuccess']);
					} else {
						$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
					}
				}else{
					$this->Flash->error(__('Sorry, this method not allow!'), ['key' => 'adminError']);
				}
			}catch(\Exception $e){}
			$this->redirect($this->referer());
	}
	
	public function beforeFilter(Event $event){
		$fields = ['id','schedule_type','keyword','start','end','selected'];
		$this->Security->config('unlockedFields', $fields);
		$actions = ['send','getStats','mailerList'];
		if (in_array($this->request->params['action'], $actions)) {
			// for csrf
			$this->eventManager()->off($this->Csrf);
			// for security component
			$this->Security->config('unlockedActions', $actions);
		}
	}

}