<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use Cake\Validation\Validator;

class CustomersController extends AppController
{
	public function initialize(){
		parent::initialize();
		ini_set('memory_limit', '-1');
		$this->loadComponent('Store');
		$this->loadComponent('Membership');
	}
	
	public function index()
	{
		$sort = $this->request->getQuery('sort', NULL);
		$direction = $this->request->getQuery('direction', NULL);
		$limit = $this->request->getQuery('perPage', 50);
		$this->paginate = [
			'sort' =>$sort,
			'direction' =>$direction,
			'limit' =>$limit,
			'maxLimit' => 20000,
		];
		$filterData = [];
		$mobile = $this->request->getQuery('mobile', '');
		$this->set('mobile', $mobile);
		if(!empty($mobile)) { $filterData['mobile'] = $mobile; }
		
		$firstname = $this->request->getQuery('firstname', '');
		$this->set('firstname', $firstname);
		if(!empty($firstname)) { $filterData['firstname'] = $firstname; }
		
		$lastname = $this->request->getQuery('lastname', '');
		$this->set('lastname', $lastname);
		if(!empty($lastname)) { $filterData['lastname'] = $lastname; }
		
		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if(!empty($email)) { $filterData['email'] = $email; }
		
		$address = $this->request->getQuery('address', '');
		$this->set('address', $address);
		if(!empty($lastname)) { $filterData['address'] = $address; }
		
		$city = $this->request->getQuery('city', '');
		$this->set('city', $city);
		if(!empty($city)) { $filterData['city'] = $city; }
		
		$pincode = $this->request->getQuery('pincode', '');
		$this->set('pincode', $pincode);
		if(!empty($pincode)) { $filterData['pincode'] = $pincode; }
		
		$createdFrom = $this->request->getQuery('createdFrom', '');
		$this->set('createdFrom', $createdFrom);
		$createdTo = $this->request->getQuery('createdTo', '');
		$this->set('createdTo', $createdTo);
		if(!empty($createdFrom) && !empty($createdTo)){
			$createdFrom = $createdFrom.' 00:00:01';
			$createdTo = $createdTo.' 23:59:59';
		}else if(!empty($createdFrom)){
			$createdFrom = $createdFrom.' 00:00:01';
			$createdTo = date('Y-m-d').' 23:59:59';
		}else if(!empty($createdTo)){
			$createdFrom = '2015-01-01 00:00:01';
			$createdTo = $createdTo.' 23:59:59';
		}else{
			$createdFrom = '2015-01-01 00:00:01';
			$createdTo = date('Y-m-d').' 23:59:59';
		}
		
		$status = $this->request->getQuery('status', '');
		$this->set('status', $status);
		if( $status !== '' ) { $filterData['is_active'] = "$status"; }
		$query = $this->Customers->find('all', ['conditions'=>$filterData])
				->where(function ($exp, $q) use($createdFrom, $createdTo) {
					return $exp->between('created', $createdFrom, $createdTo);
				})
				->order(['created' => 'DESC']);
		$customers= $this->paginate($query)->toArray();
		$lastCustomerId = (count($customers) > 0) ? $customers[count($customers)-1]->id:0;
		//pr($queryString);die;
		$this->set('queryString', $this->request->getQueryParams());
		$this->set(compact('customers','lastCustomerId'));
		$this->set('_serialize', ['customers','lastCustomerId']);
	}
	
