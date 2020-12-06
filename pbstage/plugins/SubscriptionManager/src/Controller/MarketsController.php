<?php
namespace SubscriptionManager\Controller;

use SubscriptionManager\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Event\Event;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class MarketsController extends AppController
{
	public function initialize(){
		parent::initialize();
		$this->loadComponent('SubscriptionManager.Drift');

	}
	
	public function index($id=0)
	{
		$mailers = $mailerList = [];
		$this->set('title', 'Drift Marketing');
		try{ 
			$dataTable 	= TableRegistry::get('SubscriptionManager.Buckets');
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
				$bucketMailers 	= TableRegistry::get('SubscriptionManager.DriftMailers')->find('all', ['feilds'=>['id'],'conditions'=>['bucket_id'=>$id],'limit'=>1])->hydrate(false)->toArray();
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
								'fields'=>['bucket_id']
								]
							])
							->hydrate(false)->toArray();
			//pr($mailerList);
		}catch(\Exception $e){}
		$this->set(compact('mailer','mailerList'));
		$this->set('_serialize', ['mailer','mailerList']);
	}

	public function mailerList($id=0)
	{
		$mailers = $mailerList = [];
		$this->set('title', 'Drift Mailer List');
		try{
			$mailerList 	= TableRegistry::get('SubscriptionManager.DriftMailerLists')->find('all', ['fields'=>['id','drift_mailer_id','created'], 'order'=>['DriftMailerLists.id'=>'DESC']])
							->contain(['DriftMailers' =>[
								'fields'=>['DriftMailers.id','subject','title']
								]
							])
							->hydrate(false)->toArray();
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
	
			$dataTable 	= TableRegistry::get('SubscriptionManager.DriftMailers');
			$query 		= $dataTable->find('all', ['conditions'=>$filterData])->order(['id'=>'DESC']);
			$mailers	= $this->paginate($query)->toArray();
			//pr($queryString);die;
			$bucket 	= TableRegistry::get('SubscriptionManager.Buckets')->get($bucketId);
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
			$param     = ['start_date'=>$startDate,'end_date'=>$endDate,'categories'=>PC['SENDGRID']['cat'].$catId];
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
			$dataTable 	= TableRegistry::get('SubscriptionManager.DriftMailers');
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
			$bucket 	= TableRegistry::get('SubscriptionManager.Buckets')->get($bucketId);
			$this->set('title','Drift Marketing: '.$bucket->title);
			$this->set(compact('mailer','bucketId','bucket','id','errors'));
			$this->set('_serialize', ['mailer','bucketId','bucket','id','errors']);	
		}catch(\Exception $e){
			$this->redirect($this->referer());
		}
	}
	
	public function send($bucketId=0, $key=null, $md5=null)
    {
		try {
			$id = $this->request->getData('id', 0);
			if ( $id < 1 ){
				$this->Flash->success(__('Sorry, please try again!'), ['key' => 'adminError']);
			} else { 
				$customerId = 0; //115759 for mohd.afroj@gmail.com
				$param = [
					'mailer_id' =>$id,
					'customer_id' => $customerId
				];
				if ( $this->Drift->sendToCustomers($param) ) {
					$this->Flash->success(__('Mailer successfully sent!'), ['key' => 'adminSuccess']);
				} else {
					$this->Flash->success(__('Sorry, record not found to sent mailer!'), ['key' => 'adminSuccess']);
				}
			}
		}catch(\Exception $e){ $this->Flash->success(__('Sorry, there are some issue, try later!'), ['key' => 'adminError']); }
		$this->redirect($this->referer());
	}
	
	public function test($bucketId=0, $key=null, $md5=null, $id, $ref=null, $refmd5=null)
    {
		try {
			$email = $this->request->getData('email');
			$receiver_email = $this->request->getData('receiver_email');
			if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
				$this->Flash->success(__('Sorry, Please enter a  valid email id!'), ['key' => 'adminError']);
			} else if ( !empty($receiver_email) && !filter_var($receiver_email, FILTER_VALIDATE_EMAIL) ) {
				$this->Flash->success(__('Sorry, Please enter a valid reveiver email id!'), ['key' => 'adminError']);
			} else {
				$dataTable 	= TableRegistry::get('SubscriptionManager.DriftMailers');
				$mailer 	= $dataTable->get($id);
				$mailer->email = $email;
				$dataTable->save($mailer);
				$customerData 	= TableRegistry::get('SubscriptionManager.Customers')->find('all', ['field'=>['id'],'conditions'=>['email'=>$email]])->toArray();
				$customerId = $customerData[0]['id'] ?? 0;
				if ( $customerId > 0 ) {
					$param  = [
						'mailer_id' => $id,
						'receiver_email'=>$receiver_email,
						'customer_id'=>$customerId
					];
					$email = empty($receiver_email) ? $email : $receiver_email;
					if ( $this->Drift->sendToCustomers($param) ) {
						$this->Flash->success(__('Mail successfully sent to '.$email), ['key' => 'adminSuccess']);
					} else {
						$this->Flash->success(__('Sorry, Email is not sent!'), ['key' => 'adminError']);
					}
				} else {
					$this->Flash->success(__('Sorry, There is no account for email: '.$email), ['key' => 'adminError']);
				}
			}
		}catch(\Exception $e){
			$this->Flash->success(__('Sorry, there are some issue, try later!'), ['key' => 'adminError']);
		}
		$this->redirect($this->referer());
	}
	
    public function delete($id = 0)
    {
        try{
			if( $this->request->is(['delete']) ){
				$dataTable 	= TableRegistry::get('SubscriptionManager.DriftMailers');
				$mailer = $dataTable->get($id);
				if ($dataTable->delete($mailer)) {			
					$this->Flash->success(__('The record has been deleted!'), ['key' => 'adminSuccess']);
				} else {
					$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, this method not allow!'), ['key' => 'adminError']);
			}
		}catch(\Exception $e){
		}
		$this->redirect($this->referer());
    }
	
	public function beforeFilter(Event $event)
	{
		$fields = ['id','schedule_type','keyword','start','end', 'receiver_email'];
		$this->Security->config('unlockedFields', $fields);
		$actions = ['sendMailer','getStats'];
		if (in_array($this->request->params['action'], $actions)) {
			// for csrf
			$this->eventManager()->off($this->Csrf);
			// for security component
			$this->Security->config('unlockedActions', $actions);
		}
	}

}