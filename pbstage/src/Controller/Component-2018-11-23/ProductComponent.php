<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\Collection\Collection;

/**
 * Admin component
 */
class ProductComponent extends Component
{	
	public function notifyByEmail($productId=22){
		$status = 0;
		$notifyMeTable = TableRegistry::get('NotifyMe');
		$query = $notifyMeTable->find("all",['fields'=>['email'],'conditions'=>['product_id'=>$productId]])->toArray();
		if( !empty($query) ){
			$emails = array_column($query, 'email');
			$status = 1;
			$productTable = TableRegistry::get('Products');
			$product = $productTable->get($productId, ['contain'=>['ProductsImages'],'fields'=>['id','title','size','size_unit','price','qty']]);
					   /*->contain([
							'ProductsImages' =>[
								'queryBuilder'=> function($q){
									return $q->select(['id','product_id','title','alt_text','img_base'])->where(['ProductsImages.is_base'=>1,'ProductsImages.is_active'=>'active']);
								}
							]	
						]);
					  */	
			pr($product);
		}
		return $status;
	}
	
	public function getBrands(){
		$response = [];
		$dataTable = TableRegistry::get('Brands');
		$query = $dataTable->find('all', ['conditions'=>['Brands.is_active'=>'active']])->toArray();
		if( !empty($query) ){
			foreach($query as $value){
				$response[] = $value->toArray();
			}
		}
		return $response;
    }
	
	public function getBrandByKey($key){
		$response = [];
		$dataTable = TableRegistry::get('CategoriesBrands');
		$query = $dataTable->find('all', ['conditions'=>['CategoriesBrands.url_key'=>$key]])->order(['sort_order'=>'ASC'])->toArray();
		if( !empty($query) ){
			$response = $query[0]->toArray();
		}
		return $response;
    }
	
	public function getBrandsByIds($ids){
		$response = [];
		$dataTable = TableRegistry::get('Brands');
		$query = $dataTable->find('all', ['fields'=>['Brands.id','Brands.title','Brands.country_name','Brands.image','Brands.description'],'conditions'=>['is_active'=>'active']])
				->distinct(['Brands.id'])
				->matching("CategoriesBrands", function($q) use ($ids){
					return $q->select(['category_id','url_key','logo1','logo2','description'])->where(['CategoriesBrands.brand_id IN'=>$ids]);
				})
				->order(['CategoriesBrands.sort_order' => 'ASC'])
				->toArray();
		if(!empty($query)){
			foreach($query as $value){
				$value = $value->toArray();
				$test = $value['_matchingData']['CategoriesBrands'];
				$value['category_id'] = $test['category_id'];
				$value['url_key'] = $test['url_key'];
				$value['logo1'] = $test['logo1'];
				$value['logo2'] = $test['logo2'];
				$value['description'] = !empty($test['description']) ? $test['description'] : $value['description'];
				unset($value['_matchingData']);
				$response[] = $value;
			}
		}
		return $response;
    }
	
