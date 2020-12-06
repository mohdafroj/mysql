<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;

/**
 * Reviews Controller
 *
 * @property \App\Model\Table\ReviewsTable $Reviews
 *
 * @method \App\Model\Entity\Review[] paginate($object = null, array $settings = [])
 */
class ReviewsController extends AppController
{
	public function initialize(){
		parent::initialize();
		$this->loadComponent('Store');
	}

    public function index()
    {
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$filterData = [];
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['Reviews.id'] = $id; }
		
		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if(!empty($email)) { $filterData['Customers.email'] = $email; }
		
		$skuCode = $this->request->getQuery('sku_code', '');
		$this->set('skuCode', $skuCode);
		if(!empty($skuCode)) { $filterData['Products.sku_code'] = $skuCode; }
		
		$title = $this->request->getQuery('title', '');
		$this->set('title', $title);
		if(!empty($title)) { $filterData['Reviews.title'] = $title; }
		
		$description = $this->request->getQuery('description', '');
		$this->set('description', $description);
		if(!empty($description)) {
			$filterData['Reviews.description LIKE'] = "%$description%";
		}
		
		$rating = $this->request->getQuery('rating', '');
		$this->set('rating', $rating);
		if(!empty($rating)) { $filterData['Reviews.rating'] = $rating; }
		
		$locationIP = $this->request->getQuery('location_ip', '');
		$this->set('locationIP', $locationIP);
		if(!empty($locationIP)) { $filterData['Reviews.location_ip'] = $locationIP; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['Reviews.created LIKE'] = "$created%";
		}
		
		$isActive = $this->request->getQuery('is_active', '');		
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['Reviews.is_active'] = $isActive; }
		
		$query = $this->Reviews->find('all', ['contain'=>['Customers', 'Products'], 'fields'=>['Customers.email','Products.sku_code','Reviews.id','Reviews.title','Reviews.description','Reviews.rating','Reviews.location_ip','Reviews.created','Reviews.is_active'],'conditions'=>$filterData])->order(['Reviews.created' => 'DESC']);
		$reviews= $this->paginate($query);
		
        $this->set(compact('reviews'));
        $this->set('_serialize', ['reviews']);
    }

    public function exports()
    {
		$this->response->withDownload('exports.csv');
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$filterData = [];
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['Reviews.id'] = $id; }
		
		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if(!empty($email)) { $filterData['Customers.email'] = $email; }
		
		$skuCode = $this->request->getQuery('sku_code', '');
		$this->set('skuCode', $skuCode);
		if(!empty($skuCode)) { $filterData['Products.sku_code'] = $skuCode; }
		
		$title = $this->request->getQuery('title', '');
		$this->set('title', $title);
		if(!empty($title)) { $filterData['Reviews.title'] = $title; }
		
		$description = $this->request->getQuery('description', '');
		$this->set('description', $description);
		if(!empty($description)) {
			$filterData['Reviews.description LIKE'] = "%$description%";
		}
		
		$rating = $this->request->getQuery('rating', '');
		$this->set('rating', $rating);
		if(!empty($rating)) { $filterData['Reviews.rating'] = $rating; }
		
		$locationIP = $this->request->getQuery('location_ip', '');
		$this->set('locationIP', $locationIP);
		if(!empty($locationIP)) { $filterData['Reviews.location_ip'] = $locationIP; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['Reviews.created LIKE'] = "$created%";
		}
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['is_active'] = $isActive; }
		
		$data = $this->Reviews->find('all', ['contain'=>['Customers', 'Products'], 'fields'=>['Customers.email','Products.sku_code','Reviews.id','Reviews.title','Reviews.description','Reviews.rating','Reviews.location_ip','Reviews.created','Reviews.modified','Reviews.is_active'],'conditions'=>$filterData])->order(['Reviews.created' => 'DESC'])->toArray();
    	
		
		$_serialize='data';
    	$_header = ['ID', 'Customer Email', 'SKU Code', 'Title', 'Description', 'Rating', 'IP Address', 'Created Date', 'Modified Date', 'Status'];
    	$_extract = ['id', 'customer.email', 'product.sku_code', 'title', 'description', 'rating', 'location_ip', 'created', 'modified', 'is_active'];
    	$this->set(compact('data', '_serialize', '_header', '_extract'));
    	$this->viewBuilder()->setClassName('CsvView.Csv');
    	return;
    }
    
    public function add($key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5('reviews') ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
        $review = $this->Reviews->newEntity();
        if ($this->request->is('post')) {
            $review = $this->Reviews->patchEntity($review, $this->request->getData());
            $error = $review->getErrors(); 
			if( empty($error) ){
				$review->location_ip = $this->request->clientIp();
				if ($this->Reviews->save($review)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'index']);
				}
				$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
		
        $this->set(compact('review', 'error'));
        $this->set('_serialize', ['review','error']);
    }
    
    public function edit($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
        $review = $this->Reviews->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $review = $this->Reviews->patchEntity($review, $this->request->getData());
			$error = $review->getErrors();
			if( empty($error) ){
				if( $review->offer && ($review->is_active == 'approved') ){
					$id_customer			= $review->customer_id;
					$transaction_type		= 1;
					$id_referrered_customer	= 0;
					$id_order				= 0;
					$pb_cash				= 0;
					$pb_points				= 50;
					$voucher_amount			= 0;
					$comments				= "Wallet credited for placed review to product #".$review->product_id;
					$transaction_ip			= $_SERVER['REMOTE_ADDR'];
					$this->Store->logPBWallet($id_customer, $transaction_type, $id_referrered_customer, $id_order, $pb_cash, $pb_points, $voucher_amount, $comments, $transaction_ip);
					$review->offer = 0;
				}
				if ($this->Reviews->save($review)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'index']);
				}
				$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
			}
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
        }
		$products = $customers = [];
		$Table = TableRegistry::get('Customers');
        $customers = $Table->get($review->customer_id, ['fields'=>['id', 'email', 'firstname', 'lastname']])->toArray();		

		$Table = TableRegistry::get('Products');
        $products = $Table->get($review->product_id, ['fields' =>['id', 'name', 'title','sku_code']])->toArray();
		//pr($products); die;
        $this->set(compact('review', 'error', 'customers', 'products', 'id'));
        $this->set('_serialize', ['review','error', 'customers', 'products', 'id']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $review = $this->Reviews->get($id);
        if ($this->Reviews->delete($review)) {
            $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
        } else {
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			return $this->redirect(['action'=>'edit', $id, 'key', md5($id)]);
        }
        return $this->redirect(['action'=>'index']);
    }
}
