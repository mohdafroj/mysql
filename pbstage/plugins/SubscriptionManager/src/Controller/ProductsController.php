<?php
namespace SubscriptionManager\Controller;

use SubscriptionManager\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;

class ProductsController extends AppController
{
	private $conn = null;
	public function initialize()
    {
        parent::initialize();
		$this->loadComponent('SubscriptionManager.Product');
    }
    
	public function index()
    {   
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('perPage', 50);
		$this->paginate = [
			//'SubscriptionManager.Products'=>[
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
			//]
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
		
		$url = $this->request->getQuery('url_key', '');
		$this->set('url', $url);
		if(!empty($url)) { $filterData['Products.url_key'] = $url; }
		
		$size = $this->request->getQuery('size', '');
		$this->set('size', $size);
		if(!empty($size)) { $filterData['Products.size'] = $size; }
		
		$sizeUnit = $this->request->getQuery('unit', '');
		$this->set('sizeUnit', $sizeUnit);
		if(!empty($sizeUnit)) { $filterData['Products.unit'] = $sizeUnit; }
		
		$quantity = $this->request->getQuery('quantity', '');
		$this->set('quantity', $quantity);
		if(!empty($quantity)) { $filterData['Products.quantity'] = $quantity; }
		
		$gender = $this->request->getQuery('gender', '');
		$this->set('gender', $gender);
		if(!empty($gender)) { $filterData['Products.gender'] = $gender; }
		
		$price = $this->request->getQuery('price', '');
		$this->set('price', $price);
		
		$discount = $this->request->getQuery('discount', '');
		$this->set('discount', $discount);
		if(!empty($discount)) { $filterData['Products.discount'] = $discount; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['Products.created LIKE'] = "$created%";
		}
		
		$categoryId = $this->request->getQuery('category_id', '1');
		$this->set('categoryId', $categoryId);

		$isStock = $this->request->getQuery('is_stock', '');
		if( $isStock !== '' ) { $filterData['Products.is_stock'] = $isStock; }
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['Products.is_active'] = $isActive; }
		$priceWh = [];
		if( !empty($name) ){ $priceWh['ProductPrices.name LIKE '] = "%$name%"; }
		if( !empty($price) ){ $priceWh['ProductPrices.price'] = $price; }
		$orderTable = TableRegistry::get('SubscriptionManager.Products');
		$products = $orderTable->find('all', ['fields'=>['id','sku_code','url_key','size','discount','unit','quantity','gender','is_active'],'order'=>['Products.id'=>'DESC'],'conditions'=>$filterData])
			->contain([
				'Brands'=>[
					'fields'=>['id','title']
				],
				'ProductPrices'=>[
					'queryBuilder'=>function($q){
						return $q->select(['id','name','price','product_id','location_id']);
					}
				],
				'ProductPrices.Locations'=>[
					'fields'=>['id','title','currency_logo','code']
				],
				'ProductCategories'=>[
					'fields'=>['category_id','product_id']
				]
			]);
		if( !empty($priceWh) ){
			$products = $products->matching('ProductPrices', function ($q) use ($priceWh) {
				return $q->where($priceWh);
			});
		}
		if( $categoryId > 1 ){
			$products = $products->matching('ProductCategories', function ($q) use ($categoryId) {
				return $q->where(['category_id'=>$categoryId]);
			});
		}
		$products = $this->paginate($products)->toArray(); //pr($products[0]);
		$brandsList = TableRegistry::get('SubscriptionManager.Brands')->find('list', ['conditions' => ['is_active'=>'active'], 'order' => ['title'=>'ASC']])->toArray();
		$cateList = TableRegistry::get('SubscriptionManager.Categories')->find('treeList', ['spacer' => '- '])->toArray();
		//pr($products);die;
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
		
		$url = $this->request->getQuery('url_key', '');
		$this->set('url', $url);
		if(!empty($url)) { $filterData['Products.url_key'] = $url; }
		
		$size = $this->request->getQuery('size', '');
		$this->set('size', $size);
		if(!empty($size)) { $filterData['Products.size'] = $size; }
		
		$sizeUnit = $this->request->getQuery('unit', '');
		$this->set('sizeUnit', $sizeUnit);
		if(!empty($sizeUnit)) { $filterData['Products.unit'] = $sizeUnit; }
		
		$quantity = $this->request->getQuery('quantity', '');
		$this->set('quantity', $quantity);
		if(!empty($quantity)) { $filterData['Products.quantity'] = $quantity; }
		
		$gender = $this->request->getQuery('gender', '');
		$this->set('gender', $gender);
		if(!empty($gender)) { $filterData['Products.gender'] = $gender; }
		
		$price = $this->request->getQuery('price', '');
		$this->set('price', $price);
		
		$discount = $this->request->getQuery('discount', '');
		$this->set('discount', $discount);
		if(!empty($discount)) { $filterData['Products.discount'] = $discount; }
		
		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if(!empty($created))
		{
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['Products.created LIKE'] = "$created%";
		}
		
		$categoryId = $this->request->getQuery('category_id', '1');
		$this->set('categoryId', $categoryId);

		$isStock = $this->request->getQuery('is_stock', '');
		if( $isStock !== '' ) { $filterData['Products.is_stock'] = $isStock; }
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['Products.is_active'] = $isActive; }
		$priceWh = [];
		if( !empty($name) ){ $priceWh['ProductPrices.name LIKE '] = "%$name%"; }
		if( !empty($price) ){ $priceWh['ProductPrices.price'] = $price; }
		$productTable = TableRegistry::get('SubscriptionManager.Products');
		$products = $productTable->find('all', ['fields'=>['id','sku_code','url_key','size','discount','unit','quantity','gender','is_active','created','modified'],'order'=>['Products.id'=>'DESC'],'conditions'=>$filterData]);
		if( !empty($priceWh) ){
			$products = $products->matching('ProductPrices', function ($q) use ($priceWh) {
				return $q->where($priceWh);
			});
		}
		if( $categoryId > 1 ){
			$products = $products->matching('ProductCategories', function ($q) use ($categoryId) {
				return $q->where(['category_id'=>$categoryId]);
			});
		}
		$products = $products->contain([
				'Brands'=>[
					'fields'=>['id','title']
				],
				'ProductPrices'=>[
					'queryBuilder'=>function($q){
						return $q->select(['id','name','title','cross','price','price1','price2','price3','short_description','description','product_id','location_id']);
					}
				],
				'ProductPrices.Locations'=>[
					'fields'=>['id','title','currency_logo','code']
				],
				'ProductCategories'=>[
					'fields'=>['category_id','product_id']
				]
			])->hydrate(false)->toArray(); //pr($products); die;
		$dataList = []; $header = $extract = ''; $i = 0;
		foreach($products as $value){
			$variants = $value['product_prices'];
			if (!empty($variants)) {
				$dataList[$i] = $value;
				$emptyRow = false;
				foreach ($variants as $variant) {
                    $dataList[$i]['id'] = $value['id'];
                    $dataList[$i]['sku_code'] = $value['sku_code'];
					$dataList[$i]['name'] = $variant['name'];
					$dataList[$i]['title'] = $variant['title'];
                    $dataList[$i]['size'] = $value['size'];
                    $dataList[$i]['unit'] = $value['unit'];
                    $dataList[$i]['quantity'] = $value['quantity'];
                    $dataList[$i]['brand'] = $value['brand']['title'];
                    $dataList[$i]['currency'] = $variant['location']['code'] ?? '';
                    $dataList[$i]['cross'] = $variant['cross'];
                    $dataList[$i]['price'] = $variant['price'];
                    $dataList[$i]['price1'] = $variant['price1'];
                    $dataList[$i]['price2'] = $variant['price2'];
                    $dataList[$i]['price3'] = $variant['price3'];
                    $dataList[$i]['short_description'] = $variant['short_description'];
                    $dataList[$i]['description'] = $variant['description'];
                    $dataList[$i]['created'] = $value['created'];
                    $dataList[$i]['modified'] = $value['modified'];
                    $dataList[$i]['status'] = $value['is_active'];
                    if ($emptyRow) {
						$dataList[$i]['id'] = '';
						$dataList[$i]['sku_code'] = '';	
						$dataList[$i]['size'] = '';
						$dataList[$i]['unit'] = '';
						$dataList[$i]['quantity'] = '';
						$dataList[$i]['brand'] = '';
						$dataList[$i]['created'] = '';
						$dataList[$i]['modified'] = '';
						$dataList[$i]['status'] = '';
                    }
                    $emptyRow = true;
                    $i++;
                }
			}
		}
    	$_serialize='dataList';
    	$_header = ['ID', 'SKU Code', 'name', 'Title', 'Size', 'Unit', 'Quantity', 'Brand ', 'Currency','Cross','Price','Price1','Price2','Price3','Short Description','Description', 'Created', 'Modified','Status'];
    	$_extract = ['id', 'sku_code', 'name', 'title', 'size', 'unit', 'quantity', 'brand', 'currency','cross','price','price1','price2','price3','short_description','description', 'created', 'modified','status'];
    	$this->set(compact('dataList', '_serialize', '_header', '_extract'));		
    	$this->viewBuilder()->setClassName('CsvView.Csv');
    	return;
    }

    public function add($key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5('products') ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
		$productTable = TableRegistry::get('SubscriptionManager.Products');
        $product = $productTable->newEntity();
        if ($this->request->is('post')) {
            $product = $productTable->patchEntity($product, $this->request->getData());
			$error = $product->getErrors();
			if( empty($error) ){
				$newIds = $this->request->getData('Products.family_ids');
				$newIds = ( is_array($newIds) && !empty($newIds) ) ? implode(',', $newIds) : NULL;
				$product->family_ids = $newIds;
				if ($productTable->save($product)) {
					//$this->Facebook->addItem($product->id);
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['action' => 'edit', $product->id, 'key', md5($product->id)]);
				}
				$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
		$brands = TableRegistry::get('SubscriptionManager.Brands')->find('list', ['conditions' => ['is_active'=>'active'], 'order' => ['title'=>'ASC'] ]);
		$families = TableRegistry::get('SubscriptionManager.Families')->find('list', ['conditions' => ['is_active'=>'active'], 'order' => ['title'=>'ASC'] ]);
        $this->set(compact('product', 'brands','families','error'));
        $this->set('_serialize', ['product', 'brands', 'families', 'error']);
    }
	
    public function edit($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
		$productTable = TableRegistry::get('SubscriptionManager.Products');
        $product = $productTable->get($id, [
            'contain' => []
		]);
        if ($this->request->is(['post', 'put'])) {
			$oldStatus =  $product->is_stock;
			$product = $productTable->patchEntity($product, $this->request->getData());
			$error = $product->getErrors();
			if( empty($error) ){
				$newIds = $this->request->getData('Products.family_ids');
				$newIds = ( is_array($newIds) && !empty($newIds) ) ? implode(',', $newIds) : NULL;
				$product->family_ids = $newIds;
				if ($productTable->save($product)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        }
		$brands = TableRegistry::get('SubscriptionManager.Brands')->find('list', ['conditions' => ['is_active'=>'active'], 'order'=>['title'=>'ASC'] ]);
		$families = TableRegistry::get('SubscriptionManager.Families')->find('list', ['conditions' => ['is_active'=>'active'], 'order'=>['title'=>'ASC'] ]);
        $this->set(compact('product','id','brands','families','error'));
        $this->set('_serialize', ['product','id','brands','families','error']);
    }

    public function prices($id=null, $key=null, $md5=null, $priceId=0)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		
		$error = [];
		$productPricesTable = TableRegistry::get('SubscriptionManager.ProductPrices');
        $product = ($priceId > 0) ? $productPricesTable->get($priceId) : $productPricesTable->newEntity();
        if ($this->request->is(['post'])) {
			$oldStatus =  $product->is_stock;
			$product = $productPricesTable->patchEntity($product, $this->request->getData());
			$error = $product->getErrors();
			if( empty($error) ){
				$checkList = $productPricesTable->find('all',['fields'=>['id'],'conditions'=>['product_id'=>$id,'location_id'=>$this->request->getData('location_id')]])->hydrate(false)->toArray();
				//pr($checkList);
				if( count($checkList) > 0 ){
					$this->Flash->error(__('Sorry, you already added record for selected Country!'), ['key' => 'adminError']);
				}else{
					if ($productPricesTable->save($product)) {
						$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
						$this->redirect(['action'=>'prices', $id, $key, $md5]);
					}else{
						$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
					}
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}else if( $this->request->is(['put']) ){
			$oldStatus =  $product->is_stock;
			$product = $productPricesTable->patchEntity($product, $this->request->getData());
			$error = $product->getErrors();
			if( empty($error) ){
				if ($productPricesTable->save($product)) {
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
					$this->redirect(['action'=>'prices', $id, $key, $md5]);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}else if( $this->request->is(['delete']) ){
			if( 3 > 1 ){ //if( $productPricesTable->delete($product) ){
				$this->Flash->success(__('The record has been deleted!'), ['key' => 'adminSuccess']);
				$this->redirect(['action'=>'prices', $id, $key, $md5]);
			}else{
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}

		$locations = TableRegistry::get('SubscriptionManager.Locations')->find('all',['fields'=>['id','title'],'order'=>['title'=>'ASC']])->hydrate(false)->toArray();
		$locations = array_combine(array_column($locations,'id'),array_column($locations,'title'));
		$productList = $productPricesTable->find('all',['contain'=>['Products','Locations'],'fields'=>['id','title','name','cross','price','price1','price2','price3','created','modified','is_active'],'conditions'=>['product_id'=>$id],'order'=>['ProductPrices.title'=>'ASC']])
					   ->contain([
						   'Products'=>[
							'queryBuilder' => function ($q) {
								return $q->select(['id', 'sku_code']);
							}
					   	],
						   'Locations' => [
							'queryBuilder' => function ($q) {
								return $q->select(['id', 'title', 'code','currency', 'currency_logo']);
							}
						]
					   ])
					   ->toArray();
		//pr($error);
		$this->set(compact('product','productList','id','locations','error'));
        $this->set('_serialize', ['product','productList','id','locations','error']);
    }

    public function categories($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		$error = [];
		$productCategories = TableRegistry::get('SubscriptionManager.ProductCategories');
        if ($this->request->is(['post', 'put'])) {
			$insertRecord = [];
			$productCategories->deleteAll(['product_id'=>$id], true); //delete all pre selected category
			
            $newData = $this->request->getData('chkd_ids');
			if(!empty($newData)){
				$newData = explode(",", $newData);
				$connection = ConnectionManager::get('subscription_manager');
				foreach($newData as $value){
					$insertRecord = ['product_id'=>$id, 'category_id'=>$value];
					$res = $connection->insert('product_categories', $insertRecord);
				}
			}  //pr($insertRecord);die;
			if( !empty($insertRecord) ){
				//$insertRecord = $productCategories->newEntities($insertRecord);
				//$res = $productCategories->saveMany($insertRecord);
				if( $res ){
					$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
				}else{
					$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
				}
				
			}else{
				$this->Flash->success(__('The record has been saved!'), ['key'=>'adminSuccess']);
			}
        }
		
		$product = $this->Products->get($id, [
            'contain' => 'ProductCategories'
        ]);
		$categoriesIds = $product;
		$categoriesIds = $categoriesIds->toArray();
		$categoriesIds = (new Collection($categoriesIds['product_categories']))->extract('category_id')->toArray();
		$cateTree = TableRegistry::get('SubscriptionManager.Categories')->find('threaded',['order'=>'Categories.lft'])->toArray();
		
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
			$query = TableRegistry::get('SubscriptionManager.ProductImages')->get($imgId)->toArray();
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
			$tableData = TableRegistry::get('SubscriptionManager.ProductImages');
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
				$dataTable = TableRegistry::get('SubscriptionManager.ProductImages');
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

    public function relatedProducts($id=null, $key=null, $md5=null){
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
		
		$isActive = $this->request->getQuery('is_active', '');
		$this->set('isActive', $isActive);
		if( $isActive !== '' ) { $filterData['is_active'] = $isActive; }
		
		$error = [];
		$relatedIds = [];
		$product = $this->Products->find('all')->select(['related_ids'])->where(['id =' => $id])->toArray();
		
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
				->select(['related_ids'])
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
			
			$productTable = TableRegistry::get('SubscriptionManager.Products');
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
		
		$query = TableRegistry::get('SubscriptionManager.Reviews')->find('all', ['contain'=>['Customers'], 'fields'=>['Customers.email','Reviews.id','Reviews.title','Reviews.description','Reviews.rating','Reviews.location_ip','Reviews.created','Reviews.is_active'],'conditions'=>$filterData])->order(['Reviews.created' => 'DESC'])
				 ->contain(['Locations'=>[
					 'fields'=>['id','title']
				 ]]);
		//pr($query->toArray());
		$reviews = $this->paginate($query);
		
        $this->set(compact('reviews','id'));
        $this->set('_serialize', ['reviews','id']);
    }

    public function productNotes($id=null, $key=null, $md5=null)
    {
		if( ($key != 'key') || ( $md5 != md5($id) ) ){
			return $this->redirect(['action' => 'index']);
		}
		$algoNotes = TableRegistry::get('SubscriptionManager.AlgoNotes')->getAllNotes();
		$algoNotes = array_column($algoNotes, 'note_name');
		$productNotesTable = TableRegistry::get('SubscriptionManager.ProductNotes');		
		$error = [];
        if ($this->request->is(['post'])) {
            $getData = $productNotesTable->patchEntity($productNotesTable->newEntity(), $this->request->getData());
			$error = $getData->getErrors();
			if( empty($error) ){ //pr($this->request->getData('ProductNotes.description')); die;
				$getData->product_id = $id;
				if ( in_array($this->request->getData('ProductNotes.description'), $algoNotes) ) {
					if ($productNotesTable->save($getData)) {
						$this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
						$this->redirect($this->referer());
					}else{
						$this->Flash->error(__('Sorry, record are not save!'), ['key' => 'adminError']);
					}
				} else {
					$this->Flash->error(__('Please choose notes keyword from suggestion list!'), ['key' => 'adminError']);
				}	
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
        } else if( $this->request->is(['delete']) ) {
			$noteId = $this->request->getData('noteId');
			$note = $productNotesTable->get($noteId);
			if ($productNotesTable->delete($note)) {
				$this->Flash->success(__('The record has been deleted!'), ['key' => 'adminSuccess']);
				$this->redirect($this->referer());
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		}
		$addnotes = $productNotesTable;
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
				'limit' =>$limit, // limits the rows per page
				'maxLimit' => 2000,
		];
		$query = $productNotesTable->find('all', ['fields'=>['id','title','description','is_active'],'conditions'=>['product_id'=>$id]])
				->contain(['Locations'=>[
					'fields'=>['id','title']
				]])
				->order(['ProductNotes.id' => 'DESC']);
		$notes = $this->paginate($query);
		$locations = TableRegistry::get('SubscriptionManager.Locations')->find('all',['id','title'])->hydrate(false)->toArray();
		$locations = array_combine(array_column($locations,'id'),array_column($locations,'title'));
	
        $this->set(compact('addnotes','notes','locations','id','algoNotes'));
        $this->set('_serialize', ['addnotes','notes','locations','id','algoNotes']);
    }

	public function uploadData($key=null, $md5=null) {
		if( ($key != 'key') || ( $md5 != md5('uploadData') ) ){
			return $this->redirect(['action' => 'index']);
		}
		$allColumn = ['cross', 'price', 'tier1', 'tier2', 'tier3'];
		if ( $this->request->is(['post']) ) {
			$updateStatus = $crossStatus = $priceStatus = $price1Status = $price2Status = $price3Status = 0;
			$prices = $this->request->getData('prices', []);
			if ( in_array('cross', $prices) ) { $crossStatus = 1; }
			if ( in_array('price', $prices) ) { $priceStatus = 1; }
			if ( in_array('tier1', $prices) ) { $price1Status = 1; }
			if ( in_array('tier2', $prices) ) { $price2Status = 1; }
			if ( in_array('tier3', $prices) ) { $price3Status = 1; }
			$file = $this->request->getData('name');
			if ( ($file['error'] == 4) && empty($prices) ) {
				$this->Flash->error(__('Sorry, Please choose a file to upload and check fields to updates!'), ['key' => 'adminError']);
			} else if ( $file['error'] == 4 ) {
				$this->Flash->error(__('Sorry, Please choose a file to upload!'), ['key' => 'adminError']);
			} else if ( $file['error'] !== 0 ) {
				$this->Flash->error(__('Sorry, selected file have errors!'), ['key' => 'adminError']);
			} else if ( empty($prices) ) {
				$this->Flash->error(__('Sorry, Please check fields to updates!'), ['key' => 'adminError']);
			} else if ( !in_array($file['type'], ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) ) {
				$this->Flash->error(__('Sorry, please upload excel or csv file!'), ['key' => 'adminError']);
			} else {
				$ext = explode('.', $file['name']);
				$ext = $ext[1] ?? '';
				if ( in_array($ext, ['xls', 'csv', 'xlsx']) ) {
					$uploadPath	= $file['tmp_name'];
					$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(ucfirst($ext));
					$reader->setReadDataOnly(true);
					$spreadsheet = $reader->load($uploadPath);
					$sheetData = $spreadsheet->getActiveSheet()->toArray(); //pr($sheetData);
					$header = $sheetData[0] ?? [];
					$status = 0;
					$header = array_map('strtolower', $header);
					$header0 = $header[0] ?? '';
					$header1 = $header[1] ?? '';
					$header2 = $header[2] ?? '';
					$header3 = $header[3] ?? '';
					$header4 = $header[4] ?? '';
					$header5 = $header[5] ?? '';
					if ( ($header0 == 'id') && ($header1 == 'cross') && ($header2 == 'price') && ($header3 == 'price1') && ($header4 == 'price2') && ($header5 == 'price3') ) {
						$status = 1;
					}
					if ( $status ) {
						array_shift($sheetData);
						$productPricesTable = TableRegistry::get('SubscriptionManager.ProductPrices');
						foreach ($sheetData as $row ) {
							$setUpdate = [];
							try {
								$cross = $row[1];
								$price = $row[2];
								$price1 = $row[3];
								$price2 = $row[4];
								$price3 = $row[5];
								if ( $crossStatus ) { $setUpdate['cross'] = $cross; }
								if ( $priceStatus ) { $setUpdate['price'] = $price; }
								if ( $price1Status ) { $setUpdate['price1'] = $price1; }
								if ( $price2Status ) { $setUpdate['price2'] = $price2; }
								if ( $price3Status ) { $setUpdate['price3'] = $price3; }
								if ( !empty($setUpdate) ) {
									$updateStatus = 1;
									$productPricesTable->query()->update()->set($setUpdate)->where(['product_id' => $row[0]])->execute();
								}
							} catch (\Exception $e) { }
						}
					}
				}
				if ( $updateStatus ) {
					$this->Flash->success(__('The record has been updated!'), ['key' => 'adminSuccess']);
				} else {
					$this->Flash->error(__('Sorry, Record not updated!'), ['key' => 'adminError']);
				}
			}
		}
        $this->set(compact('allColumn'));
        $this->set('_serialize', ['allColumn']);
	}

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The record has been saved!'), ['key' => 'adminSuccess']);
			$this->redirect($this->referer());
        } else {
            $this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
        }
        return true;
	}
	
	public function search(){
		$keyword 			= $this->request->getQuery('term');
		$responseArray		= array();
		$query		= TableRegistry::get('SubscriptionManager.ProductPrices')->find('all',['fields'=>['Products.id','ProductPrices.name','ProductPrices.title'],'contain'=>['Products']]);
			if( !empty($keyword) ){ $query = $query->where(['ProductPrices.location_id'=>1])->where(["MATCH (ProductPrices.name, ProductPrices.title, Products.search_keyword) AGAINST ('$keyword')"]); }
		$query = $query->toArray();
		
		if(!empty($query))
		{
			$counter		= 0;
			foreach($query as $value)
			{
				$responseArray[$counter]['id']		= $value->Product->id;
				$responseArray[$counter]['label']	= $value->name;
				$responseArray[$counter]['value']	= $value->name;
				$responseArray[$counter]['name']	= $value->name;
				$responseArray[$counter]['title']	= $value->title;
				$counter++;
			}
		}
		echo json_encode($responseArray);
		die;
	}
		
	public function productCompare()
	{
		$familyTable = TableRegistry::get('SubscriptionManager.Families')->find('All', ['fields' => ['id', 'title'], 'conditions' => ['is_active' => 'Active']])->order(['title'=>'ASC'])->hydrate(0)->toArray();
		$notesFamily = array_combine(array_column($familyTable, 'title'), array_column($familyTable, 'title'));
		$brandTable = TableRegistry::get('SubscriptionManager.Brands')->find('all', ['fields' => ['id', 'title'], 'conditions' => ['is_active' => 'Active']])->order(['title'=>'ASC'])->hydrate(0)->toArray();
		$brandFamily = array_combine(array_column($brandTable, 'id'), array_column($brandTable, 'title'));
		$productTable = TableRegistry::get('SubscriptionManager.AlgoProducts');
		$productTables = $productTable->find('All', ['fields' => ['id', 'product_name']])->hydrate(0)->toArray();
		$productData = array_combine(array_column($productTables, 'id'), array_column($productTables, 'product_name'));
		//$productData=$productTables;
		$resultFinal = [];

		if ($this->request->is(['ajax', 'post'])) {
			$familyForm = $this->request->getData('family_name', []);
			$brandForm = $this->request->getData('brand_name', '');
			if ( empty($brandForm) && empty($familyForm) ) {
				$this->Flash->error(__('Sorry, please select brand & family!'), ['key' => 'adminError']);
			} else if (empty($brandForm)){
				$this->Flash->error(__('Sorry, please select brand name!'), ['key' => 'adminError']);
			} else if ( !empty($familyForm) ) {
				$productFrom = $this->request->getData('product_from', []);
				$genderParam  = $this->request->getData('gender','');
				$this->set('product_from', $productFrom);
				$this->set('family_name', $familyForm);
				//pr($familyForm); die;
				$gender = ['unisex', 'mfemale'];
				if ( !empty($genderParam) ) {
					$gender[] = $genderParam; 
				}
				$productTableWith = TableRegistry::get('SubscriptionManager.Products')->find('All')
				->innerJoinWith('ProductCategories', function ($q){ return $q->where(['category_id'=>4]); })
				->contain(['ProductPrices'])->where(['gender IN' => $gender, 'brand_id'=>$brandForm])->hydrate(0)->toArray();
				$totalFamilyEarnValue = 0;
				$matched = [];
				if ( !empty($productFrom) ) {
					$topNotePer =  40;
					$baseNotePer = 30;
					//$productFrom = implode(",", $productFrom); pr($productFrom); die;
					$prodFrom =	$productTable->find('All')->where(['id IN' => $productFrom])->hydrate(0)->toArray();
					foreach ( $prodFrom as $resFrom ) {
						$pyramidFrom =  json_decode($resFrom['perfume_pyramid']);
						$topNoteNameFrom = [];
						$middleNoteNameFrom = [];
						$baseNoteNameFrom = [];
						$topNoteNameFromNds = [];
						$middleNoteNameFromNds = [];
						$baseNoteNameFromNds = [];
						if ( isset($pyramidFrom->topNote) ) {
							$familyName = [];
							foreach ( $pyramidFrom->topNote as $value ) {
								$noteName = trim($value->name); //top notes title
								if ( !empty($noteName) ) {
									$topNoteNameFromNds[] = strtolower($noteName);
									$familyName[] = $noteName;
								}
							}
							$topNoteNameFrom = $this->Product->getFamilyName($familyName);
						}
						if ( isset($pyramidFrom->middleNote) ) {
							$familyName = [];
							foreach ($pyramidFrom->middleNote as $value) {
								$noteName = trim($value->name); //middle notes title
								if ( !empty($noteName) ) {
									$middleNoteNameFromNds[] = strtolower($noteName);
									$familyName[] = $noteName;
								}
							}
							$middleNoteNameFrom = $this->Product->getFamilyName($familyName);
						}
						if ( isset($pyramidFrom->baseNote) ) {
							$familyName = [];
							foreach ($pyramidFrom->baseNote as $value) {
								$noteName = trim($value->name); //base notes title
								if ( !empty($noteName) ) {
									$baseNoteNameFromNds[] = strtolower($noteName);
									$familyName[] = $noteName;
								}
							}
							$baseNoteNameFrom = $this->Product->getFamilyName($familyName);
						}
						$top_P_MiddNoteNameFrom = array_merge($topNoteNameFrom, $middleNoteNameFrom);
						$midd_P_BaseNoteNameFrom = array_merge($middleNoteNameFrom, $baseNoteNameFrom);
						$notesMergeAllFrom = array_merge($topNoteNameFromNds, $middleNoteNameFromNds, $baseNoteNameFromNds);

						foreach ($productTableWith as $resWith) { //Products Table Data
							$topMiddleBaseNotes = $this->Product->getNoteByProduct($resWith['id']);
							$topNoteNameWith = $this->Product->getFamilyName($topMiddleBaseNotes['top']);
							$topNoteNameWithNds = $topMiddleBaseNotes['top'];
							$middleNoteNameWith = $this->Product->getFamilyName($topMiddleBaseNotes['middle']);
							$middleNoteNameWithNds = $topMiddleBaseNotes['middle'];
							$baseNoteNameWith = $this->Product->getFamilyName($topMiddleBaseNotes['base']);
							$baseNoteNameWithNds = $topMiddleBaseNotes['base'];
							$top_P_MiddNoteNameWith = array_merge($topNoteNameWith, $middleNoteNameWith);
							$midd_P_BaseNoteNameWith = array_merge($middleNoteNameWith, $baseNoteNameWith);
							$notesMergeAllWith = array_merge($topNoteNameWithNds, $middleNoteNameWithNds, $baseNoteNameWithNds); 
							//pr($top_P_MiddNoteNameWith); die;
							//Note Comparing
							$topMiddleToTopMiddle     =   array_unique(array_intersect($top_P_MiddNoteNameFrom, $top_P_MiddNoteNameWith));
							$middleBaseToMiddleBase   =   array_unique(array_intersect($midd_P_BaseNoteNameFrom, $midd_P_BaseNoteNameWith));

							$totalMatchNotes = 0;
							if (!empty($topMiddleToTopMiddle)) {
								$matchTopValue =  $this->Product->getPercentSum($top_P_MiddNoteNameFrom, $top_P_MiddNoteNameWith, $topMiddleToTopMiddle);
								$totalMatchNotes +=  $this->Product->giveAdditionalPercent($matchTopValue, $topNotePer);
							}

							if (!empty($middleBaseToMiddleBase)) {
								$matchBaseValue = $this->Product->getPercentSum($midd_P_BaseNoteNameFrom, $midd_P_BaseNoteNameWith, $middleBaseToMiddleBase);
								$totalMatchNotes +=     $this->Product->giveAdditionalPercent($matchBaseValue, $baseNotePer);
							}
							$getNotesMatchValueCmp = [];
							if (!empty($notesMergeAllFrom) &&  !empty($notesMergeAllWith)) {
								$getNotesMatchValueCmp =   $this->Product->getNotesValueByMatch($notesMergeAllFrom, $notesMergeAllWith);
							}
							if (!empty($getNotesMatchValueCmp))
								$totalMatchNotes += $getNotesMatchValueCmp['matchvalue'];
							//$totalMatchNotes = (($totalMatchNotes != '') ? ($totalMatchNotes) : '0');
							//End Note Comparing
							//$totalMatchNotesAndFaml = $totalMatchNotes;
							$TotalPointsCollected = round((($totalMatchNotes + $resWith['pscore']) / 2), 2);
							//$TotalPointsCollected = $totalMatchNotesAndFaml;
							$takeAllFamily = array_merge($topNoteNameWith, $middleNoteNameWith, $baseNoteNameWith); 
							$divtakeAllNoteNameWith =   1;
							$takeAllPercentWith     =   $this->Product->dividendPer($takeAllFamily, $matched, $divtakeAllNoteNameWith);
							$fnlArrSort = [];
							foreach ($takeAllPercentWith as $key => $value) {
								$fnlArrSort[] = ['name' => $key, 'value' => $value, 'rank' => $value];
							}
							usort($fnlArrSort, function ($a, $b) {
								return $b <=> $a;
							});
							$fnlarrper = [];
							$topNoteFamilyPer = '';
							foreach ($fnlArrSort as $key => $value) {
								switch ($value['value']) {
									case 1: $topNoteFamilyPer = 90; break;
									case 2: $topNoteFamilyPer = 80; break;
									default: $topNoteFamilyPer = 70;
								}
								$percntfamily = ($value['value'] / count($takeAllFamily));
								$fnlarrper[] = ['name' => $value['name'], 'value' => $percntfamily, 'rank' => $value['rank']];
							}
							
							$topFamilyHold =  array_slice($fnlarrper, 0, 5);
							if (count($familyForm) == 1 && count($topFamilyHold) >= 1) {
								if (count($topFamilyHold) >= 1) {
									$yourArray = array_map('strtolower', $familyForm);
									foreach ($topFamilyHold as $key => $value) {
										if (in_array(strtolower($value['name']), $yourArray)) {
											switch ($value['rank']) {
												case 1: $topNoteFamilyPer = 70; break;
												case 2: $topNoteFamilyPer = 80; break;
												default: $topNoteFamilyPer = 90;
											}
											$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
										}
									}
								}
								$famscore = $this->Product->getFamilyScore($familyForm);
								if (count($famscore) > 1) {
									$fscval = round(array_sum($famscore) / count($famscore), 2);
								} else {
									$fscval = array_sum($famscore);
								}
							} else if (count($familyForm) > 1) {
								$rtyu = [];
								$fscval = '';
								$topNoteFamilyPer = 0;
								$topFamilyHoldArr = array_column($topFamilyHold, 'name');
								$hhArr =  array_intersect(array_map('strtolower', $topFamilyHoldArr), array_map('strtolower', $familyForm)); //pr($topFamilyHoldArr);pr($familyForm);pr($hhArr);
								switch (count($hhArr)) {
									case 1: $topNoteFamilyPer = 80; break;
									case 2: $topNoteFamilyPer = 90; break;
									default: $topNoteFamilyPer = 100;
								}
								$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
								$famscore = $this->Product->getFamilyScore($hhArr);	
								if (count($famscore) > 1) {
									$fscval = round(array_sum($famscore) / count($famscore), 2);
								} else {
									$fscval = array_sum($famscore);
								}
							}
							//pr($takeAllFamily);
							//pr($takeAllPercentWith);
							//pr($fnlArrSort);
							//pr($familyForm);
							//pr($fnlarrper);
							//pr($topFamilyHold);
							//pr($rtyu);
							//die;
							$totalFamilyEarnValue = $rtyu[0]['EarnValue'] ?? 0;
							$resultFinal[] = [
								'name' => $resWith['product_prices'][0]['title'],
								'score' => $resWith['pscore'],
								'value' => $TotalPointsCollected,
								'FamilyEarnValue' => $totalFamilyEarnValue,
								'notesMatchValue' => $getNotesMatchValueCmp['matchvalue'] ?? 0,
								'totalMatchNotes' => $totalMatchNotes,
								'affinityScore'=> round(($resWith['pscore'] + $totalFamilyEarnValue + $totalMatchNotes)/3, 2)
							];
						} //end of foreach
					} //end of foreach
				} else {
					foreach ($productTableWith as $resWith) {
						$rtyu = [];
						$fnlArrSort = [];
						$topNoteFamilyPer = 0;
						$totalMatchNotes = 0;
						$topMiddleBaseNotes = $this->Product->getNoteByProduct($resWith['id']);
						$topNoteNameWith = $this->Product->getFamilyName($topMiddleBaseNotes['top']);
						$middleNoteNameWith = $this->Product->getFamilyName($topMiddleBaseNotes['middle']);
						$baseNoteNameWith = $this->Product->getFamilyName($topMiddleBaseNotes['base']);
						$takeAllFamily = array_merge($topNoteNameWith, $middleNoteNameWith, $baseNoteNameWith); 
						$divtakeAllNoteNameWith =   1;
						$takeAllPercentWith     =   $this->Product->dividendPer($takeAllFamily, $matched, $divtakeAllNoteNameWith);
						foreach ($takeAllPercentWith as $key => $value) {
							$fnlArrSort[] = ['name' => $key, 'value' => $value, 'rank' => $value];
						}
						usort($fnlArrSort, function ($a, $b) {
							return $b <=> $a;
						});
						$fnlarrper = [];
						foreach ($fnlArrSort as $key => $value) {
							switch ($value['value']) {
								case 1: $topNoteFamilyPer = 90; break;
								case 2: $topNoteFamilyPer = 80; break;
								default: $topNoteFamilyPer = 70;
							}
							$percntfamily = ($value['value'] / count($takeAllFamily));
							$fnlarrper[] = ['name' => $value['name'], 'value' => $percntfamily, 'rank' => $value['rank']];
						}

						$topFamilyHold =  array_slice($fnlarrper, 0, 5);
						if (count($familyForm) == 1 && count($topFamilyHold) >= 1) {
							$yourArray = array_map('strtolower', $familyForm);
							foreach ($topFamilyHold as $key => $value) {
								if (in_array(strtolower($value['name']), $yourArray)) {
									switch ($value['rank']) {
										case 1: $topNoteFamilyPer = 70; break;
										case 2: $topNoteFamilyPer = 80; break;
										default: $topNoteFamilyPer = 90;
									}
									$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
								}
							}
							$famscore = $this->Product->getFamilyScore($familyForm);
							if (count($famscore) > 1) {
								$fscval = round(array_sum($famscore) / count($famscore), 2);
							} else {
								$fscval = array_sum($famscore);
							}
						} else  if (count($familyForm) > 1) {
							$fscval = '';
							$topNoteFamilyPer = 0;
							$topFamilyHoldArr = array_column($topFamilyHold,'name');
							$hhArr =  array_intersect(array_map('strtolower', $topFamilyHoldArr), array_map('strtolower', $familyForm));
							switch (count($hhArr)) {
								case 1: $topNoteFamilyPer = 80; break;
								case 2: $topNoteFamilyPer = 90; break;
								default: $topNoteFamilyPer = 100;
							}
							$rtyu[] = ['EarnValue' => $topNoteFamilyPer];
							$famscore = $this->Product->getFamilyScore($hhArr);
							if (count($famscore) > 1) {
								$fscval = round(array_sum($famscore) / count($famscore), 2);
							} else {
								$fscval = array_sum($famscore);
							}
						}
						//pr($takeAllFamily);
						//pr($takeAllPercentWith);
						//pr($fnlArrSort);
						//pr($familyForm);
						//pr($fnlarrper);
						//pr($topFamilyHold);
						//pr($rtyu);
						//die;
						$totalFamilyEarnValue = $rtyu[0]['EarnValue'] ?? 0;
						$TotalPointsCollected = round((($totalFamilyEarnValue + $resWith['pscore']) / 2), 2);
						$resultFinal[] = [
							'name' => $resWith['product_prices'][0]['title'], 
							'score' => $resWith['pscore'],
							'notesMatchValue' => $getNotesMatchValueCmp['matchvalue'] ?? 0, 
							'allFamily' => $fnlarrper, 
							'FamilyEarnValue' => $totalFamilyEarnValue, 
							'baseNoteNameWith' => $baseNoteNameWith, 
							'middleNoteNameWith' => $middleNoteNameWith, 
							'topNoteNameWith' => $topNoteNameWith,  
							'value' => $TotalPointsCollected,
							'affinityScore' => $TotalPointsCollected,
							'totalMatchNotes' => $totalMatchNotes
						];
					}
				}
				//usort($resultFinal,[$this->Product,'compareOrder']);
				usort($resultFinal, function ($a, $b) {
					return $b['value'] <=> $a['value'];
				});
			} else {
				$this->Flash->error(__('Sorry, please select at least one family!'), ['key' => 'adminError']);
			}
		}

		$this->set(compact('notesFamily', 'brandFamily', 'productData', 'resultFinal'));
		$this->set('_serialize', ['notesFamily', 'brandFamily', 'productData', 'resultFinal']);
	}

	public function beforeFilter(Event $event)
	{
		$this->Security->config('unlockedFields', ['prices']);
		$actions = ['saverelated','updateImages'];
		if (in_array($this->request->params['action'], $actions)) {
			// for csrf
			//$this->eventManager()->off($this->Csrf);
			// for security component
			$this->Security->config('unlockedActions', $actions);
		}
	}
}
