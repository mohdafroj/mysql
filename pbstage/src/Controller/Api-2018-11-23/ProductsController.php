<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Connection;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;

class ProductsController extends AppController
{
	public function initialize()
    {
        parent::initialize();
		$this->loadComponent('Product');
		$this->loadComponent('Delhivery');
		$this->loadComponent('Store');
    }
    public function index()
    {
		//$products = $this->Product->notifyEmail();
		//pr($products);
		die;
    }

    public function notifyMe()
    {
		$data = [];
		$status = 0; 
		$message = '';
		$action = 1;
		if( $this->request->is(['post']) ){
			$email = $this->request->getData('email');
			$productId = $this->request->getData('productId');
			if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
				$message = 'Sorry, Please enter valid email id!';
				$action = 0;
			}
			if( !is_numeric($productId) ) {
				$message = 'Sorry, Product Id should not valid!';
				$action = 0;
			}

			if($action){
				$notifyMeTable = TableRegistry::get('NotifyMe');
				$query = $notifyMeTable->find("all",['conditions'=>['email'=>$email,'product_id'=>$productId]])->toArray();
				$message = 'Your request successfully saved!';
				$status = 1;
				if( empty($query) ){
					$newData = $notifyMeTable->newEntity();
					$newData->email = $email;
					$newData->product_id = $productId;
					if( !$notifyMeTable->save($newData) ){
						$status = 0; 
						$message = 'Sorry, Please try again!';
					}
				}else{
					if(!$query[0]['status']){
						$row = $notifyMeTable->get($query[0]['id']);
						$row->status = 1;
						$data = $notifyMeTable->save($row);
					}
				}
			}
		}else{
			$message = 'Invalid request!';
		}
		echo json_encode(['message'=>$message, 'status'=>$status, 'data'=>$data]);
		die;
    }

	public function getSuggestion(){
		$data = [];
		//$limit = $this->request->getQuery('limit', 20);
		//$keyword = $this->request->getQuery('searchkeyword', NULL);

		$filterData['Products.is_active'] = 'active';		
		$query = $this->Products->find('all', ['fields'=>['id','title','sku_code','url_key'],'conditions'=>$filterData])
				->contain([
					'Brands' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','title','image','description'])->where(['Brands.is_active'=>'active']);
						}
					]	
				])
				->contain([
					'ProductsImages' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','product_id','title','alt_text','img_base'])->where(['ProductsImages.is_base'=>1,'ProductsImages.is_active'=>'active']);
						}
					]	
				])
			->order(['Products.name'])
				->toArray();
			
		if(count($query) > 0){
			foreach($query as $value){
				$images = [];
				foreach($value->products_images as $v){
					$images = [
						'id'=>$v->id,
						'alt'=>$v->alt_text,
						'title'=>$v->title,
						'url'=>$v->img_base
					];
					break;
				}

				$brand = [];
				if( !empty($value->brand) ){
					$brand = [
						'id'			=> $value->brand->id,
						'title'			=> $value->brand->title,
						'image'			=> $value->brand->image,
					];
				}
				$tax = explode('_',$value->goods_tax);

				$data[] = [
					'id'=>$value->id,
					'title'=>$value->title,
					'urlKey'=>$value->url_key,
					'brand'=>$brand,
					'images'=>$images,
				];
			}
		}
		
		echo json_encode($data);
		die;
	}
		
	public function getDetails(){
		$data = $param = [];
		$status = false; 
		$message = '';
		if( $this->request->is('get') ){
			$param['id'] 		= $this->request->getQuery('id', 0);
			$param['urlKey']	= $this->request->getQuery('key', '');
			$param['userId'] 	= $this->request->getQuery('userId', 0);
			
			$data = $this->Product->getDetails($param);			
			if( isset($data['id']) ){
				$message = 'Record found!';
				$status = true;
			}else{
				$message = 'Sorry, this product not found!';
			}
		}else{
			$message = 'Sorry, requested method not allow!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		echo json_encode($response); die;
	}

	public function getReviews(){
		$data = $param = [];
		$status = false; 
		$message = '';
		if( $this->request->is('get') ){
			$limit 		= $this->request->getQuery('limit', 10);
			$productId 	= $this->request->getQuery('productId', 0);
			$page	   	= $this->request->getQuery('page', 1);
			
			$reviews = $this->Product->getProductReviews($productId, $limit, $page);
			$total   = sizeof($reviews);		
			if( $total > 0 ){
				$message = "Total $total records found!";
			}else{
				$message = 'Sorry, reviews not found!';
			}
			$status = true;
			$data['reviews'] = $reviews;
			$data['pager'] = $page;
			$data['total'] = $total;
			$data['viewMore'] = ($total == $limit) ? 1:0;
		}else{
			$message = 'Sorry, requested method not allow!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		echo json_encode($response); die;
	}

	public function search()
	{
		$keyword 			= $this->request->getQuery('term');
		$responseArray		= array();
		$query		= $this->Products->find('all',[]);
			if( !empty($keyword) ){ $query = $query->where(["MATCH (Products.name, Products.title, Products.search_keyword) AGAINST ('$keyword')"]); }
		$query = $query->toArray();
		
		
		if(!empty($query))
		{
			$counter		= 0;
			foreach($query as $value)
			{
				$responseArray[$counter]['id']		= $value->id;
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
	
	/*######################################## Start Genric Pages ########################################*/
	public function getOfferProducts(){
		$data = $filterData = [];
		$status = 0; 
		$message = '';
		$relatedIds = 0;
		if( $this->request->is('get') ){
			$userId = $this->request->getQuery('userId', 0);
			$categoryId = $this->request->getQuery('category-id', 0);
			$gender 	= $this->request->getQuery('gender', '');
			$code 		= $this->request->getQuery('sku_code', '');
			$combo 		= $this->request->getQuery('combo', '0');
			$filterData['Products.is_combo'] = $combo;
			if( !empty($gender) ){ $filterData['Products.gender'] = strtolower($gender); }
			if( !empty($code) ){ $filterData['Products.sku_code'] = $code; }
			
			$inStock  	= $this->request->getQuery('in-stock', -1);
			switch($inStock){
				case 1 : $filterData['Products.is_stock'] = 'in_stock'; break;
				case 0 : $filterData['Products.is_stock'] = 'out_of_stock'; break;
				default:
			}
			
			$data = $this->Product->getOfferProducts($filterData, $categoryId, $userId);			
			$status = 1;
			$message = count($data).' product found!';
		}else{
			$message = 'Sorry, requested method not allow!';
		}
		//pr($data); die;
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		echo json_encode($response); die;
	}


	public function getSkuProducts(){
		$data = $filterData = [];
		$status = 0; 
		$message = '';
		$relatedIds = 0;
		if( $this->request->is('get') ){
			$userId = $this->request->getQuery('userId', 0);
			$categoryId = $this->request->getQuery('category-id', 0);
			$gender 	= $this->request->getQuery('gender', '');
			$sku 		= ['6291100177929','6291106810196','9911100200041','3610400000103','6354145841023','6330123456601','6300020150018','6291100178773','6291106810165','6299800200220','3610400035327','3354145841019','6300861086033','6300020150087','6291100170098','6291106810141','6299800200312','3610400035310','6300020152838','6390808880992','6300020152463','6291100179466','6291106810110','PB00000021','3610400035280','6354145107860','6390808880886','6300020150032','6291100178766','6291106810172','9911100199918','3610400035297','6300020152852','6330123452900','6300020150070','6291100175789','6291106810011','9911100199949','3610400035273','6354145107860','6300861086040','6300020150056'];
			$combo 		= $this->request->getQuery('combo', '0');
			//$filterData['Products.is_combo'] = $combo;
			if( !empty($gender) ){ $filterData['Products.gender'] = strtolower($gender); }
			if( !empty($sku) ){ $filterData['Products.sku_code IN'] = $sku; }
			//pr($filterData);
			$inStock  	= $this->request->getQuery('in-stock', -1);
			switch($inStock){
				case 1 : $filterData['Products.is_stock'] = 'in_stock'; break;
				case 0 : $filterData['Products.is_stock'] = 'out_of_stock'; break;
				default:
			}
			$data = $this->Product->getOfferProducts($filterData, $categoryId, $userId);			
			$status = 1;
			$message = count($data).' product found!';
		}else{
			$message = 'Sorry, requested method not allow!';
		}
		//pr($data); die;
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		echo json_encode($response); die;
	}
	/*######################################## End of Genric Pages ########################################*/
	
	public function getBrandsByCategory(){
		$data = [];
		$success = false; 
		$message = '';
		if( $this->request->is('get') ){
			$categoryId = $this->request->getQuery('category-id', 0);
			if( $categoryId > 0 ){
				$param['categoryId'] = $categoryId;
				$param['returnKey'] = 1;
				$data = $this->Product->getBrandsByCategory($param);
			}
			$success = true;
		}else{
			$message = 'Sorry, requested method not allow!';
		}
		//pr($data); die;
		
		$response = ['message'=>$message, 'status'=>$success, 'data'=>$data];
		echo json_encode($response); die;
	}
		
	public function getFilterProducts(){
		$data = $param = $brands = $products = $filterData = [];
		$success = false; 
		$totalProduct = 0;
		$message = '';
		try{
			$brandKey = $this->request->getQuery('brandKey', ''); //louis-cardin-perfumes
			$param['gender'] 		= $this->request->getQuery('gender', '');
			$param['userId'] 		= $this->request->getQuery('userId', 0);
			$param['combo'] 		= $this->request->getQuery('combo', 0);
			$param['outStock'] 	= $this->request->getQuery('out-of-stock', 0);
			$page 		= $this->request->getQuery('page', 1);
			$limit 		= $this->request->getQuery('limit', 12);

			$param['categoryId'] 	= 0;
			$param['brandId'] 		= 0;
			if( !empty($brandKey) ){
				$res = $this->Product->getBrandByKey($brandKey);
				if(!empty($res)){
					$param['categoryId'] 	= $res['category_id'];
					$param['brandId'] 		= $res['brand_id'];
				}
			}
			
			$filterData = [];
			$categoryId = ( isset($param['categoryId']) && 	($param['categoryId'] > 0) ) 	? $param['categoryId'] 	: 0;
			$brandId 	= ( isset($param['brandId']) 	&& 	($param['brandId'] > 0) ) 		? $param['brandId'] 	: 0;
			$gender 	= ( isset($param['gender']) 	&& 	($param['gender'] != '') ) 		? $param['gender'] 		: '';
			$combo 		= ( isset($param['combo']) && ($param['combo'] > 0)) ? 1 : 0;
			$outStock 	= isset($param['outStock']) ? $param['outStock'] : 0;
			$catIds = [];
			if( $categoryId == 7 ){
				$catIds = [7,4];
			}else{
				$catIds = [$categoryId];
			}
	
			$userId = ( isset($param['userId']) && ($param['userId'] > 0) ) ? $param['userId'] : 0;
			$cart = $this->Store->getActiveCart($userId);
			$cart = $cart['cart'];
			$cartIds = [];
			if( count($cart) ){ $cartIds = array_column($cart, 'id'); }
			
			$this->paginate = [
				'limit'=>$limit,
				'maxLimit'=>20,
				'page'=>$page,
				'order'=>['Products.is_stock'=>'asc', 'Products.is_combo'=>'asc', 'Products.sort_order'=>'asc']
			];
	
			if( !empty($gender) ){
				$filterData['Products.gender IN'] = ['unisex',$gender];
			}
			if( $brandId > 0 ){
				$filterData['Products.brand_id'] = $brandId;
			}
			if( $outStock > 0 ){
				$filterData['Products.is_stock'] = 'in_stock';
			}
			if($combo ==1){
				$filterData['Products.is_combo'] = $combo;
			}
	
			$filterData['Products.is_active'] = 'active';
			
			$dataTable = TableRegistry::get('Products');
			$query = $dataTable->find('all', ['fields'=>['id','name','title','url_key','size','size_unit','best_seller','tag_line','brand_id','price','offer_price','is_stock','is_combo'],'conditions'=>$filterData])
					->contain([
						'ProductsImages' =>[
							'queryBuilder'=> function($q){
								return $q->select(['id','product_id','title','alt_text','img_large'])->where(['ProductsImages.is_large'=>1,'ProductsImages.is_active'=>'active']);
							}
						]	
					]);
			if( count($catIds) > 0 ){
				$query = $query->matching("ProductsCategories", function($q) use ($catIds){
						return $q->where(['ProductsCategories.category_id IN'=>$catIds]);
					});
			}		
			$totalProduct 	= count($query->toArray());
			if( !empty($query) ){
				$query= $this->paginate($query);
				//pr(count($query->toArray())); die; 
				$query = $query->toArray();
				foreach($query as $value){
					$images = [];
					foreach($value->products_images as $v){
						$images = [
							'id'=>$v->id,
							'alt'=>$v->alt_text,
							'title'=>$v->title,
							'url'=>$v->img_large
						];
					}
	
					$products[] = [
						'id'=>$value->id,
						'name'=>$value->name,
						'title'=>$value->title,
						'urlKey'=>$value->url_key,
						'tagClass'=>$this->Product->getTagClass($value->tag_line),
						'tagLine'=>empty($value->tag_line)?'':$value->tag_line,
						'price'=>$value->price,
						'discount'=>$this->Product->getDiscounts($value->price),
						'size'=>$value->size,
						'sizeUnit'=>$value->size_unit,
						'offerPrice'=>$value->offer_price,
						'brandId'=>$value->brand_id,
						'sold'=>$value->best_seller,
						'isStock'=>$value->is_stock,
						'isCombo'=>$value->is_combo > 0 ? 1:0,
						'isCart'=>in_array($value->id, $cartIds) ? 1:0,
						'images'=>$images
					];
				}
			}
			$tempProBrandIds = array_column($products, 'brandId');
			$param['brandIds'] = array_unique($tempProBrandIds);
			
			//echo count($brandIds); die;					
			$brands = $this->Product->getBrandsByCategory($param);
			$message = $totalProduct.', record found!';
			$success = true;
		}catch(\Exception $e){
			$message = 'Sorry, requested method not allow!';
		}
		$data['total'] 	= $totalProduct;
		$data['brands'] 	= $brands;
		$data['products'] 	= $products;
		//pr($data); die;		
		$response = ['message'=>$message, 'status'=>$success, 'data'=>$data];
		echo json_encode($response); die;
	}
		
	public function getMoreProducts(){
		$data = $param = $filterData = [];
		try{
			$brandKey = $this->request->getQuery('brandKey', ''); //louis-cardin-perfumes
			$gender 		= $this->request->getQuery('gender', '');
			$userId 		= $this->request->getQuery('userId', 0);
			$combo 		= $this->request->getQuery('combo', 0);
			$outStock 	= $this->request->getQuery('out-of-stock', 0);
			$page 		= $this->request->getQuery('page', 1);
			$limit 		= $this->request->getQuery('limit', 12);

			if( !empty($brandKey) ){
				$res = $this->Product->getBrandByKey($brandKey);
				if(!empty($res)){
					$categoryId 	= $res['category_id'];
					$brandId 		= $res['brand_id'];
				}
			}
			
			$filterData = [];
			$catIds = [];
			if( $categoryId == 7 ){
				$catIds = [7,4];
			}else{
				$catIds = [$categoryId];
			}
	
			$cart = $this->Store->getActiveCart($userId);
			$cart = $cart['cart'];
			$cartIds = [];
			if( count($cart) ){ $cartIds = array_column($cart, 'id'); }
			
			$this->paginate = [
				'limit'=>$limit,
				'maxLimit'=>20,
				'page'=>$page,
				'order'=>['Products.is_stock'=>'asc', 'Products.is_combo'=>'asc', 'Products.sort_order'=>'asc']
			];
	
			if( !empty($gender) ){
				$filterData['Products.gender IN'] = ['unisex',$gender];
			}
			if( $brandId > 0 ){
				$filterData['Products.brand_id'] = $brandId;
			}
			if( $outStock > 0 ){
				$filterData['Products.is_stock'] = 'in_stock';
			}
			if($combo ==1){
				$filterData['Products.is_combo'] = $combo;
			}
	
			$filterData['Products.is_active'] = 'active';
			
			$dataTable = TableRegistry::get('Products');
			$query = $dataTable->find('all', ['fields'=>['id','name','title','url_key','size','size_unit','best_seller','tag_line','brand_id','price','offer_price','is_stock','is_combo'],'conditions'=>$filterData])
					->contain([
						'ProductsImages' =>[
							'queryBuilder'=> function($q){
								return $q->select(['id','product_id','title','alt_text','img_large'])->where(['ProductsImages.is_large'=>1,'ProductsImages.is_active'=>'active']);
							}
						]	
					]);
			if( count($catIds) > 0 ){
				$query = $query->matching("ProductsCategories", function($q) use ($catIds){
						return $q->where(['ProductsCategories.category_id IN'=>$catIds]);
					});
			}		
			if( !empty($query) ){
				$query= $this->paginate($query);
				$query = $query->toArray();
				foreach($query as $value){
					$images = [];
					foreach($value->products_images as $v){
						$images = [
							'id'=>$v->id,
							'alt'=>$v->alt_text,
							'title'=>$v->title,
							'url'=>$v->img_large
						];
					}
	
					$data[] = [
						'id'=>$value->id,
						'name'=>$value->name,
						'title'=>$value->title,
						'urlKey'=>$value->url_key,
						'tagClass'=>$this->Product->getTagClass($value->tag_line),
						'tagLine'=>empty($value->tag_line)?'':$value->tag_line,
						'price'=>$value->price,
						'size'=>$value->size,
						'sizeUnit'=>$value->size_unit,
						'offerPrice'=>$value->offer_price,
						'brandId'=>$value->brand_id,
						'sold'=>$value->best_seller,
						'isStock'=>$value->is_stock,
						'isCombo'=>$value->is_combo > 0 ? 1:0,
						'isCart'=>in_array($value->id, $cartIds) ? 1:0,
						'images'=>$images
					];
				}
			}
		}catch(\Exception $e){
		}
		echo json_encode($data); die;
	}
		
}