	public function getBrandsByCategory($param){
		$response = [];
		$categoryId = $param['categoryId'] ?? 0;
		$brandsId 	= $param['brandsId'] ?? [];
		$returnKey 	= $param['returnKey'] ?? 0;
		
		$productTable = TableRegistry::get('Products');
		$dataTable = TableRegistry::get('Brands');
		$query = $dataTable->find('all', ['fields'=>['id','title','country_name','image','description'],'conditions'=>['Brands.is_active'=>'active']])
				->matching("CategoriesBrands", function($q) use ($categoryId, $brandsId){
					$filterData['CategoriesBrands.category_id'] = $categoryId;
					if( count($brandsId) > 0 ){
						$filterData['CategoriesBrands.brand_id IN'] = $brandsId;
					}
					return $q->select(['category_id','url_key','tag_line','logo1','logo2','description'])->where($filterData);
				})
				->matching("Products", function($q) use ($brandsId){
					$pData['Products.is_active'] = 'active';
					if( count($brandsId) > 0 ){
						$pData['Products.brand_id IN'] = $brandsId;
					}
					return $q->select(['id'])->where($pData);
				})
				->order(['CategoriesBrands.sort_order' => 'ASC'])
				->group(['CategoriesBrands.brand_id'])
				->toArray();

		if(!empty($query)){
			foreach($query as $value){
				$value = $value->toArray();
				$test = $value['_matchingData']['CategoriesBrands'];
				$value['category_id'] = $test['category_id'];
				$value['url_key'] 	  = $test['url_key'];
				$value['tag_line']    = $test['tag_line'];
				$value['logo1']       = $test['logo1'];
				$value['logo2']       = $test['logo2'];
				$value['description'] = !empty($test['description']) ? $test['description'] : $value['description'];
				unset($value['_matchingData']);
				array_values($value);
				$brandId = $value['id'];
				$catId 	 = $value['category_id'];
				//find min start prince
				$query = $productTable->find('all', ['fields'=>['price'],'conditions'=>['brand_id'=>$brandId,'is_active'=>'active'],'limit'=>1,'order'=>['price'=>'asc']])
						->matching("ProductsCategories", function($q) use ($catId){
							return $q->where(['ProductsCategories.category_id'=>$catId]);
						})
						->toArray();
				$value['start_price'] =  ($query[0]->price) ?? 0;
				//find review rating
				$query = $productTable->find();
				$query = $query->select(['sold'=>$query->func()->sum('best_seller')])
					->where(['brand_id'=>$brandId,'is_active'=>'active'])
					->toArray();
				$value['sold'] =  ($query[0]->sold) ?? 0;
				//pr($query);die;
				if( $value['start_price'] > 0 ){
					if( $returnKey ){
						$response[$value['url_key']] = $value;
					}else{
						$response[] = $value;
					}
				}
			}
		}
		return $response;
    }
	
	public function getCategory($categoryIds=[]) {		
		$data = [];
		try{
			$dataTable = TableRegistry::get('Categories');
			$data = $dataTable->find('all', ['fields'=>['id','name','title','image','short_description'],'conditions'=>['Categories.id IN'=>$categoryIds,'Categories.is_active'=>'active']])->hydrate(false)->toArray();
		}catch( \Exception $e){}
		return $data;
    }
	
	public function offerPriceValidity($fromDate='', $toDate=''){
		$status = 0;
		if( !empty($fromDate) && !empty($fromDate) && ($fromDate != 0) && ($fromDate != null) &&  !empty($toDate) && !empty($toDate) && ($toDate != 0) && ($toDate != null) ){
			if( (strtotime($toDate) - strtotime($fromDate)) > 0 ){
				$status = 1;
			}
		}else if( ($fromDate == null) && ($toDate == null)  ){
			$status = 1;
		}		
		return $status;
	}
	