	public function exports($lastCustomerId=1) {  	
    	$this->response->withDownload('exports.csv');
    	$limit = $this->request->getQuery('perPage', 50);
    	$offset = $this->request->getQuery('page', 1);
			$offset = ($offset - 1)*$limit;
    	$filterData = [];
    	//$filterData['id >= '] = $lastCustomerId;

    	$mobile = $this->request->getQuery('mobile', '');
    	if(!empty($mobile)) { $filterData['mobile'] = $mobile; }
    	
    	$firstname = $this->request->getQuery('firstname', '');
    	if(!empty($firstname)) { $filterData['firstname'] = $firstname; }
    	
    	$lastname = $this->request->getQuery('lastname', '');
    	if(!empty($lastname)) { $filterData['lastname'] = $lastname; }
    	
    	$email = $this->request->getQuery('email', '');
    	if(!empty($email)) { $filterData['email'] = $email; }
    	
    	$address = $this->request->getQuery('address', '');
    	if(!empty($lastname)) { $filterData['address'] = $address; }
    	
    	$city = $this->request->getQuery('city', '');
    	if(!empty($city)) { $filterData['city'] = $city; }
    	
    	$pincode = $this->request->getQuery('pincode', '');
    	if(!empty($pincode)) { $filterData['pincode'] = $pincode; }
    	
			$createdFrom = $this->request->getQuery('createdFrom', '');
			$this->set('createdFrom', $createdFrom);
			$createdTo = $this->request->getQuery('createdTo', '');
			$this->set('createdTo', $createdTo);
			if(!empty($createdFrom) && !empty($createdTo)){
				$createdFrom = $createdFrom.' 00:00:01';
				$createdTo = $createdTo.' 23:59:59';
			}else if(!empty($createdFrom)){
				$createdFrom = $createdFrom.' 00:00:01';
				$createdTo = date('Y-m-d').' 23:59:59';
			}else if(!empty($createdTo)){
				$createdFrom = '2015-01-01 00:00:01';
				$createdTo = $createdTo.' 23:59:59';
			}else{
				$createdFrom = '2015-01-01 00:00:01';
				$createdTo = date('Y-m-d').' 23:59:59';
			}
				
				$status = $this->request->getQuery('status', '');
				if( $status !== '' ) { $filterData['is_active'] = "$status"; }
				
			$data	= $this->Customers->find('all', ['conditions'=>$filterData, 'offset'=>$offset,'limit'=>$limit])
					->where(function ($exp, $q) use($createdFrom, $createdTo) {
						return $exp->between('created', $createdFrom, $createdTo);
					})
					->order(['created' => 'DESC'])->toArray();
		
			//pr($data); die;
				$_serialize = 'data';
				$_header = ['ID', 'Mobile', 'First Name', 'Last Name', 'Email', 'Address', 'City', 'Pincode', 'Created Date', 'Modified Date', 'Logdate', 'Logums'];
				$_extract = ['id', 'mobile', 'firstname', 'lastname', 'email', 'address', 'city', 'pincode', 'created', 'modified', 'logdate', 'lognum'];
				$this->set(compact('data', '_serialize', '_header', '_extract'));
				$this->viewBuilder()->setClassName('CsvView.Csv');
				return;    	
  }

