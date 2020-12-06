<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use Cake\Routing\Router;
use Cake\Validation\Validator;

class PgsController extends AppController
{
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }
    
	public function index()
    {
		$this->set('title', 'Payment Methods');
		$dataTable = TableRegistry::get('PaymentMethods');
		if( $this->request->is('post') ){
			$dataTable->query()->update()->set(['active_default' => 0])->execute();
			$id = $this->request->getData('id');
			$dataTable->query()->update()->set(['active_default'=>1])->where(['id'=>$id])->execute();
			$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
		}
		$pgs = $dataTable->find('all',['order'=>['sort_order'=>'ASC']])->toArray();
		$this->set(compact('pgs'));
        $this->set('_serialize', ['pgs']);
    }

    
    public function add($key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5('pgs') ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
		$dataTable = TableRegistry::get('PaymentMethods');
		$pg = $dataTable->newEntity();
        if ($this->request->is('post')) {
            $pg = $dataTable->patchEntity($pg, $this->request->getData());
			$validator = new Validator();
			$validator
				->notEmpty('title','Please enter payment method title!');			
			$validator
				->notEmpty('code','Please enter payment method title!');			
			$validator
				->notEmpty('fees','Please enter fees amount!')
				->add('fees', 'fees', [
					'rule' => function($value){
						return ($value > -1);
					}, 
					'message' => 'Sorry, Fees should be zero or greater!'
				]);

			$validator->inList('status', ['1','0']);
			$error = $validator->errors($this->request->getData());
			if( empty($error) ){
				if ($dataTable->save($pg)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'index']);
				}
				$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
        $this->set(compact('pg','error'));
        $this->set('_serialize', ['pg', 'error']);
    }
	
    public function edit($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
		$dataTable = TableRegistry::get('PaymentMethods');
        $pg = $dataTable->get($id);
        if ($this->request->is(['post', 'put'])) {
			$pg = $dataTable->patchEntity($pg, $this->request->getData());
			$validator = new Validator();
			$validator
				->notEmpty('title','Please enter payment method title!');
			
			$validator
				->notEmpty('fees','Please enter fees amount!')
				->add('fees', 'fees', [
					'rule' => function($value){
						return ($value > -1);
					}, 
					'message' => 'Sorry, Fees should be zero or greater!'
				]);

			$validator
				->integer('sort_order','Please enter number only!')
				->notEmpty('sort_order','Please enter sort order number!')
				->add('sort_order', [
					'message' => ['rule' => function($value){ return ($value > -1); }, 'message' => 'Sort order should be zero or greater!']
				]);

			$validator->inList('status', ['1','0']);
			$error = $validator->errors($this->request->getData());
			if( empty($error) ){
				if ($dataTable->save($pg)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'index']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
        $this->set(compact('pg','id','error'));
        $this->set('_serialize', ['pg','id','error']);
    }

	public function shipvendors()
    {
		$dataTable  = TableRegistry::get('Shipvendors');
		$this->set('title', 'Shipping Vendors');
		//echo $this->request->method();
		if ($this->request->is(['post'])) {
			$setDefault = $this->request->getData('setDefault');
			if ( $setDefault > 0 ) {
				$dataTable->query()->update()->set(['set_default' => 0])->execute();
				$dataTable->query()->update()->set(['set_default' => 1])->where(['id'=>$setDefault])->execute();
			} else {
				$vendorId = $this->request->getData('vendorId');
				$setAction = $this->request->getData('setAction');
				$pincodesFile = $this->request->getData('pincodes');
				$pincodesTable  = TableRegistry::get('ShipvendorPincodes');
				if( $setAction ){
					if( isset($pincodesFile['error']) && ( $pincodesFile['error'] == 0 ) ) {
						$pinocdeList = [];
						$handle = fopen($pincodesFile['tmp_name'], "r");
						$checkColumn = 0;
						while ($row = fgetcsv($handle)){
							if ( $checkColumn ){
								$pinocdeList[] = [
									'shipvendor_id'=> $vendorId,
									'pincode'=>$row[0]
								];
							} else {
								if( isset($row[0]) && ($row[0] == 'pincode') ) { $checkColumn = 1; }
							}
						}
						fclose($handle); // close file pointer
						if( !empty($pinocdeList) ){
							$pincodesTable->query()->delete()->where(['shipvendor_id' => $vendorId])->execute();
							$allPincodes = $pincodesTable->newEntities($pinocdeList);
							if( $pincodesTable->saveMany($allPincodes) ){
								$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
							}
						}
						if( $checkColumn == 0 ) {
							$this->Flash->error(__('Sorry, First column should be pincode!'), ['key' => 'adminError']);
						}
						//pr($pinocdeList);
					} else {
						$this->Flash->error(__('Sorry, uploaded file have error!'), ['key' => 'adminError']);
					}
				} else {
					$dataList = $dataTable->find('all', ['conditions'=>['Shipvendors.id' =>$vendorId]])
					->contain(
						['ShipvendorPincodes' => [
							'queryBuilder' => function ($q){
								return $q->select(['shipvendor_id', 'pincode']);
							}
						]
					])
					->hydrate(0)->toArray();
					$venderName = $dataList[0]['title'] ?? '';
					$dataList = $dataList[0]['shipvendor_pincodes'] ?? [];
					if( !empty($dataList) ) { 
						$this->Flash->success(__('Pincodes are found for selected Vendor!'), ['key' => 'adminSuccess']);
						$this->response = $this->response->withDownload($venderName.'-pincodes.csv');
						$_serialize = 'dataList'; //pr($dataList); die;
						$_header = ['vendor', 'pincode'];
						$_extract = ['shipvendor_id', 'pincode'];
						$this->set(compact('dataList', '_serialize', '_header', '_extract'));
						$this->viewBuilder()->setClassName('CsvView.Csv');
						return;
					} else {
						$this->Flash->error(__('Sorry, pincodes not found for selected Vendor!'), ['key' => 'adminError']);
					}
				}
			}
		}
		$vendorList = $dataTable->find('all')->hydrate(false)->toArray();
        $this->set(compact('vendorList'));
        $this->set('_serialize', ['vendorList']);
    }
	
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
			return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
        }
        return true;
    }
	
	public function beforeFilter(Event $event)
	{
		$this->Security->config('unlockedFields', ['id', 'setDefault', 'prepaid', 'postpaid', 'active_default', 'pincodes']);
		$actions = ['saverelated','updateImages'];

		if (in_array($this->request->params['action'], $actions)) {
			// for csrf
			//$this->eventManager()->off($this->Csrf);
			// for security component
			$this->Security->config('unlockedActions', $actions);
		}
	}

	public function couriers($code='')
    {
		$this->set('title', 'Couriers Methods');
		$dataTable = TableRegistry::get('Couriers');
		if( $this->request->is('post') ){
			$dataTable->query()->update()->set(['prepaid'=>0, 'postpaid'=>0])->execute();
			$prepaid = $this->request->getData('prepaid');
			$postpaid = $this->request->getData('postpaid');
			$prepaid = ($prepaid > 0) ? $prepaid : 0;
			$postpaid = ($postpaid > 0) ? $postpaid : 0;
			$dataTable->query()->update()->set(['prepaid'=>1])->where(['id'=>$prepaid])->execute();
			$dataTable->query()->update()->set(['postpaid'=>1])->where(['id'=>$postpaid])->execute();
			$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
		}else if ( $code == 'reset' ){
			$dataTable->query()->update()->set(['prepaid'=>0, 'postpaid'=>0])->execute();
			$this->Flash->success(__('The record has been reset!'), ['key' => 'adminSuccess']);
			$this->redirect(['action'=>'Couriers']);
		}
		$couriers = $dataTable->find('all',['conditions'=>['id !='=>3],'order'=>['title'=>'ASC']])->toArray();
		$this->set(compact('couriers'));
        $this->set('_serialize', ['couriers']);
    }

	public function notifications(){
		$str = '';
		if( $this->request->is(['ajax']) ){
			$countter = 0;
			$liData = '';
			$productsTable = TableRegistry::get('Products');
			$query = $productsTable->find('all',['conditions'=>['is_stock'=>'out_of_stock','is_active'=>'active']]);
			$query = $query->select(['total'=>$query->func()->count('*')])
					 ->hydrate(false)
					 ->toArray();
			if( isset($query[0]['total']) && $query[0]['total'] > 0 ){
				$countter++;
				$link = Router::url([ 
					'controller'=>'Products','action'=>'index', '?'=>['is_stock'=>'out_of_stock']
					]);
				$liData .= '<li><a href="'.$link.'">Out of Stock Products</a></li>';
			}
	
			$str  = '<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="fa fa-bell-o"></i>
						<span class="label label-warning">'.$countter.'</span>
					  </a>
					  <ul class="dropdown-menu" role="menu" style="width:20%;">'.$liData.'</ul>';				  
			
		}
		echo $str; die;
	}

}
