<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;

//use Cake\Http\Client;
use Cake\Network\Http\Client;
/**
 * Admin component
 */
class FacebookComponent extends Component
{
	private $token 				= 'EAAYlnSFkIm8BAE6NBnWsnzyEMMELvw2GMVZABV75rThm4ASllVtZC32GSqxE73RDDAVdWOR3bgu3JFNe00VyaaAg5motNRb73XJmZA0Jycqzmira0Vcoz3ysI2EDxoBgxRXGQfXb8A75fNDEEfkCDmvShzbbfDzCYSWENb1rKLO9dC457jI';
	private $apiHost			= 'https://graph.facebook.com/v4.0/2127945087522331/batch'; //2127945087522331
	private $apiHostStatus		= 'https://graph.facebook.com/v4.0/2127945087522331/check_batch_request_status';
	private $http;
	
	public function __construct(){
		$this->http = new Client();
	}

	// handle status of batch request
	public function getStatus ($handle) {
		$inputData  = [
			'access_token'=>$this->token,
			'handle'=>$handle
		];
		$inputData  = json_encode($inputData); 
		//pr($inputData); die;
		return $this->http->get($this->apiHostStatus, $inputData, [ 'headers'=>['Content-Type'=>'application/json']]);
	}

	//delete a item from facebook catalogue
	public function removeItem($sku=NULL)
	{
		$response   = Null;
		if( !empty($sku) ){
			$inputData  = [
				'access_token'=>$this->token,
				'requests'=>[
					[
						'method'=>'DELETE',
						'retailer_id'=>$sku
					]
				]
			];
			$inputData  = json_encode($inputData); 
			$result 	= $this->http->post($this->apiHost, $inputData,['headers'=>['Content-Type'=>'application/json']]);
			$response 	= $result;
		}
		return $response;
	}

	//add a new item to facebook catalogue
	public function addItem($productId=0)
	{
		$response   = null;
		$inputs		= [];
		if( is_numeric($productId) && ($productId > 0) ){
			$inputs		= [$productId];
		}elseif( is_array($productId) ){
			$inputs		= $productId;
		}
		$items		= $this->getItems($inputs);
		$requestData = [];
		foreach( $items as $item ){
			$requestData[]  = [
				'method'=>'UPDATE',
				'retailer_id'=>$item['sku'],
				'data'=>[
					//'id'=>$item['sku'],
					'name'=>$item['title'],
					//'item_group_id'=>$item['sku'],
					'description'=>$item['description'],
					'currency'=>$item['currency'],
					'price'=>$item['price']*100,
					'sale_price'=>$item['sale_price']*100,
					'availability'=>$item['stock'],
					'brand'=>$item['brand'],
					'category'=>$item['category'],
					'gender'=>$item['gender'],
					'image_url'=>$item['image'],
					'url'=>$item['url'],
					'condition'=>'used'
				]
			];
		}

		if( count($requestData) > 0 ){
			$inputData  = [
				'access_token'=>$this->token,
				'requests'=>$requestData
			];
			$inputData  = json_encode($inputData); 
			//pr($inputData); die;
			$result 	= $this->http->post($this->apiHost, $inputData,['headers'=>['Content-Type'=>'application/json']]);
			$response 	= $result;
		}
		return $response;
	}

	//add or update all items to facebook catalogue
	public function addAllItem()
	{
		$response    = Null;
		$items		 = $this->getItems([]);
		$requestData = [];
		foreach( $items as $item ){
			$requestData[]  = [
				'method'=>'UPDATE',
				'retailer_id'=>$item['sku'],
				'data'=>[
					//'id'=>$item['sku'],
					'name'=>$item['title'],
					//'item_group_id'=>$item['sku'],
					'description'=>$item['description'],
					'currency'=>$item['currency'],
					'price'=>$item['price']*100,
					'sale_price'=>$item['sale_price']*100,
					'availability'=>$item['stock'],
					'brand'=>$item['brand'],
					'category'=>$item['category'],
					'gender'=>$item['gender'],
					'image_url'=>$item['image'],
					'url'=>$item['url'],
					'condition'=>'new'
				]
			];
			
		}
		if( count($requestData) > 0 ){
			$inputData  = [
				'access_token'=>$this->token,
				'requests'=>$requestData
			];
			$inputData  = json_encode($inputData); 
			//pr($inputData); die;
			$result 	= $this->http->post($this->apiHost,$inputData,['headers'=>['Content-Type'=>'application/json']]);
			$response 	= $result;
		}
		///pr($response);
		return $response;
	}

	//get item from database
	private function getItems(Array $productsId){
		$categoryTable = TableRegistry::get('Categories');
		$dataTable = TableRegistry::get('Products');
		$query	   = $dataTable->find('all',['contain'=>['Brands']]);
		if( is_array($productsId) && count($productsId) > 0 ){
			$query = $query->where(['Products.id IN'=>$productsId]);
		}
		    $query = $query->where(['Products.is_active'=>'active'])
					->contain([
						'ProductsImages'=>[
							'queryBuilder'=>function($q){
								return $q->select(['product_id','url'=>'img_large'])->where(['is_large'=>'1']);
							}
						],
						'ProductsCategories'=>[
							'queryBuilder'=>function($q){
								return $q->select(['product_id','category_id']);
							}
						]
					])
					->hydrate(false)
					->toArray();
		$productList = []; 
		if( !empty($query) ){
			foreach($query as $value){
				$categoryName = NULL;
				$categoryIds  = array_column($value['products_categories'], 'category_id');
				if( count($categoryIds) > 0 ){
					$cat		  = $categoryTable->get($categoryIds[0],['fields'=>'name'])->toArray();
					$categoryName = $cat['name'] ?? NULL;
				}
				$productList[] = [
					'sku'=>$value['sku_code'],
					'name'=>$value['name'],
					'title'=>$value['title'],
					'description'=>strip_tags($value['description']),
					'currency'=>'INR',
					'price'=>$value['price'],
					'sale_price'=>$value['offer_price'],
					'stock'=>($value['is_stock'] == 'in_stock') ? 'in stock':'out of stock',
					'brand'=>($value['brand']['title']) ?? NULL,
					'category'=>$categoryName,
					'gender'=>$value['gender'],
					'image'=>$value['products_images'][0]['url'] ?? NULL,
					'url'=>'https://www.perfumebooth.com/'.$value['url_key']
				];
			}
		}
		return $productList;
	}
}