	public function view($id = null){
	try{
		$customer = $this->Customers->get($id);
		$member = $this->Membership->getMembership($id);
		//pr($member);
		$error = [];
		if ($this->request->is(['patch', 'post', 'put'])) {
			$customer = $this->Customers->patchEntity($customer, $this->request->getData(), ['validate'=>'adminProfile']);
			$error = $customer->getErrors(); //pr($customer);
			if (empty($error) ) {
				$a = $this->Customers->save($customer);
				if($a){
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}
		$customer = $this->Customers->get($id);
		$error['customer'] = $error;
		$locations = TableRegistry::get('Locations');
		$locList = $locations->find('treeList', ['spacer' => '- ','conditions'=>['parent_id'=>2],'order'=>['title'=>'ASC']]);
		$this->set(compact('customer', 'member','id', 'locList', 'error'));
		$this->set('_serialize', ['customer', 'member', 'id', 'locList', 'error']);
	}catch (\Exception $e){
		$this->redirect(['action'=>'index']);
	}
	}

	public function add($id = null)
	{
	try{
		$customer = $this->Customers->newEntity();
		$error = [];
		if ($this->request->is(['patch', 'post', 'put'])) {
			$customer = $this->Customers->patchEntity($customer, $this->request->getData(), ['validate'=>'adminNewProfile']);
			$error = $customer->getErrors();				
			if (empty($error) ) {
				if($this->Customers->save($customer)){
					$input = [
						'userId'=>$customer->id,
						'firstname'=>$customer->firstname,
						'lastname'=>$customer->lastname,
						'address'=>$customer->address,
						'city'=>$customer->city,
						'pincode'=>$customer->pincode,
						'mobile'=>$customer->mobile,
						'email'=>$customer->email,
						'state'=>$customer->state,
						'country'=>$customer->country,
						'setdefault'=>1,
					];
					$this->Customer->addAddresses($input);
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'index']);
				}else{
					$this->Flash->error(__('Sorry, there are something issue, try again!'), ['key' => 'adminError']);
				}
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}
		$error['customer'] = $error;
		$locations = TableRegistry::get('Locations');
		$locList = $locations->find('treeList', ['spacer' => '- ','conditions'=>['parent_id'=>2],'order'=>['title'=>'ASC']]);
		$this->set(compact('customer', 'member','id', 'locList', 'error'));
		$this->set('_serialize', ['customer', 'member', 'id', 'locList', 'error']);
	}catch (\Exception $e){
		$this->redirect(['action'=>'index']);
	}
	}

	public function addresses($id = null)
	{
	try{
		$dataTable 		= TableRegistry::get('Addresses');
		$addressId		= $this->request->getQuery('address-id', '0');
		$address 		= ($addressId > 0) ? $dataTable->get($addressId) : $dataTable->newEntity();
		$error 			= []; 
		if ($this->request->is(['patch'])) {
			$addressId = $this->request->data('address-id');
			$dataTable->query()->update()->set(['set_default'=>0])->where(['customer_id'=>$id])->execute();
			$dataTable->query()->update()->set(['set_default'=>1])->where(['id'=>$addressId])->execute();
			$this->Flash->success(__('Default Address updated successfully!'), ['key' => 'adminSuccess']);
		}
		if ($this->request->is(['delete'])) {
			$addressId = $this->request->data('address-id');
			$dataTable->query()->delete()->set(['set_default'=>'1'])->where(['id'=>$addressId])->execute();
			$this->Flash->success(__('Address deleted successfully!'), ['key' => 'adminSuccess']);
		}
		if ($this->request->is(['post', 'put'])) {
			$address->customer_id = $id;
			$validator = new Validator();
			$validator
			->notEmpty('firstname','Please enter firstname!')
			->add('firstname', [
				'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The firstname should be 3 to 20 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The firstname contains only a-z, 0-1 and space characters only!']
			])
			->notEmpty('lastname','Please enter lastname!')
			->add('lastname', [
				'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The lastname should be 3 to 20 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The lastname contains only a-z, 0-1 and space characters only!']
			])
			->notEmpty('address','Please enter address!')
			->notEmpty('city','Please enter City/Town/District!')
			->add('city', [
				'length' => ['rule' => ['lengthBetween', 3, 500], 'message' => 'The city should be 3 to 20 character long!'],
				'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The city contains only a-z, 0-1 and space characters only!']
			])
			->notEmpty('pincode','Please enter pincode!')
			->add('pincode', [
				'length' => ['rule' => ['lengthBetween', 6, 6], 'message' => 'The pincode should be 6 digits long!'],
				'charNum' => ['rule' =>['custom', '/^[0-9]*$/i'], 'message' => 'The pincode contains only 0-1 number only!']
			])
			->notEmpty('mobile','Please enter mobile!')
			->add('mobile', [
				'length' => ['rule' => ['lengthBetween', 10, 10], 'message' => 'The mobile should be 10 digits long!'],
				'charNum' => ['rule' =>['custom', '/^[0-9]*$/i'], 'message' => 'The mobile contains only 0-1 number only!']
			])
			->notEmpty('email','Please enter email!')
			->email('email',[
				'valid'=>[
					'rule'=>'email',
					'message'=>'Please enter a valid email id!'
				]
			]);				
			$error = $validator->errors($this->request->getData());
			$address 	= $dataTable->patchEntity($address, $this->request->getData());
			if (empty($error) ) {
				if($dataTable->save($address)){
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					$this->redirect(['action'=>'addresses', $id, 'key',md5($id)]);
				}else{
					$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);	
				}
			} else {
				$this->Flash->error(__('Sorry, Please fill required fields!'), ['key' => 'adminError']);
			}
		}
		$addressesList 	= $dataTable->find('all', ['conditions'=>['customer_id'=>$id]]);
		$locationList 	= TableRegistry::get('Locations')->find('treeList', ['spacer' => '- ','conditions'=>['parent_id'=>2],'order'=>['title'=>'ASC']])->toArray();
		$locationList 	= array_combine($locationList,$locationList);
		$this->set(compact('address', 'addressesList','id', 'locationList', 'error'));
		$this->set('_serialize', ['address', 'addressesList', 'id', 'locationList', 'error']);
	}catch (\Exception $e){
		$this->redirect(['action'=>'index']);
	}
	}

	public function delete($id = null)
	{
			$this->request->allowMethod(['post', 'delete']);
			$customer = $this->Customers->get($id);
			if ($this->Customers->delete($customer)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
		return $this->redirect(['action' => 'index']);
			} else {
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
			}
			return true;
	}

	
	public function wallet($id = 0){
		
		$pbPointsTable	= TableRegistry::get('PbCashPoints');
    	$pbWallets      = $pbPointsTable->newEntity();
		if( $this->request->is(['post']) ){
			$action = 1;
			$orderId = $this->request->getData("id_order", 0);
			$transaction_type = $this->request->getData("transaction_type", 0);
			$comments = $this->request->getData("comments", '');

			$pb_cash = $this->request->getData("pb_cash", 0);
			$pb_points = $this->request->getData("pb_points", 0);
			$voucher_amount = $this->request->getData("voucher_amount", 0);
			
			if( !is_numeric($orderId) || ($orderId == 0) ){
				$action = 0;
				$this->Flash->error(__('Sorry, Please enter order number!'), ['key' => 'adminError']);
			}else if( ( $voucher_amount > 0 ) && (($voucher_amount % 501 == 0) || ($voucher_amount % 100 == 0)) ){
				$action = 0;
				$this->Flash->error(__('Sorry, Voucher amount should be multiple of "501" or "100" !'), ['key' => 'adminError']);
			}else if ( empty($comments) ){
				$action = 0;
				$this->Flash->error(__('Sorry, Please enter reason for this transaction!'), ['key' => 'adminError']);
			}

			if( $action && (($pb_cash > 0) || ($pb_points > 0) || ($voucher_amount > 0)) ){
					$this->Store->logPBWallet($id, $transaction_type, 0, $orderId, $pb_cash, $pb_points, $voucher_amount, $comments);
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					$this->redirect(['action'=>'wallet',$id, 'key', md5($id)]);
				}
		}
		$this->set('queryString', $this->request->getQueryParams());
		$limit			= $this->request->getQuery('limit', 50);
		$this->paginate	= [
				'limit' 	=> $limit, // limits the rows per page
				'maxLimit' 	=> 2000,
			];
		
		$filterData 	= [];
		
		$filterData['id_customer']	= $id;
		$this->set('id_customer', $id);
		
		$transation_id 	= $this->request->getQuery('id', '');
		$this->set('id', $transation_id);
		if(!empty($transation_id))
		{
			$filterData['id']	= $transation_id;
		}
		
		$referrer_name	= $this->request->getQuery('referrer_name', '');
		$this->set('referrer_name', $referrer_name);
		if(!empty($referrer_name))
		{
			// $filterData['referrer_name']	= $couponCode;
		}
		
		$id_order		= $this->request->getQuery('id_order', '');
		$this->set('id_order', $id_order);
		if(!empty($id_order))
		{
			$filterData['id_order']	= $id_order;
		}
		
		$pb_cash		= $this->request->getQuery('pb_cash', '');
		$this->set('pb_cash', $pb_cash);
		if(!empty($pb_cash))
		{
			$filterData['pb_cash']			= $pb_cash;
		}
		
		$pb_points		= $this->request->getQuery('pb_points', '');
		$this->set('pb_points', $pb_points);
		if(!empty($pb_points))
		{
			$filterData['pb_points']		= $pb_points;
		}
		
		$voucher_amount	= $this->request->getQuery('voucher_amount', '');
		$this->set('voucher_amount', $voucher_amount);
		if(!empty($voucher_amount))
		{
			$filterData['voucher_amount']	= $voucher_amount;
		}
		
		$transaction_type	= $this->request->getQuery('transaction_type', '');
		$this->set('transaction_type', $transaction_type);
		if($transaction_type !== '')
		{
			$filterData['transaction_type']	= $transaction_type;
		}
		
		$comments	= $this->request->getQuery('comments', '');
		$this->set('comments', $comments);
		if($comments !== '')
		{
			$filterData['comments LIKE']	= "%".$comments."%";
		}
		
		$voucher_amount	= $this->request->getQuery('voucher_amount', '');
		$this->set('voucher_amount', $voucher_amount);
		if($voucher_amount !== '')
		{
			$filterData['voucher_amount =']	= $voucher_amount;
		}
		
		$pb_points	= $this->request->getQuery('pb_points', '');
		$this->set('pb_points', $pb_points);
		if($pb_points !== '')
		{
			$filterData['pb_points =']	= $pb_points;
		}
		
		$pb_cash	= $this->request->getQuery('pb_cash', '');
		$this->set('pb_cash', $pb_cash);
		if($pb_cash !== '')
		{
			$filterData['pb_cash =']	= $pb_cash;
		}
		
		$createdFrom	= $this->request->getQuery('created_from', '');
		$this->set('createdFrom', $createdFrom);
		
		$createdTo 		= $this->request->getQuery('created_to', '');
		$this->set('createdTo', $createdTo);
		
		if(!empty($createdFrom) && !empty($createdTo))
		{
			$createdFromNew	= date('Y-m-d 00:00:00', strtotime($createdFrom));
			$filterData['transaction_date >='] = $createdFromNew;
			$createdToNew	= date('Y-m-d 23:59:59', strtotime($createdTo));
			$filterData['transaction_date <='] = $createdToNew;
		}
		else if(!empty($createdFrom))
		{
			$createdFromNew	= date('Y-m-d 00:00:00', strtotime($createdFrom));
			$filterData['transaction_date >='] = $createdFromNew;
		}
		else if(!empty($createdTo))
		{
			$createdToNew	= date('Y-m-d 23:59:59', strtotime($createdTo));
			$filterData['transaction_date <='] = $createdToNew;
		}
		
		
		$history		= $pbPointsTable->find('all', ['conditions'=>$filterData])
							->order(['id' => 'DESC']);
		$history	= $this->paginate($history);
		// pr($history);
		// die;
        $this->set(compact('history', 'id','pbWallets'));
        $this->set('_serialize', ['history', 'id', 'pbWallets']);
	}
	
	public function cart($id = 0)
	{
		$cartTable	= TableRegistry::get('Carts');
		$productTable	= TableRegistry::get('Products');
		$this->set('queryString', $this->request->getQueryParams());
		$limit			= $this->request->getQuery('limit', 50);
		$this->paginate	= [
				'limit' 	=> $limit, // limits the rows per page
				'maxLimit' 	=> 2000,
			];
		
		$filterData 	= [];
		
		$sku 	= $this->request->getQuery('sku', '');
		$this->set('sku', $sku);
		if(!empty($sku)) { $filterData['sku_code']	= $sku; }
		
		$title 	= $this->request->getQuery('title', '');
		$this->set('title', $title);
		
		$fromPrice 	= $this->request->getQuery('fromPrice', '0');
		$this->set('fromPrice', $fromPrice);
		
		$toPrice 	= $this->request->getQuery('toPrice', '5000');
		$this->set('toPrice', $toPrice);
		
		$checked 	= $this->request->getQuery('checked', []); 
		$checked 	= array_unique($checked);
		$checked 	= ( isset($checked[0]) && ($checked[0] == 0) ) ? array_splice($checked, 1) : $checked;
		$redirectionAction = 0;
		//delete unchecked items from cart
		$allChecked = $this->request->getQuery('allChecked', []);
		if( count($allChecked) ){
			$cartTable->query()->delete()->where(['customer_id'=>$id, 'product_id IN'=>$allChecked])->execute();
		}
		if( count($checked) ){
			$addedItem 	= $cartTable->find()->select(['product_id'])->where(['customer_id'=>$id])->hydrate(false)->toArray();
			$addedItem 	= array_column($addedItem, 'product_id');
			$query 		= $cartTable->query()->insert(['customer_id', 'product_id']);
			$action 	= 0;
			foreach( $checked as $value ){
				if( !in_array($value, $addedItem) ){
					$query 	= $query->values(['customer_id'=>$id, 'product_id'=>$value]);
					$action 	= 1;
				}
			}
			if($action){
				$query = $query->execute();
				$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
			}else{
				$this->Flash->error(__('Sorry, selected item already added!'), ['key' => 'adminError']);
			}
			$redirectionAction = 1;
		}
		if( $redirectionAction ){ return $this->redirect(['action' => 'cart', $id,'key', md5($id)]); }
		if( $this->request->is(['get']) ){
			if( count($filterData) > 0 || !empty($title) || ($fromPrice != 0) || ($toPrice != 5000) ){
				if( !empty($title) ){
					$filterData['title LIKE'] = "%$title%";
				}
				$cart	= 	$productTable->find('all', ['fields'=>['id','title','sku_code','price', 'checked'=>'0'],'conditions'=>$filterData])
							->where(function ($exp, $q) use($fromPrice, $toPrice) {
								return $exp->between('price', $fromPrice, $toPrice);
							});
			}else{
				$cart	= 	$productTable->find('all', ['fields'=>['id','title','sku_code','price', 'checked'=>'1']])
							->matching('Carts', function($q) use ($id){
								return $q->select(['id','product_id','qty','created'])->where(['customer_id'=>$id]);
							});
			}
			$cart 	= $cart->where(function ($exp, $q) use($fromPrice, $toPrice) {
						return $exp->between('price', $fromPrice, $toPrice);
					});
			$cart	= $this->paginate($cart);
		}
        $this->set(compact('cart', 'id'));
        $this->set('_serialize', ['cart', 'id']);
	}
	
	public function wishlist($id = 0)
	{
		$limit			= $this->request->getQuery('limit', 50);
		$this->paginate	= [
				'limit' 	=> $limit, // limits the rows per page
				'maxLimit' 	=> 2000,
			];
		
		if( $this->request->is(['get']) ){
			$wishlist	= 	TableRegistry::get('Products')->find('all', ['fields'=>['id','title','sku_code','price']])
						->matching('Wishlists', function($q) use ($id){
							return $q->select(['id','created'])->where(['customer_id'=>$id]);
						});
			$wishlist	= $this->paginate($wishlist);
		}
        $this->set(compact('wishlist', 'id'));
        $this->set('_serialize', ['wishlist', 'id']);
	}
	
	public function orders($id = null)
	{
		$this->set('queryString', $this->request->getQueryParams());
		$limit			= $this->request->getQuery('limit', 50);
		$this->paginate	= [
				'limit' 	=> $limit, // limits the rows per page
				'maxLimit' 	=> 2000,
			];
		
		$filterData 	= [];
		
		$filterData['customer_id']	= $id;
		$this->set('id_customer', $id);
		
		$id_order 	= $this->request->getQuery('id_order', '');
		$this->set('id_order', $id_order);
		if(!empty($id_order))
		{
			$filterData['id']	= $id_order;
		}
		
		$createdFrom	= $this->request->getQuery('created_from', '');
		$this->set('createdFrom', $createdFrom);
		
		$createdTo 		= $this->request->getQuery('created_to', '');
		$this->set('createdTo', $createdTo);
		
		if(!empty($createdFrom) && !empty($createdTo))
		{
			$createdFromNew	= date('Y-m-d 00:00:00', strtotime($createdFrom));
			$filterData['created >='] = $createdFromNew;
			$createdToNew	= date('Y-m-d 23:59:59', strtotime($createdTo));
			$filterData['created <='] = $createdToNew;
		}
		else if(!empty($createdFrom))
		{
			$createdFromNew	= date('Y-m-d 00:00:00', strtotime($createdFrom));
			$filterData['created >='] = $createdFromNew;
		}
		else if(!empty($createdTo))
		{
			$createdToNew	= date('Y-m-d 23:59:59', strtotime($createdTo));
			$filterData['created <='] = $createdToNew;
		}
		
		$bill_name		= $this->request->getQuery('bill_name', '');
		$this->set('bill_name', $bill_name);
		if(!empty($bill_name))
		{
			$tempData['billing_firstname LIKE ']			= '%'.$bill_name.'%';
			$tempData['billing_lastname LIKE ']			= '%'.$bill_name.'%';
			$filterData['AND'][0]['OR']	= $tempData;
		}
		
		$ship_name		= $this->request->getQuery('ship_name', '');
		$this->set('ship_name', $ship_name);
		if(!empty($ship_name))
		{
			$tempData['shipping_firstname LIKE ']			= '%'.$ship_name.'%';
			$tempData['shipping_lastname LIKE ']			= '%'.$ship_name.'%';
			$filterData['AND'][1]['OR']	= $tempData;
		}
		
		$from_amount		= $this->request->getQuery('from_amount', '');
		$this->set('from_amount', $from_amount);
		if(!empty($from_amount))
		{
			$filterData['payment_amount >= ']		= $from_amount;
		}
		
		$to_amount	= $this->request->getQuery('to_amount', '');
		$this->set('to_amount', $to_amount);
		if(!empty($to_amount))
		{
			$filterData['payment_amount <=']	= $to_amount;
		}
		
		$ordersTable	= TableRegistry::get('Orders');
		$orders			= $ordersTable->find('all', ['conditions'=>$filterData])
							->order(['created' => 'DESC']);
		$orders			= $this->paginate($orders);
		// pr($orders);
		// die;
        $this->set(compact('orders', 'id'));
        $this->set('_serialize', ['orders', 'id']);
	}

	public function search()
	{
		$search 			= $this->request->getQuery('term');
		$responseArray		= array();
		$customerTable		= $this->Customers;
		$customerData		= $customerTable->find('all', ['conditions' => ['email LIKE ' => '%'.$search.'%']])->toArray();
		if(!empty($customerData))
		{
			$counter		= 0;
			foreach($customerData as $temp_customer)
			{
				$responseArray[$counter]['id']			= $temp_customer->id;
				$responseArray[$counter]['label']		= $temp_customer->email;
				$responseArray[$counter]['value']		= $temp_customer->email;
				$responseArray[$counter]['firstname']	= $temp_customer->firstname;
				$responseArray[$counter]['lastname']	= $temp_customer->lastname;
				$responseArray[$counter]['address']		= $temp_customer->address;
				$responseArray[$counter]['city']		= $temp_customer->city;
				$responseArray[$counter]['pincode']		= $temp_customer->pincode;
				$responseArray[$counter]['mobile']		= $temp_customer->mobile;
				$responseArray[$counter]['email']		= $temp_customer->email;
				$responseArray[$counter]['location_id']	= $temp_customer->location_id;
				$counter++;
			}
		}
		echo json_encode($responseArray);
		exit;
	}

	public function getAddresses()
	{
		$id 			= $this->request->getQuery('customer-id', 0);
		$dataTable 		= TableRegistry::get('Addresses');
		$addressesList 	= $dataTable->find('all', ['conditions'=>['customer_id'=>$id]])->hydrate(false)->toArray();
		echo json_encode($addressesList);
		exit;
	}

}