<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Connection;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use Cake\Routing\Router;

class ProductsController extends AppController
{
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Product');
        $this->loadComponent('Facebook');
    }
    
	public function index()
    {   
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('perPage', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$filterData = []; //debug(); die;
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['Products.id'] = $id; }
		
		$skuCode = $this->request->getQuery('sku_code', '');
		$this->set('skuCode', $skuCode);
		if(!empty($skuCode)) { $filterData['Products.sku_code'] = $skuCode; }
		
		$brandId = $this->request->getQuery('brand_id', '');
		$this->set('brandId', $brandId);
		if(!empty($brandId)) { $filterData['Products.brand_id'] = $brandId; }
		
		$name = $this->request->getQuery('name', '');
		$this->set('name', $name);
		if(!empty($name)) { $filterData['Products.name'] = $name; }
		
		$url = $this->request->getQuery('url_key', '');
		$this->set('urlKey', $url);
		if(!empty($url)) { $filterData['Products.url_key'] = $url; }
		
		$size = $this->request->getQuery('size', '');
		$this->set('size', $size);
		if(!empty($size)) { $filterData['Products.size'] = $size; }
		
		$sizeUnit = $this->request->getQuery('size_unit', '');
		$this->set('sizeUnit', $sizeUnit);
		if(!empty($sizeUnit)) { $filterData['Products.size_unit'] = $sizeUnit; }
		
		$qty = $this->request->getQuery('qty', '');
		$this->set('qty', $qty);
		if(!empty($qty)) { $filterData['Products.qty'] = $qty; }
		
		$stock = $this->request->getQuery('stock', '');
		$this->set('stock', $stock);
		if(!empty($stock)) { $filterData['Products.is_stock'] = $stock; }
		
		$gender = $this->request->getQuery('gender', '');
		$this->set('gender', $gender);
		if(!empty($gender)) { $filterData['Products.gender'] = $gender; }
		
		$price = $this->request->getQuery('price', '');
		$this->set('price', $price);
		if(!empty($price)) { $filterData['Products.price'] = $price; }
		
		$offerPrice = $this->request->getQuery('offer_price', '');
		$this->set('offerPrice', $offerPrice);
		if(!empty($offerPrice)) { $filterData['offer_price'] = $offerPrice; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$created%";
		}
		
		$isStock = $this->request->getQuery('is_stock', '');
		if( $isStock !== '' ) { $filterData['Products.is_stock'] = $isStock; }
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['Products.is_active'] = $isActive; }
		
		$query = $this->Products->find('all', ['contain'=>['Brands','ProductsCategories'],'conditions'=>$filterData]);
		if(empty($isStock)){
			$query = $query->order(['created' => 'DESC']);
		}else{
			$query = $query->order(['modified' => 'DESC']);
		}
		//pr($query->toArray());die;
		$categoryId = $this->request->getQuery('category_id', '1');
		$this->set('categoryId', $categoryId);
		if($categoryId > 1){
			$catFilterList = [$categoryId];
			$query = $query->matching('ProductsCategories', function ($q) use ($catFilterList) {
					return $q->where(['ProductsCategories.category_id IN ' => $catFilterList]);
				});
		}
		$products= $this->paginate($query)->toArray();
		$brandsList = TableRegistry::get('Brands')->find('list', ['conditions' => ['Brands.is_active'=>'active'], 'order' => ['Brands.title'=>'ASC']])->toArray();
		$cateList = TableRegistry::get('Categories')->find('treeList', ['spacer' => '- '])->toArray();
		//pr($products);
		//die;
        $this->set(compact('products','brandsList','cateList'));
        $this->set('_serialize', ['products','brandsList','cateList']);
    }

    public function exports()
    {
    	$this->response->withDownload('exports.csv');
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('perPage', 50);
		$offset = $this->request->getQuery('page', 1);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
				'offset'=>($offset - 1)*$limit
		];
		$filterData = [];
		$id = $this->request->getQuery('id', '');
		$this->set('id', $id);
		if(!empty($id)) { $filterData['id'] = $id; }
		
		$skuCode = $this->request->getQuery('sku_code', '');
		$this->set('skuCode', $skuCode);
		if(!empty($skuCode)) { $filterData['sku_code'] = $skuCode; }
		
		$brandId = $this->request->getQuery('brand_id', '');
		$this->set('brandId', $brandId);
		if(!empty($brandId)) { $filterData['brand_id'] = $brandId; }
		
		$title = $this->request->getQuery('title', '');
		$this->set('title', $title);
		if(!empty($title)) { $filterData['title'] = $title; }
		
		$url = $this->request->getQuery('url_key', '');
		$this->set('urlKey', $url);
		if(!empty($url)) { $filterData['url_key'] = $url; }
		
		$size = $this->request->getQuery('size', '');
		$this->set('size', $size);
		if(!empty($size)) { $filterData['size'] = $size; }
		
		$sizeUnit = $this->request->getQuery('size_unit', '');
		$this->set('sizeUnit', $sizeUnit);
		if(!empty($sizeUnit)) { $filterData['size_unit'] = $sizeUnit; }
		
		$qty = $this->request->getQuery('qty', '');
		$this->set('qty', $qty);
		if(!empty($qty)) { $filterData['qty'] = $qty; }
		
		$gender = $this->request->getQuery('gender', '');
		$this->set('gender', $gender);
		if(!empty($gender)) { $filterData['gender'] = $gender; }
		
		$stock = $this->request->getQuery('is_stock', '');
		$this->set('stock', $stock);
		if(!empty($stock)) { $filterData['is_stock'] = $stock; }
		
		$price = $this->request->getQuery('price', '');
		$this->set('price', $price);
		if(!empty($price)) { $filterData['price'] = $price; }
		
		$offerPrice = $this->request->getQuery('offer_price', '');
		$this->set('offerPrice', $offerPrice);
		if(!empty($offerPrice)) { $filterData['offer_price'] = $offerPrice; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$created%";
		}
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['Products.is_active'] = $isActive; }
		$data = $this->Products->find('all', ['contain'=>['Brands'],'conditions'=>$filterData])
				->contain([
					'ProductsCategories.Categories' => [
						'queryBuilder' => function ($q) {
							return $q->select(['name']);
						}
					]
				])
				->order(['created' => 'DESC']);
		
		$categoryId = $this->request->getQuery('category_id', '1');
		$this->set('categoryId', $categoryId);
		if($categoryId > 1){
			$catFilterList = [$categoryId];
			$data = $data->matching('ProductsCategories', function ($q) use ($catFilterList) {
					return $q->where(['ProductsCategories.category_id IN ' => $catFilterList]);
				});
		}
		$data = $data->hydrate(0)->toArray();
		$dataList = []; 
		foreach($data as $value){
			$category = '';
			foreach( $value['products_categories'] as $cat ){
				$category = $cat['category']['name'].', ';
			}
			$dataList[] = [
				'id' => $value['id'],
				'sku_code' => $value['sku_code'],
				'title' => $value['title'],
				'size' => $value['size'],
				'unit' => $value['size_unit'],
				'dead_weight' => $value['dead_weight'],
				'box_weight' => $value['box_weight'],
				'quantity' => $value['qty'],
				'categories' => substr($category, 0, -2),
				'brand' => $value['brand']['title'],
				'goods_tax' => $value['goods_tax'],
				'price' => $value['price'],
				'stock' => $value['is_stock'],
				'status' => $value['is_active'],
				'created' => $value['created'],
				'modified' => $value['modified'],
				'short_description' => strip_tags($value['short_description']),
				'description' => strip_tags($value['description'])
			];
		}
    	$_serialize='dataList';
    	$_header = ['ID', 'SKU Code', 'Title', 'Size', 'Size Unit', 'Dead Weight', 'Box Weight', 'Quantity', 'Categories', 'Brand ', 'GST Tax', 'Price', 'Stock', 'Status', 'Created Date', 'Modified Date','Short Description','Description'];
    	$_extract = ['id', 'sku_code', 'title', 'size', 'unit', 'dead_weight', 'box_weight', 'quantity', 'categories', 'brand', 'goods_tax', 'price', 'stock', 'status', 'created', 'modified','short_description','description'];
    	$this->set(compact('dataList', '_serialize', '_header', '_extract'));		
    	$this->viewBuilder()->setClassName('CsvView.Csv');
    	return;
    }

    public function soldProduct()
    {
    	$this->response->withDownload('exports.csv');
		$query = $this->Products->find('all', ['fields'=>['id', 'sku_code', 'title']])
			->contain([
				'OrderDetails' => [
					'queryBuilder' => function ($q) {
						return $q->select(['product_id', 'quantity' => 'sum(qty)' ])->group(['product_id']);
					}
				]
			])
			->order(['title' => 'ASC'])->hydrate(0)->toArray();
		$query = array_map(function($value){
				$value['quantity'] = $value['order_details'][0]['quantity'] ?? 0;
				unset($value['order_details']);
			return $value;
		}, $query);
		//pr($query); die;
    	$_serialize='query';
    	$_header = ['ID', 'SKU Code', 'Title', 'Quantity'];
    	$_extract = ['id', 'sku_code', 'title', 'quantity'];
    	$this->set(compact('query', '_serialize', '_header', '_extract'));		
    	$this->viewBuilder()->setClassName('CsvView.Csv');
    	return;
    }

    public function add($key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5('products') ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
        $product = $this->Products->newEntity();
        if ($this->request->is('post')) {
            $product = $this->Products->patchEntity($product, $this->request->getData());
			$error = $product->getErrors();
			$newKey = $this->request->getData('Products.url_key');
			if( $this->Product->findUrlRewrite($newKey) ){
				$error['url_key']['urlKey']  = 'This url key already exists in system!';
			}
			if( empty($error) ){
				$newIds = $this->request->getData('Products.family_ids');
				$newIds = ( is_array($newIds) && !empty($newIds) ) ? implode(',', $newIds) : NULL;
				$product->family_ids = $newIds;
				if ($this->Products->save($product)) {
					$this->Product->updateUrlRewrite(['old_key'=>'','new_key'=>$newKey,'type'=>'product']);
					$this->Facebook->addItem($product->id);
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'edit', $product->id, 'key', md5($product->id)]);
				}
				$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
		$brands = TableRegistry::get('Brands')->find('list', ['conditions' => ['Brands.is_active'=>'active'], 'order' => ['Brands.title'=>'ASC'] ]);
		$families = TableRegistry::get('Families')->find('list', ['conditions' => ['Families.is_active'=>'active'], 'order' => ['Families.title'=>'ASC'] ]);
        $this->set(compact('product', 'brands','families','error'));
        $this->set('_serialize', ['product', 'brands', 'families', 'error']);
    }
	
    public function edit($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
        $product = $this->Products->get($id, [
            'contain' => []
		]);
        if ($this->request->is(['patch', 'post', 'put'])) {
			$oldStatus =  $product->is_stock;
			$oldKey = $product->url_key ?? '';
			$newKey = $this->request->getData('Products.url_key');
			$product = $this->Products->patchEntity($product, $this->request->getData());
			$error = $product->getErrors();
			if( ($oldKey != $newKey) && $this->Product->findUrlRewrite($newKey) ){
				$error['url_key']['urlKey']  = 'This url key already exists in system!';
			}
			if( empty($error) ){
				$newIds = $this->request->getData('Products.family_ids');
				$newIds = ( is_array($newIds) && !empty($newIds) ) ? implode(',', $newIds) : NULL;
				$product->family_ids = $newIds;
				//pr($product->is_stock);die;
				if ($this->Products->save($product)) {
					$this->Product->updateUrlRewrite(['old_key'=>$oldKey,'new_key'=>$newKey,'type'=>'product']);
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					if( $product->is_active == 'active' ){
						$this->Facebook->addItem($product->id);
					}else{
						$this->Facebook->removeItem($product->sku_code);
					}
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
		$brands = TableRegistry::get('Brands')->find('list', ['conditions' => ['Brands.is_active'=>'active'], 'order' => ['Brands.title'=>'ASC'] ]);
		$families = TableRegistry::get('Families')->find('list', ['conditions' => ['Families.is_active'=>'active'], 'order' => ['Families.title'=>'ASC'] ]);
        $this->set(compact('product','id','brands','families','error'));
        $this->set('_serialize', ['product','id','brands','families','error']);
    }

    public function categories($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		$error = [];
        if ($this->request->is(['patch', 'post', 'put'])) {
			$insertRecord = [];
			$ProductsCategories = TableRegistry::get('ProductsCategories');
			
            $newData = $this->request->getData('chkd_ids');
			if(!empty($newData)){
				$newData = explode(",", $newData); 
				foreach($newData as $value){
					$insertRecord[] = ['product_id'=>$id, 'category_id'=>$value];
				}
			}
			if( is_array($insertRecord) && !empty($insertRecord) ){
				$insertRecord = $ProductsCategories->newEntities($insertRecord);
			}
			//pr($insertRecord);die;
			if( !empty($newData) ){
				$ProductsCategories->deleteAll(['ProductsCategories.product_id'=>$id], true); //delete all pre selected category
				if( $ProductsCategories->saveMany($insertRecord) ){
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
			}
        }
		
		//$temp = TableRegistry::get('Products_Categories')->find('All',['where product_id = '=>$id])->toArray();
		//pr($temp);
		$product = $this->Products->get($id, [
            'contain' => 'ProductsCategories'
        ]);
		$categoriesIds = $product;
		$categoriesIds = $categoriesIds->toArray();
		$categoriesIds = (new Collection($categoriesIds['products_categories']))->extract('category_id')->toArray();
		$cateTree = TableRegistry::get('Categories')->find('threaded',['order'=>'Categories.lft'])->toArray();
		
        $this->set(compact('id','cateTree','categoriesIds','error'));
        $this->set('_serialize', ['id','cateTree','categoriesIds','error']);
    }

    public function images($id=null, $key=null, $md5=null, $imgId=0)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		$image = [
				'id'=>0,
				'largeImage'=>'',
				'baseImage'=>'',
				'smallImage'=>'',
				'thumbImage'=>'',
				'popupImage'=>'',
				'titleImage'=>'',
				'altContent'=>'',
		];		
		if($imgId > 0){
			$query = TableRegistry::get('ProductsImages')->get($imgId)->toArray();
			$image = [
				'id'=>$query['id'],
				'largeImage'=>$query['img_large'],
				'baseImage'=>$query['img_base'],
				'smallImage'=>$query['img_small'],
				'thumbImage'=>$query['img_thumbnail'],
				'popupImage'=>$query['img_popup'],
				'titleImage'=>$query['title'],
				'altContent'=>$query['alt_text'],
			];
		}
		$images = $this->Product->getProductImages($id);
        $this->set(compact('image','id','images','imgId'));
        $this->set('_serialize', ['image','id','images','imgId']);
    }
	
	public function updateImages(){
		$status = false;
		$msg = '';
		$data = [];
		if($this->request->is('ajax')){
			$tableData = TableRegistry::get('ProductsImages');
			$ids = $this->request->getData('ids');
			$order = $this->request->getData('order');
			$base = $this->request->getData('base');
			$small = $this->request->getData('small');
			$thumbnail = $this->request->getData('thumbnail');
			$large = $this->request->getData('large');
			$exclude = $this->request->getData('exclude');
			$status = $this->request->getData('status');
			$remove = $this->request->getData('remove');
			for($i=0; $i < count($ids); $i++){
				$id = $ids[$i];
				$query = $tableData->get($id);
				if( in_array($id, $remove) ){
					$tableData->delete($query);
				}else{
					$query->img_order = $order[$i];
					$query->is_base = in_array($id, $base) ? 1:0;
					$query->is_small = in_array($id, $small) ? 1:0;
					$query->is_thumbnail = in_array($id,$thumbnail) ? 1:0;
					$query->is_large = in_array($id, $large) ? 1:0;
					$query->exclude = in_array($id, $exclude) ? 1:0;
					$query->is_active = $status[$i];
					$tableData->save($query);
				}
			}			
		}else{
			$msg = 'Invalid request!';
		}
		echo json_encode(['message'=>$msg,'status'=>$status,'data'=>$data]);
		die;
	}

	public function saveImages(){
		$status = false;
		$msg = '';
		$data = [];
		if($this->request->is('ajax')){
			$largeImage = $this->request->getData('largeImage');
			$baseImage = $this->request->getData('baseImage');
			$smallImage = $this->request->getData('smallImage');
			$thumbImage = $this->request->getData('thumbImage');
			$popupImage = $this->request->getData('popupImage');
			$titleImage = $this->request->getData('titleImage');
			$altContent = $this->request->getData('altContent');
			$productId = $this->request->getData('productId');
			$id = $this->request->getData('id');
			if( !empty($baseImage) && ($productId > 0) ){
				$dataTable = TableRegistry::get('ProductsImages');
				$qry = ( $id > 0 ) ?  $dataTable->get($id) : $dataTable->newEntity();
				$qry->product_id = $productId;
				$qry->title = $titleImage;
				$qry->alt_text = $altContent;
				$qry->img_large = $largeImage;
				$qry->img_base = $baseImage;
				$qry->img_small = $smallImage;
				$qry->img_thumbnail = $thumbImage;
				$qry->img_popup = $popupImage;
				if( $dataTable->save($qry) ){
					$msg = 'Links Saved!';
					$status = true;
				}else{
					$msg = 'Sry, Please try again!';
				}
			}else{
				$msg = 'Please enter at least base image link!';
			}
		}else{
			$msg = 'Invalid request!';
		}
		echo json_encode(['message'=>$msg,'status'=>$status,'data'=>$data]);
		die;
	}

    public function relatedProducts($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$filterData = [];
		$skuCode = $this->request->getQuery('sku_code', '');
		$this->set('skuCode', $skuCode);
		if(!empty($skuCode)) { $filterData['sku_code'] = $skuCode; }
		
		$title = $this->request->getQuery('title', '');
		$this->set('title', $title);
		if(!empty($title)) { $filterData['title'] = $title; }
		
		$price = $this->request->getQuery('price', '');
		$this->set('price', $price);
		if(!empty($price)) { $filterData['price'] = $price; }
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['is_active'] = $isActive; }
		
		$error = [];
		$relatedIds = [];
		$product = $this->Products
			->find('all')
			->select(['title', 'related_ids'])
			->where(['id =' => $id])
			->toArray();
			
		//pr($filterData);
		$relatedIds = !empty($product[0]->related_ids) ? explode(',', $product[0]->related_ids) : [];
		if( empty($filterData) ){
			$filterData['id IN '] = !empty($product[0]->related_ids) ? explode(',', $product[0]->related_ids) : [0];			
		}else if( !empty($relatedIds) ){
			//$filterData['id IN '] = !empty($product[0]->related_ids) ? explode(',', $product[0]->related_ids) : [0];			
		}
				
		$filterData['id !='] = $id;
		
		$query = $this->Products->find('all', ['conditions'=>$filterData])->order(['created' => 'DESC']);
		$products = $this->paginate($query);
        $this->set(compact('products', 'id', 'relatedIds', 'error'));
        $this->set('_serialize', ['products', 'id', 'relatedIds', 'error']);
    }

	public function saverelated()
	{
		//$this->RequestHandler->config('inputTypeMap.json', ['json_decode', true]);
		//$data = $this->request->input('json_decode');
		//$postdata = file_get_contents("php://input");
		//$req = json_decode($postdata, true);
		$this->autoRender = false;
		$status = false; $message = '';
		//$this->response->withHeader('Content-Type', 'application/json; charset=UTF-8');
		if( $this->request->is(['ajax']) ){
			$id = $this->request->getData('currentId');
			$currentAllIds = $this->request->getData('currentAllIds');
			$currentChkIds = $this->request->getData('currentChkIds');
			
			$product = $this->Products
				->find('all')
				->select(['title', 'related_ids'])
				->where(['id =' => $id])
				->toArray();			
			$relatedIds = !empty($product[0]->related_ids) ? explode(',', $product[0]->related_ids) : [];			
			$saveRelatedProduct = [];
			if( !empty($relatedIds) ){
				//find all unchecked values for product;
				$currentUnChkIds = array_diff($currentAllIds, $currentChkIds); //$currentAllIds - $currentChkIds
				$currentUnChkIds = array_values(array_unique($currentUnChkIds));
				
				//Remove all unchecked values from saved database
				$tempIds = array_diff($relatedIds, $currentUnChkIds); //$relatedIds - $currentUnChkIds
				$tempIds = array_values(array_unique($tempIds));
				
				//merge saved and current checked ids
				$saveRelatedProduct = array_merge($tempIds, $currentChkIds);
				$saveRelatedProduct = array_values(array_unique($saveRelatedProduct));				
			}else{
				$saveRelatedProduct = $currentChkIds;
			}
			
			$productTable = TableRegistry::get('Products');
			$updateProduct = $productTable->get($id);			
			$updateProduct->related_ids = !empty($saveRelatedProduct) ? implode(',',$saveRelatedProduct):NULL;
			if($productTable->save($updateProduct)){
				$status = true;				
			}else{
				$message = "Sorry, Record not saved!";
			}
		}else{
			$message = 'This is invalid request!';
		}
		//$this->response->body();
		//return $this->response;
		echo json_encode(array('flag'=>$status,'message'=>$message));		
		die;
	}
	
    public function productReviews($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$filterData = [];
		$filterData['product_id'] = $id;
		
		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if(!empty($email)) { $filterData['Customers.email'] = $email; }
		
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
		
		$query = TableRegistry::get('Reviews')->find('all', ['contain'=>['Customers'], 'fields'=>['Customers.email','Reviews.id','Reviews.title','Reviews.description','Reviews.rating','Reviews.location_ip','Reviews.created','Reviews.is_active'],'conditions'=>$filterData])->order(['Reviews.created' => 'DESC']);
		//debug($query);
		$reviews = $this->paginate($query);
		
        $this->set(compact('reviews','id'));
        $this->set('_serialize', ['reviews','id']);
    }

    public function productNotes($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$tables = TableRegistry::get('ProductsNotes');		
		$error = [];
        if ($this->request->is(['post'])) {
            $getData = $tables->patchEntity($tables->newEntity(), $this->request->getData());
			$error = $getData->getErrors();
			if( empty($error) ){
				$getData->product_id = $id;
				if ($tables->save($getData)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
		$addnotes = $tables;
		
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$query = TableRegistry::get('ProductsNotes')->find('all', ['fields'=>['id','title','description','is_active'],'conditions'=>['product_id'=>$id]])->order(['id' => 'DESC']);
		$notes = $this->paginate($query);
		
        $this->set(compact('addnotes','notes','id'));
        $this->set('_serialize', ['addnotes','notes','id']);
    }

    public function noteDelete($id = null,$productId)
    {
        $this->request->allowMethod(['post', 'delete']);
		$tables = TableRegistry::get('ProductsNotes');	
        $note = $tables->get($id);
        if ($tables->delete($note)) {			
            $this->Flash->success(__('The record has been deleted!'), ['key' => 'adminSuccess']);
			return $this->redirect(['action' => 'product-notes',$productId,'key',md5($productId)]);
        } else {
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
        }
        return true;
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
			$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
			TableRegistry::get('Cms')->query()->delete()->where(['request_path'=>$product->url_key])->execute();
			return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
        }
        return true;
    }
	
	public function beforeFilter(Event $event)
	{
		$actions = ['saverelated','updateImages'];

		if (in_array($this->request->params['action'], $actions)) {
			// for csrf
			//$this->eventManager()->off($this->Csrf);
			// for security component
			$this->Security->config('unlockedActions', $actions);
		}
	}
}