	public function getDetails($param){
		$data = $rProducts = $pCategory = $reviews = $custReviews = $filterData = [];
		$relatedIds = $isContain = 0;
		$cantainCategory = [6,7];
			
		$userId = ( isset($param['userId']) && ($param['userId'] > 0) ) ? $param['userId'] : 0;
		$this->Store 	= new StoreComponent(new ComponentRegistry());
		$cart = $this->Store->getActiveCart($userId);
		$cart = $cart['cart'];
		$cartIds = [];
		if( count($cart) ){ $cartIds = array_column($cart, 'id'); }

		$this->Customer 	= new CustomerComponent(new ComponentRegistry());
		$wishlist = $this->Customer->getWishlist($userId);
		$wishlistIds = [];
		if( count($wishlist) ){ $wishlistIds = array_column($wishlist, 'id'); }

		if( isset($param['id']) && ($param['id'] > 0) ){ $filterData['Products.id'] = $param['id']; }
		if( isset($param['urlKey']) && ($param['urlKey'] != '') ){ $filterData['Products.url_key'] = $param['urlKey']; }
		if( count($filterData) == 0 ){ $filterData['Products.id'] = -1; }
		$filterData['Products.is_active'] = 'active';
		
		$dataTable = TableRegistry::get('Products');
		$query = $dataTable->find('all', ['contain'=>['ProductsCategories','ProductsNotes'],'fields'=>['id','name','title','sku_code','url_key','size','size_unit','price','qty','product_perfume_type','best_seller','tag_line','is_stock','goods_tax','offer_price','offer_from','offer_to','gender','related_ids','short_description','description','meta_title','meta_keyword','meta_description'],'conditions'=>$filterData])
				->contain([
					'Brands' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','title','country_name','image', 'description'])->where(['Brands.is_active'=>'active']);
						}
					]	
				])
			->contain([
				'ProductsImages' =>[
					'queryBuilder'=> function($q){
						return $q->select(['id', 'product_id', 'title', 'alt_text', 'img_thumbnail', 'img_small', 'img_base', 'img_large', 'is_thumbnail', 'is_small', 'is_base', 'is_large'])->where(['ProductsImages.is_active'=>'active', 'ProductsImages.exclude'=>0])->order(['ProductsImages.img_order'=>'ASC']);
					}
				]	
			])
			->toArray(); //pr($query); die;
		if(!empty($query)){
			foreach($query as $value){
				$isContain = 0;
				$images = $notes = $brand = [];
				if($value->brand){
					$brand = [
						'id'=>$value->brand->id,
						'title'=>$value->brand->title,
						'description'=>$value->brand->description,
						'countryName'=>$value->brand->country_name,
						'image'=>$value->brand->image
					];
				}
				foreach($value->products_categories as $v){
					$pCategory[] = $v->category_id;
					if( in_array($v->category_id, $cantainCategory) ){ $isContain = 1; }
				}
				foreach($value->products_images as $v){
					$images[] = [
						'id'=>$v->id,
						'title'=>$v->title,
						'alt'=>$v->alt_text,
						'imgThumbnail'=>$v->img_thumbnail,
						'imgSmall'=>$v->img_small,
						'imgBase'=>$v->img_base,
						'imgLarge'=>$v->img_large,
						'isThumbnail'=>$v->is_thumbnail,
						'isSmall'=>$v->is_small,
						'isBase'=>$v->is_base,
						'isLarge'=>$v->is_large
					];
				}
				foreach($value->products_notes as $v){
					$notes[] = [
						'id'=>$v->id,
						'title'=>$v->title,
						'description'=>$v->description
					];
				}

				$perfumeType = '';
				switch($value->product_perfume_type){
					case 'attar': $perfumeType = 'Attar'; break;
					case 'edp': $perfumeType = 'EDP- Eau De Parfum'; break;
					case 'edt': $perfumeType = 'EDT-Eau De Toilette'; break;
					case 'edit': $perfumeType = ''; break;
					case 'pdt': $perfumeType = 'Parfum De Toilette'; break;
					default:
				}
				$data = [
					'id'=>$value->id,
					'name'=>$value->name,
					'title'=>$value->title,
					'skuCode'=>$value->sku_code,
					'urlKey'=>$value->url_key,
					'tagClass'=>$this->getTagClass($value->tag_line),
					'tagLine'=>empty($value->tag_line)?'':$value->tag_line,
					'size'=>$value->size,
					'sizeUnit'=>$value->size_unit,
					'price'=>$value->price,
					'perfumeType'=>$perfumeType,
					'qty'=>$value->qty,
					'sold'=>$value->best_seller,
					'isStock'=>$value->is_stock,
					'isCart'=>in_array($value->id, $cartIds) ? 1:0,
					'isWishlist'=>in_array($value->id, $wishlistIds) ? 1:0,
					'isContain'=>$isContain,
					'offerPrice'=>$value->offer_price,
					'offerFrom'=>$value->offer_from,
					'offerTo'=>$value->offer_to,
					'offerStatus'=>$this->offerPriceValidity($value->offer_from, $value->offer_to),
					'gender'=>$value->gender,
					'shortDescription'=>$value->short_description,
					'description'=>$value->description,
					'metaTitle'=>$value->meta_title,
					'metaKeyword'=>$value->meta_keyword,
					'metaDescription'=>$value->meta_description,
					'images'=>$images,
					'brand'=>$brand,
					'notes'=>$notes,
					'categories'=>$pCategory,
					'category'=>$this->getCategory($pCategory),
				];
				$relatedIds = $value->related_ids;
			}
		}
			
		if(count($data) > 0){
			$ids = explode(',',$relatedIds);
			if( count($ids) > 0 ){
				$rProducts = $this->getRelatedProduct($ids, $userId);
			}
			if( count($rProducts) == 0 ){
				foreach($data['categories'] as $v){
					$rProducts = $this->getProductByCategory($v, $userId);
					if(count($rProducts > 0)){ break; }
				}
			}
			$reviews		= $this->getProductReviews($data['id']);
			$custReviews 	= $this->totalProductReviews($data['id']);
		}
			
		$data['related'] = $rProducts;
		$data['reviews'] = $reviews;
		$data['custReviews'] = $custReviews;
		return $data;
	}
	
	public function getOfferProducts($filterData, $categoryId=0, $userId=0){
		$data = $pCategory = $reviews = $custReviews = [];

		$this->Store 	= new StoreComponent(new ComponentRegistry());
		$cart = $this->Store->getActiveCart($userId);
		$cart = $cart['cart'];
		$cartIds = [];
		if( count($cart) ){ $cartIds = array_column($cart, 'id'); }

		$this->Customer 	= new CustomerComponent(new ComponentRegistry());
		$wishlist = $this->Customer->getWishlist($userId);
		$wishlistIds = [];
		if( count($wishlist) ){ $wishlistIds = array_column($wishlist, 'id'); }
		
		$filterData['Products.is_active'] = 'active';
		//$filterData['Products.is_stock'] = 'in_stock';
		$dataTable = TableRegistry::get('Products');
		$query = $dataTable->find('all', ['contain'=>['ProductsCategories','ProductsNotes'],'fields'=>['id','name','title','sku_code','url_key','size','size_unit','price','qty','product_perfume_type','best_seller','tag_line','combo_code','refill_code','is_stock','goods_tax','offer_price','offer_from','offer_to','gender','related_ids','short_description','description','meta_title','meta_keyword','meta_description'],'conditions'=>$filterData])
			->contain([
				'Brands' =>[
					'queryBuilder'=> function($q){
						return $q->select(['id','title','country_name','image', 'description'])->where(['Brands.is_active'=>'active']);
					}
				]	
			])
			->contain([
				'ProductsImages' =>[
					'queryBuilder'=> function($q){
						return $q->select(['id', 'product_id', 'title', 'alt_text', 'img_thumbnail', 'img_small', 'img_base', 'img_large', 'is_thumbnail', 'is_small', 'is_base', 'is_large'])->where(['ProductsImages.is_active'=>'active', 'ProductsImages.exclude'=>0])->order(['ProductsImages.img_order'=>'ASC']);
					}
				]	
			]);
		if( $categoryId > 0 ){
			$query = $query->matching("ProductsCategories", function($q) use ($categoryId){
				return $q->where(['ProductsCategories.category_id'=>$categoryId]);
			});
		}		
		$query = $query->order(['Products.is_stock'=>'ASC','Products.sort_order'=>'ASC'])
			->toArray();
			 //pr($query); die;
		if(!empty($query)){
			foreach($query as $value){
				$images = $notes = $brand = [];
				if($value->brand){
					$brand = [
						'id'=>$value->brand->id,
						'title'=>$value->brand->title,
						'description'=>$value->brand->description,
						'countryName'=>$value->brand->country_name,
						'image'=>$value->brand->image
					];
				}
				foreach($value->products_categories as $v){
					$pCategory[] = $v->category_id;
				}
				foreach($value->products_images as $v){
					$images[] = [
						'id'=>$v->id,
						'title'=>$v->title,
						'alt'=>$v->alt_text,
						'imgThumbnail'=>$v->img_thumbnail,
						'imgSmall'=>$v->img_small,
						'imgBase'=>$v->img_base,
						'imgLarge'=>$v->img_large,
						'isThumbnail'=>$v->is_thumbnail,
						'isSmall'=>$v->is_small,
						'isBase'=>$v->is_base,
						'isLarge'=>$v->is_large
					];
				}
				foreach($value->products_notes as $v){
					$notes[] = [
						'id'=>$v->id,
						'title'=>$v->title,
						'description'=>$v->description
					];
				}

				$perfumeType = '';
				switch($value->product_perfume_type){
					case 'attar': $perfumeType = 'Attar'; break;
					case 'edp': $perfumeType = 'EDP- Eau De Parfum'; break;
					case 'edt': $perfumeType = 'EDT-Eau De Toilette'; break;
					case 'edit': $perfumeType = ''; break;
					case 'pdt': $perfumeType = 'Parfum De Toilette'; break;
					default:
				}
				$rProducts = [];
				$ids = explode(',', $value->related_ids);
				if( count($ids) > 0 ){
					$rProducts = $this->getRelatedProduct($ids);
				}
				/*
				if( count($rProducts) == 0 ){
					foreach($pCategory as $v){
						$rProducts = $this->getProductByCategory($v);
						if(count($rProducts > 0)){ break; }
					}
				}*/
				$data[] = [
					'id'=>$value->id,
					'name'=>$value->name,
					'title'=>$value->title,
					'skuCode'=>$value->sku_code,
					'urlKey'=>$value->url_key,
					'tagClass'=>$this->getTagClass($value->tag_line),
					'tagLine'=>empty($value->tag_line)?'':$value->tag_line,
					'size'=>$value->size,
					'sizeUnit'=>$value->size_unit,
					'price'=>$value->price,
					'offerPrice'=>$value->offer_price,
					'perfumeType'=>$perfumeType,
					'qty'=>$value->qty,
					'sold'=>$value->best_seller,
					'comboCode'=>!empty($value->combo_code) ? $value->combo_code:'',
					'refillCode'=>!empty($value->refill_code) ? $value->refill_code:'',
					'isStock'=>$value->is_stock,
					'isCart'=>in_array($value->id, $cartIds) ? 1:0,
					'isWishlist'=>in_array($value->id, $wishlistIds) ? 1:0,
					'offerPrice'=>$value->offer_price,
					'offerFrom'=>$value->offer_from,
					'offerTo'=>$value->offer_to,
					'gender'=>$value->gender,
					'shortDescription'=>$value->short_description,
					'description'=>$value->description,
					'metaTitle'=>$value->meta_title,
					'metaKeyword'=>$value->meta_keyword,
					'metaDescription'=>$value->meta_description,
					'images'=>$images,
					'brand'=>$brand,
					'notes'=>$notes,
					'categories'=>$pCategory,
					'related'=>$rProducts,
					'reviews'=>$this->getProductReviews($value->id),
					'custReviews'=>$this->totalProductReviews($value->id)
				];
			}
		}
		//pr($data);die;
		return $data;
	}
	
	public function getProductByCategory($categoryId, $userId=0, $limit=20) {
		$data = [];
		$this->Store 	= new StoreComponent(new ComponentRegistry());
		$cart = $this->Store->getActiveCart($userId);
		$cart = $cart['cart'];
		$cartIds = [];
		if( count($cart) ){ $cartIds = array_column($cart, 'id'); }

		$dataTable = TableRegistry::get('Products');
		$query = $dataTable->find('all', ['fields'=>['id','name','title','sku_code','url_key','size','size_unit','best_seller','tag_line','price','is_stock','goods_tax','offer_price','offer_from','offer_to','gender','short_description'],'limit'=>$limit,'conditions'=>['Products.is_active'=>'active']])
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
				->matching("ProductsCategories", function($q) use ($categoryId){
					return $q->where(['ProductsCategories.category_id'=>$categoryId]);
				})
				->order(['Products.is_stock'=>'ASC','Products.sort_order'=>'ASC'])
				->toArray();
		//pr($query);
		foreach($query as $value){
			$images = [];
			foreach($value->products_images as $v){
				$images = [
					'id'=>$v->id,
					'alt'=>$v->alt_text,
					'title'=>$v->title,
					'url'=>$v->img_base
				];
			}
			$brand = [];
			if( !empty($value->brand) ){
				$brand = [
					'id'			=> $value->brand->id,
					'title'			=> $value->brand->title,
					'image'			=> $value->brand->image,
					'description'	=> $value->brand->description,
				];
			}
			$tax = explode('_',$value->goods_tax);
			
			$data[] = [
				'id'=>$value->id,
				'name'=>$value->name,
				'title'=>$value->title,
				'skuCode'=>$value->sku_code,
				'urlKey'=>$value->url_key,
				'tagClass'=>$this->getTagClass($value->tag_line),
				'tagLine'=>empty($value->tag_line)?'':$value->tag_line,
				'size'=>$value->size,
				'sizeUnit'=>$value->size_unit,
				'sold'=>$value->best_seller,
				'price'=>$value->price,
				'isStock'=>$value->is_stock,
				'isCart'=>in_array($value->id, $cartIds) ? 1:0,
				'taxTitle'=>$tax[0],
				'taxValue'=>$tax[1],
				'taxType'=>$tax[2],
				'offerPrice'=>$value->offer_price,
				'offerFrom'=>$value->offer_from,
				'offerTo'=>$value->offer_to,
				'gender'=>$value->gender,
				'description'=>$value->short_description,
				'brand'=>$brand,
				'images'=>$images,
			    'reviews'=>$this->getProductReviews($value->id),
				'custReviews'=>$this->totalProductReviews($value->id)

			];
		}
		return $data;
    }
	
	public function getRelatedProduct($ids=[], $userId=0){
		$data = [];
		$this->Store 	= new StoreComponent(new ComponentRegistry());
		$cart = $this->Store->getActiveCart($userId);
		$cart = $cart['cart'];
		$cartIds = [];
		if( count($cart) ){ $cartIds = array_column($cart, 'id'); }

		$dataTable = TableRegistry::get('Products');
		$query = $dataTable->find('all', ['fields'=>['id','name','title','sku_code','url_key','size','size_unit','best_seller','tag_line','price','is_stock','goods_tax','offer_price','offer_from','offer_to','gender','short_description'],'conditions'=>['Products.id IN'=>$ids,'Products.is_active'=>'active']])
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
							return $q->select(['id','product_id','title','alt_text','img_large'])->where(['ProductsImages.is_large'=>1,'ProductsImages.is_active'=>'active']);
						}
					]	
				])
				->order(['Products.sort_order'=>'ASC'])
				->toArray();
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
			$brand = [];
			if( !empty($value->brand) ){
				$brand = [
					'id'			=> $value->brand->id,
					'title'			=> $value->brand->title,
					'image'			=> $value->brand->image,
					'description'	=> $value->brand->description,
				];
			}
			$tax = explode('_',$value->goods_tax);

			$data[] = [
				'id'=>$value->id,
				'name'=>$value->name,
				'title'=>$value->title,
				'skuCode'=>$value->sku_code,
				'urlKey'=>$value->url_key,
				'tagClass'=>$this->getTagClass($value->tag_line),
				'tagLine'=>empty($value->tag_line)?'':$value->tag_line,
				'size'=>$value->size,
				'sizeUnit'=>$value->size_unit,
				'sold'=>$value->best_seller,
				'price'=>$value->price,
				'isStock'=>$value->is_stock,
				'isCart'=>in_array($value->id, $cartIds) ? 1:0,
				'taxTitle'=>$tax[0],
				'taxValue'=>$tax[1],
				'taxType'=>$tax[2],
				'offerPrice'=>$value->offer_price,
				'offerFrom'=>$value->offer_from,
				'offerTo'=>$value->offer_to,
				'gender'=>$value->gender,
				'description'=>$value->short_description,
				'brand'=>$brand,
				'images'=>$images,
			    'reviews'=>$this->getProductReviews($value->id),
				'custReviews'=>$this->totalProductReviews($value->id)
			];
		}
		return $data;
    }

	public function getProductReviews($productId, $limit=10, $page=1){
		$data = [];
		$dataTable = TableRegistry::get('Reviews');
		$query = $dataTable->find('all', ['fields'=>['id','title','customer_name','description','rating','created'],'conditions'=>['Reviews.product_id'=>$productId,'Reviews.is_active'=>'approved']])
				->contain([
					'Customers' =>[
						'queryBuilder'=> function($q){
							return $q->select(['id','firstname','lastname','image']);
						}
					]	
				])
				->order(['Reviews.created'=>'DESC'])
				->limit($limit)
				->page($page)
				->toArray(); //pr($query); die;
		foreach($query as $value){			
			$customer = [];
			if($value->customer){
				$customer = [
					'id'=>$value->customer->id,
					'name'=>!empty($value->customer_name) ? $value->customer_name:$value->customer->firstname.' '.$value->customer->lastname,
					'image'=>$value->customer->image
				];
			}
			$data[] = [
				'id'=>$value->id,
				'title'=>$value->title,
				'description'=>$value->description,
				'rating'=>$value->rating,
				'created'=>date('F j, Y h:i:s A',strtotime($value->created)),
				'customer'=>$customer
			];
		}		
		return $data;
	}
	
	public function totalProductReviews($productId){
		$total = $rating = 0;
		$query = TableRegistry::get('Reviews')->find();
		$query = $query->select(['total'=>$query->func()->count('*'),'totalRating'=>$query->func()->sum('rating')])
				->group(['customer_id'])
				->where(['product_id'=>$productId,'is_active'=>'approved'])
				->toArray();
		$customers = count($query);
		foreach($query as $value){
			$total = $total + $value->total;
			$rating = $rating + $value->totalRating;
		}
		$rating = ($total > 0) ? ceil($rating/$total):0;
		return ['customers'=>$customers, 'rating'=>$rating];
	}
	
	public function getProductImages($id=0){
		$data = [];
		$dataTable = TableRegistry::get('ProductsImages');
		$query = $dataTable->find('all', ['fields'=>['id', 'product_id', 'img_order', 'title', 'alt_text', 'img_thumbnail', 'img_small', 'img_base', 'img_large', 'is_thumbnail', 'is_small', 'is_base', 'is_large','exclude','is_active'],'conditions'=>['product_id'=>$id]])
				->toArray();
		foreach($query as $v){
			$data[] = [
						'id'=>$v->id,
						'imgOrder'=>$v->img_order,
						'title'=>$v->title,
						'alt'=>$v->alt_text,
						'imgThumbnail'=>$v->img_thumbnail,
						'imgSmall'=>$v->img_small,
						'imgBase'=>$v->img_base,
						'imgLarge'=>$v->img_large,
						'isThumbnail'=>$v->is_thumbnail,
						'isSmall'=>$v->is_small,
						'isBase'=>$v->is_base,
						'isLarge'=>$v->is_large,
						'exclude'=>$v->exclude,
						'isActive'=>$v->is_active,
			];
		}
		return $data;
    }

	public function getTagClass($tagline){
		$class = '';
		switch($tagline){
			case 'Best Seller': $class = ''; break;
			case 'Money': $class = 'corner_money'; break;
			case 'New': $class = 'corner_new'; break;
			case 'Premium': $class = 'corner_premium'; break;
			case 'Trending': $class = 'corner_trending'; break;
			default: //Best Seller
		}
		return $class;
	}

	public function getDiscounts($price){
		$discount = 20;
		$discountedPrice = ($price - ($price*$discount)/100);
		return ['discount'=>$discount,'price'=>$discountedPrice,'label'=>'20% Off'];
	}
	
}
