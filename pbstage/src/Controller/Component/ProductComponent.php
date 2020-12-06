<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

/**
 * Admin component
 */
class ProductComponent extends Component
{
    public function findUrlRewrite($key){
        $dataTable = TableRegistry::get('UrlRewrite');
        $query = $dataTable->find('all',['conditions'=>['request_path'=>$key]])->hydrate(0)->toArray();
        return empty($query) ? 0:1;
    }
    
    public function updateUrlRewrite($param){
        $addAction = 0;
        $oldKey    = $param['old_key'];
        $newKey    = $param['new_key'];
        $dataTable = TableRegistry::get('UrlRewrite');
        if( !empty($oldKey) && ( $oldKey != $newKey ) ){
            $dataTable->query()->update()->set(['request_path'=>$newKey])->where(['request_path'=>$oldKey])->execute();
        }else if(empty($oldKey)) {
            $addAction = 1;
        }else{
            if( $this->findUrlRewrite($newKey) == 0 ){ $addAction = 1; }
        }
        if( $addAction ){
            $row = $dataTable->newEntity();
            $row->id_path = $param['type'];
            $row->request_path = $newKey;
            $dataTable->save($row);
        } //pr($param); die;
        return true;
    }

    public function notifyByEmail($productId = 22)
    {
        $status = 0;
        $notifyMeTable = TableRegistry::get('NotifyMe');
        $query = $notifyMeTable->find("all", ['fields' => ['email'], 'conditions' => ['product_id' => $productId]])->toArray();
        if (!empty($query)) {
            $emails = array_column($query, 'email');
            $status = 1;
            $productTable = TableRegistry::get('Products');
            $product = $productTable->get($productId, ['contain' => ['ProductsImages'], 'fields' => ['id', 'title', 'size', 'size_unit', 'price', 'qty']]);
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

    public function getBrands()
    {
        $response = [];
        $dataTable = TableRegistry::get('Brands');
        $query = $dataTable->find('all', ['conditions' => ['Brands.is_active' => 'active']])->toArray();
        if (!empty($query)) {
            foreach ($query as $value) {
                $response[] = $value->toArray();
            }
        }
        return $response;
    }

    public function getBrandByKey($key)
    {
        $response = [];
        $dataTable = TableRegistry::get('CategoriesBrands');
        $query = $dataTable->find('all', ['conditions' => ['CategoriesBrands.url_key' => $key]])->order(['sort_order' => 'ASC'])->toArray();
        if (!empty($query)) {
            $response = $query[0]->toArray();
        }
        return $response;
    }

    public function getBrandsByIds($ids)
    {
        $response = [];
        $dataTable = TableRegistry::get('Brands');
        $query = $dataTable->find('all', ['fields' => ['Brands.id', 'Brands.title', 'Brands.country_name', 'Brands.image', 'Brands.description'], 'conditions' => ['is_active' => 'active']])
            ->distinct(['Brands.id'])
            ->matching("CategoriesBrands", function ($q) use ($ids) {
                return $q->select(['category_id', 'url_key', 'logo1', 'logo2', 'description'])->where(['CategoriesBrands.brand_id IN' => $ids]);
            })
            ->order(['CategoriesBrands.sort_order' => 'ASC'])
            ->toArray();
        if (!empty($query)) {
            foreach ($query as $value) {
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

    public function getBrandsByCategory($param)
    {
        $response = [];
        $categoryId = $param['categoryId'] ?? 0;
        $brandsId = $param['brandsId'] ?? [];
        $returnKey = $param['returnKey'] ?? 0;

        $productTable = TableRegistry::get('Products');
        $dataTable = TableRegistry::get('Brands');
        $query = $dataTable->find('all', ['fields' => ['id', 'title', 'country_name', 'image', 'description'], 'conditions' => ['Brands.is_active' => 'active']])
            ->matching("CategoriesBrands", function ($q) use ($categoryId, $brandsId) {
                $filterData['CategoriesBrands.category_id'] = $categoryId;
                if (count($brandsId) > 0) {
                    $filterData['CategoriesBrands.brand_id IN'] = $brandsId;
                }
                return $q->select(['category_id', 'url_key', 'tag_line', 'logo1', 'logo2', 'description'])->where($filterData);
            })
            ->matching("Products", function ($q) use ($brandsId) {
                $pData['Products.is_active'] = 'active';
                $pData['Products.is_stock'] = 'in_stock';
                if (count($brandsId) > 0) {
                    $pData['Products.brand_id IN'] = $brandsId;
                }
                return $q->select(['id'])->where($pData);
            })
            ->order(['CategoriesBrands.sort_order' => 'ASC'])
            ->group(['CategoriesBrands.brand_id'])
            ->toArray();

        if (!empty($query)) {
            foreach ($query as $value) {
                $value = $value->toArray();
                $test = $value['_matchingData']['CategoriesBrands'];
                $value['category_id'] = $test['category_id'];
                $value['url_key'] = $test['url_key'];
                $value['tag_line'] = $test['tag_line'];
                $value['logo1'] = $test['logo1'];
                $value['logo2'] = $test['logo2'];
                $value['description'] = !empty($test['description']) ? $test['description'] : $value['description'];
                unset($value['_matchingData']);
                array_values($value);
                $brandId = $value['id'];
                $catId = $value['category_id'];
                //find min start prince
                $query = $productTable->find('all', ['fields' => ['price'], 'conditions' => ['brand_id' => $brandId, 'is_active' => 'active'], 'limit' => 1, 'order' => ['price' => 'asc']])
                    ->matching("ProductsCategories", function ($q) use ($catId) {
                        return $q->where(['ProductsCategories.category_id' => $catId]);
                    })
                    ->toArray();
                $value['start_price'] = ($query[0]->price) ?? 0;
                //find review rating
                $query = $productTable->find();
                $query = $query->select(['sold' => $query->func()->sum('best_seller')])
                    ->where(['brand_id' => $brandId, 'is_active' => 'active'])
                    ->toArray();
                $value['sold'] = ($query[0]->sold) ?? 0;
                //pr($query);die;
                if ($value['start_price'] > 0) {
                    if ($returnKey) {
                        $response[$value['url_key']] = $value;
                    } else {
                        $response[] = $value;
                    }
                }
            }
        }
        return $response;
    }

    public function getCategory($categoryIds = [])
    {
        $data = [];
        try {
            $dataTable = TableRegistry::get('Categories');
            $data = $dataTable->find('all', ['fields' => ['id', 'name', 'title', 'image', 'short_description'], 'conditions' => ['Categories.id IN' => $categoryIds, 'Categories.is_active' => 'active']])->hydrate(false)->toArray();
        } catch (\Exception $e) {}
        return $data;
    }

    public function offerPriceValidity($fromDate = '', $toDate = '')
    {
        $status = 0;
        if (!empty($fromDate) && !empty($fromDate) && ($fromDate != 0) && ($fromDate != null) && !empty($toDate) && !empty($toDate) && ($toDate != 0) && ($toDate != null)) {
            if ((strtotime($toDate) - strtotime($fromDate)) > 0) {
                $status = 1;
            }
        } else if (($fromDate == null) && ($toDate == null)) {
            $status = 1;
        }
        return $status;
    }

    public function getPages($param)
    {
        $data = ['related'=>[],'reviews'=>[],'custReviews'=>[]];
        $cantainCategory = [6,7];
        $userId = (isset($param['userId']) && ($param['userId'] > 0)) ? $param['userId'] : 0;
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cartIds = array_column($cart['cart'], 'id');

        $this->Customer = new CustomerComponent(new ComponentRegistry());
        $wishlist = $this->Customer->getWishlist($userId);
        $wishlistIds = count($wishlist) ? array_column($wishlist, 'id') : [];
        if (isset($param['urlKey']) && ($param['urlKey'] != '')) {
            $urlRewrite = TableRegistry::get('UrlRewrite')->find('all',['fields'=>['id_path'],'conditions'=>['request_path'=>$param['urlKey']]])->hydrate(0)->toArray();
            //pr($urlRewrite); die;
            if( !empty($urlRewrite) ){                
                switch($urlRewrite[0]['id_path']){
                    case 'category':
                        break;
                    case 'static':
                        $page = $param['page'] ?? 1;
                        $data = TableRegistry::get('Cms')->find('all',['fields'=>['conditions','id','title','content','image','is_amp','metaTitle'=>'meta_title','metaKeyword'=>'meta_keyword','metaDescription'=>'meta_description'],'conditions'=>['is_active'=>'active','url_key'=>$param['urlKey']]])->hydrate(0)->toArray();
                        if(!empty($data)){
                            $data = $data[0];
                            $conditions = json_decode($data['conditions'],true);
                            $priceFrom = $conditions['prices'][0] ?? 0;
                            $priceTo = $conditions['prices'][1] ?? 2000000;
                            $categoryIds = $conditions['categories'] ?? [];                            
                            $brandIds = $conditions['brands'] ?? [];
                            $sku = $conditions['sku'] ?? '';
                            $sku = explode(",", $sku);
                            //pr($sku);
                            $related = TableRegistry::get('Products')->find('all', ['fields' => ['id', 'name', 'title', 'skuCode'=>'sku_code', 'urlKey'=>'url_key', 'size', 'sizeUnit'=>'size_unit', 'price', 'qty', 'sold'=>'best_seller', 'tagLine'=>'Products.tag_line', 'isStock'=>'is_stock', 'offerPrice'=>'offer_price', 'offerFrom'=>'offer_from', 'offerTo'=>'offer_to', 'gender'], 'conditions' => ['Products.is_active'=>'active']])
                            ->contain([
                                'ProductsImages' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'url'=>'img_large'])->where(['ProductsImages.is_large' => 1, 'ProductsImages.is_active' => 'active', 'ProductsImages.exclude' => 0])->order(['ProductsImages.img_order' => 'ASC']);
                                    },
                                ],
                                'ProductsNotes' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['product_id', 'id', 'title', 'description'])->where(['ProductsNotes.is_active' => 'active']);
                                    },
                                ],
                                'Brands' => [
                                    'queryBuilder' => function ($q) {
                                        return $q->select(['id', 'title', 'country_name', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                                    },
                                ]
                            ])
                            ->where(['Products.id NOT IN'=>[558]]) // enter product id
                            ->where(function ($q) use ($priceFrom, $priceTo) {
                                return $q->between('Products.price', $priceFrom, $priceTo);
                            });
                            if(!empty($sku[0])){
                                $related = $related->where(['Products.sku_code IN'=>$sku]);
                            }
                            if(!empty($brandIds)){
                                $related = $related->where(['Products.brand_id IN'=>$brandIds]);
                            }
                            if(!empty($categoryIds)){
                                $related = $related->matching("ProductsCategories", function ($q) use ($categoryIds) {
                                    return $q->where(['ProductsCategories.category_id IN' => $categoryIds]);
                                });
                            }
                            $related =  $related->page($page)->limit(50)->hydrate(0)->toArray(); 
                            //pr($related); die;
                            $data['related'] = array_map(function($v){
                                $product = array_slice($v,0,17); 
                                $product['notes'] = array_map(function($notes){ return array_slice($notes,1); },$v['products_notes']);
                                $product['images'] = array_map(function($images){ return array_slice($images,1); },$v['products_images']);
                                $product['images'] = $product['images'][0] ?? [];
                                $product['tagClass'] = $this->getTagClass($v['tagLine']);
                                return $product;
                            },$related);
                            //$title = (count($related) > 1) ? $data['title'].' - '.count($related).' Products' : $data['title'];
                            $data['content'] = str_replace("{{PageTitle}}", $data['title'], $data['content']);
                            //$data['content'] = str_replace("{{CmsProduct}}", '<app-cms-product [productList]="productList"></app-cms-product>', $data['content']);
                            $data['pageType'] = 'static';
                            $data['paymentOffer'] = $this->Store->getWebMessage();
                            $data = array_slice($data,1);
                        }                
                        break;
                    case 'product':
                        $query = TableRegistry::get('Products')->find('all', ['contain' => ['ProductsCategories'], 'fields' => ['related_ids', 'product_perfume_type','id', 'name', 'title', 'skuCode'=>'sku_code', 'urlKey'=>'url_key', 'size', 'sizeUnit'=>'size_unit', 'price', 'qty', 'sold'=>'best_seller', 'tagLine'=>'Products.tag_line', 'isStock'=>'is_stock', 'offerPrice'=>'offer_price', 'offerFrom'=>'offer_from', 'offerTo'=>'offer_to', 'gender', 'shortDescription'=>'short_description', 'description', 'metaTitle'=>'meta_title', 'metaKeyword'=>'meta_keyword', 'metaDescription'=>'meta_description'], 'conditions' => ['Products.is_active'=>'active','Products.url_key'=>$param['urlKey']]])
                        ->contain([
                            'Brands' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['id', 'title', 'country_name', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                                },
                            ],
                            'ProductsImages' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'imgThumbnail'=>'img_thumbnail', 'imgSmall'=>'img_small', 'imgBase'=>'img_base', 'imgLarge'=>'img_large', 'isThumbnail'=>'is_thumbnail', 'isSmall'=>'is_small', 'isBase'=>'is_base', 'isLarge'=>'is_large'])->where(['ProductsImages.is_active' => 'active', 'ProductsImages.exclude' => 0])->order(['ProductsImages.img_order' => 'ASC']);
                                },
                            ],
                            'ProductsNotes' => [
                                'queryBuilder' => function ($q) {
                                    return $q->select(['product_id', 'id', 'title', 'description'])->where(['ProductsNotes.is_active' => 'active']);
                                },
                            ]
                        ])->limit(1)->hydrate(0)->toArray(); //pr($query); die;

                        $data = array_map(function($v) use ($userId, $cantainCategory, $cartIds, $wishlistIds){
                            switch ($v['product_perfume_type']) {
                                case 'attar':$perfumeType = 'Attar';
                                    break;
                                case 'edp':$perfumeType = 'EDP- Eau De Parfum';
                                    break;
                                case 'edt':$perfumeType = 'EDT-Eau De Toilette';
                                    break;
                                case 'edit':$perfumeType = '';
                                    break;
                                case 'pdt':$perfumeType = 'Parfum De Toilette';
                                    break;
                                default: $perfumeType = '';
                            }
                            $pCategory = array_column($v['products_categories'],'category_id');
                            $v['perfumeType'] = $perfumeType;
                            $v['isCart'] = in_array($v['id'], $cartIds) ? 1 : 0;
                            $v['isWishlist'] = in_array($v['id'], $wishlistIds) ? 1 : 0;
                            $v['isContain'] = empty(array_intersect($cantainCategory, $pCategory)) ? 0:1;
                            $v['tagClass'] = $this->getTagClass($v['tagLine']);
                            $v['offerStatus'] = $this->offerPriceValidity($v['offerFrom'], $v['offerTo']);
                            $v['images'] = array_map(function($images){return array_slice($images,1);},$v['products_images']);
                            $v['notes'] = array_map(function($notes){ return array_slice($notes,1);},$v['products_notes']);
                            $v['categories'] = $pCategory;
                            $v['category'] = $this->getCategory($pCategory);
                            $ids = explode(',', $v['related_ids']);
                            $rProducts = [];
                            if (count($ids) > 0) {
                                $rProducts = $this->getRelatedProduct($ids, $userId);
                            }
                            if (count($rProducts) == 0) {
                                foreach ($v['categories'] as $rp) {
                                    $rProducts = $this->getProductByCategory($rp, $userId);
                                    if (count($rProducts) > 0) {break;}
                                }
                            }
                            $v['related'] = $rProducts;
                            $v['reviews'] = $this->getProductReviews($v['id']);
                            $v['progressRating'] = $this->getReviewRatingProgress($v['id']);
                            $v['custReviews'] = $this->totalProductReviews($v['id']);
                            $v = array_slice($v,2);
                            unset($v['products_images'],$v['products_notes'],$v['products_categories']);
                            $v['pageType'] = 'product';
                            $v['paymentOffer'] = $this->Store->getWebMessage();
                            return $v;
                        },$query);
                        $data = count($data) ? $data[0] : ['related'=>[],'reviews'=>[],'custReviews'=>[]];
                        break;
                    default:    
                }
            }            
        } //pr($data); die;
        return $data;
    }

    public function getAmpPages($param)
    {
        $data = ['related'=>[],'reviews'=>[],'custReviews'=>[]];
        if (isset($param['urlKey']) && ($param['urlKey'] != '')) {
            $urlRewrite = TableRegistry::get('UrlRewrite')->find('all',['fields'=>['id_path'],'conditions'=>['request_path'=>$param['urlKey']]])->hydrate(0)->toArray();
            if( !empty($urlRewrite) ){
                $data = TableRegistry::get('Cms')->find('all',['fields'=>['conditions','id','title','image','metaTitle'=>'meta_title','metaKeyword'=>'meta_keyword','metaDescription'=>'meta_description'],'conditions'=>['is_active'=>'active','url_key'=>$param['urlKey']]])->hydrate(0)->toArray();
                if(!empty($data)){
                    $data = $data[0];
                    $conditions = json_decode($data['conditions'],true);
                    $priceFrom = $conditions['prices'][0] ?? 0;
                    $priceTo = $conditions['prices'][1] ?? 2000000;
                    $categoryIds = $conditions['categories'] ?? [];                            
                    $brandIds = $conditions['brands'] ?? [];
                    $sku = $conditions['sku'] ?? '';
                    $sku = explode(",", $sku);
                    //pr($sku);
                    $related = TableRegistry::get('Products')->find('all', ['fields' => ['id', 'name', 'title', 'skuCode'=>'sku_code', 'urlKey'=>'url_key', 'size', 'sizeUnit'=>'size_unit', 'price', 'qty', 'sold'=>'best_seller', 'tagLine'=>'Products.tag_line', 'isStock'=>'is_stock', 'offerPrice'=>'offer_price', 'offerFrom'=>'offer_from', 'offerTo'=>'offer_to', 'gender'], 'conditions' => ['Products.is_active'=>'active']])
                    ->contain([
                        'ProductsImages' => [
                            'queryBuilder' => function ($q) {
                                return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'url'=>'img_large'])->where(['ProductsImages.is_large' => 1, 'ProductsImages.is_active' => 'active', 'ProductsImages.exclude' => 0])->order(['ProductsImages.img_order' => 'ASC']);
                            },
                        ],
                        'ProductsNotes' => [
                            'queryBuilder' => function ($q) {
                                return $q->select(['product_id', 'id', 'title', 'description'])->where(['ProductsNotes.is_active' => 'active']);
                            },
                        ],
                        'Brands' => [
                            'queryBuilder' => function ($q) {
                                return $q->select(['id', 'title', 'country_name', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                            },
                        ]
                    ])
                    ->where(['Products.id NOT IN'=>[558]]) // enter product id
                    ->where(function ($q) use ($priceFrom, $priceTo) {
                        return $q->between('Products.price', $priceFrom, $priceTo);
                    });
                    if(!empty($sku[0])){
                        $related = $related->where(['Products.sku_code IN'=>$sku]);
                    }
                    if(!empty($brandIds)){
                        $related = $related->where(['Products.brand_id IN'=>$brandIds]);
                    }
                    if(!empty($categoryIds)){
                        $related = $related->matching("ProductsCategories", function ($q) use ($categoryIds) {
                            return $q->where(['ProductsCategories.category_id IN' => $categoryIds]);
                        });
                    }
                    $related =  $related->page(1)->limit(4)->hydrate(0)->toArray(); 
                    //pr($related); die;
                    $data['related'] = array_map(function($v){
                        $product = array_slice($v,0,17); 
                        $product['notes'] = array_map(function($notes){ return array_slice($notes,1); },$v['products_notes']);
                        $product['images'] = array_map(function($images){ return array_slice($images,1); },$v['products_images']);
                        $product['images'] = $product['images'][0] ?? [];
                        $product['tagClass'] = $this->getTagClass($v['tagLine']);
                        return $product;
                    },$related);
                    $data = array_slice($data,1);
                }                
            }            
        } pr($data); die;
        return $data;
    }

    public function getDetails($param)
    {
        $cantainCategory = [6, 7];
        $userId = (isset($param['userId']) && ($param['userId'] > 0)) ? $param['userId'] : 0;
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cartIds = array_column($cart['cart'], 'id');

        $this->Customer = new CustomerComponent(new ComponentRegistry());
        $wishlist = $this->Customer->getWishlist($userId);
        $wishlistIds = [];
        if (count($wishlist)) {$wishlistIds = array_column($wishlist, 'id');}

        $data = TableRegistry::get('Products')->find('all', ['contain' => ['ProductsCategories'], 'fields' => ['related_ids', 'product_perfume_type','id', 'name', 'title', 'skuCode'=>'sku_code', 'urlKey'=>'url_key', 'size', 'sizeUnit'=>'size_unit', 'price', 'qty', 'sold'=>'best_seller', 'tagLine'=>'Products.tag_line', 'isStock'=>'is_stock', 'offerPrice'=>'offer_price', 'offerFrom'=>'offer_from', 'offerTo'=>'offer_to', 'gender', 'shortDescription'=>'short_description', 'description', 'metaTitle'=>'meta_title', 'metaKeyword'=>'meta_keyword', 'metaDescription'=>'meta_description'], 'conditions' => ['Products.is_active'=>'active','Products.url_key'=>$param['urlKey']]])
        ->contain([
            'Brands' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id', 'title', 'country_name', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                },
            ],
            'ProductsImages' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'imgThumbnail'=>'img_thumbnail', 'imgSmall'=>'img_small', 'imgBase'=>'img_base', 'imgLarge'=>'img_large', 'isThumbnail'=>'is_thumbnail', 'isSmall'=>'is_small', 'isBase'=>'is_base', 'isLarge'=>'is_large'])->where(['ProductsImages.is_active' => 'active', 'ProductsImages.exclude' => 0])->order(['ProductsImages.img_order' => 'ASC']);
                },
            ],
            'ProductsNotes' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['product_id', 'id', 'title', 'description'])->where(['ProductsNotes.is_active' => 'active']);
                },
            ]
        ])->limit(1)->hydrate(0)->toArray(); //pr($query); die;

        $data = array_map(function($v) use ($userId, $cantainCategory, $cartIds, $wishlistIds){
            switch ($v['product_perfume_type']) {
                case 'attar':$perfumeType = 'Attar';
                    break;
                case 'edp':$perfumeType = 'EDP- Eau De Parfum';
                    break;
                case 'edt':$perfumeType = 'EDT-Eau De Toilette';
                    break;
                case 'edit':$perfumeType = '';
                    break;
                case 'pdt':$perfumeType = 'Parfum De Toilette';
                    break;
                default: $perfumeType = '';
            }
            $pCategory = array_column($v['products_categories'],'category_id');
            $v['perfumeType'] = $perfumeType;
            $v['isCart'] = in_array($v['id'], $cartIds) ? 1 : 0;
            $v['isWishlist'] = in_array($v['id'], $wishlistIds) ? 1 : 0;
            $v['isContain'] = empty(array_intersect($cantainCategory, $pCategory)) ? 0:1;
            $v['tagClass'] = $this->getTagClass($v['tagLine']);
            $v['offerStatus'] = $this->offerPriceValidity($v['offerFrom'], $v['offerTo']);
            $v['images'] = array_map(function($images){return array_slice($images,1);},$v['products_images']);
            $v['notes'] = array_map(function($notes){ return array_slice($notes,1);},$v['products_notes']);
            $v['categories'] = $pCategory;
            $v['category'] = $this->getCategory($pCategory);
            $ids = explode(',', $v['related_ids']);
            $rProducts = [];
            if (count($ids) > 0) {
                $rProducts = $this->getRelatedProduct($ids, $userId);
            }
            if (count($rProducts) == 0) {
                foreach ($v['categories'] as $rp) {
                    $rProducts = $this->getProductByCategory($rp, $userId);
                    if (count($rProducts) > 0) {break;}
                }
            }
            $v['related'] = $rProducts;
            $v['reviews'] = $this->getProductReviews($v['id']);
            $v['progressRating'] = $this->getReviewRatingProgress($v['id']);
            $v['custReviews'] = $this->totalProductReviews($v['id']);
            $v = array_slice($v,2);
            unset($v['products_images'],$v['products_notes'],$v['products_categories']);
            $v['pageType'] = 'product';
            $v['paymentOffer'] = $this->Store->getWebMessage();
            return $v;
        },$data);
        return count($data) ? $data[0] : ['related'=>[],'reviews'=>[],'custReviews'=>[]];
    }

    public function getOfferProducts($filterData, $categoryId = 0, $userId = 0)
    {
        $data = $pCategory = $reviews = $custReviews = [];

        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cart = $cart['cart'];
        $cartIds = [];
        if (count($cart)) {$cartIds = array_column($cart, 'id');}

        $this->Customer = new CustomerComponent(new ComponentRegistry());
        $wishlist = $this->Customer->getWishlist($userId);
        $wishlistIds = [];
        if (count($wishlist)) {$wishlistIds = array_column($wishlist, 'id');}

        $filterData['Products.is_active'] = 'active';
        //$filterData['Products.is_stock'] = 'in_stock';
        $dataTable = TableRegistry::get('Products');
        $query = $dataTable->find('all', ['contain' => ['ProductsCategories', 'ProductsNotes'], 'fields' => ['id', 'name', 'title', 'sku_code', 'url_key', 'size', 'size_unit', 'price', 'qty', 'product_perfume_type', 'best_seller', 'tag_line', 'combo_code', 'refill_code', 'is_stock', 'goods_tax', 'offer_price', 'offer_from', 'offer_to', 'gender', 'related_ids', 'short_description', 'description', 'meta_title', 'meta_keyword', 'meta_description'], 'conditions' => $filterData])
            ->contain([
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'country_name', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                    },
                ],
                'ProductsImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'title', 'alt_text', 'img_thumbnail', 'img_small', 'img_base', 'img_large', 'is_thumbnail', 'is_small', 'is_base', 'is_large'])->where(['ProductsImages.is_active' => 'active', 'ProductsImages.exclude' => 0])->order(['ProductsImages.img_order' => 'ASC']);
                    },
                ]
            ]);
        if ($categoryId > 0) {
            $query = $query->matching("ProductsCategories", function ($q) use ($categoryId) {
                return $q->where(['ProductsCategories.category_id' => $categoryId]);
            });
        }
        $query = $query->order(['Products.is_stock' => 'ASC', 'Products.sort_order' => 'ASC'])->toArray();
        //pr($query); die;
        if (!empty($query)) {
            foreach ($query as $value) {
                $images = $notes = $brand = [];
                if ($value->brand) {
                    $brand = [
                        'id' => $value->brand->id,
                        'title' => $value->brand->title,
                        'description' => $value->brand->description,
                        'countryName' => $value->brand->country_name,
                        'image' => $value->brand->image,
                    ];
                }
                foreach ($value->products_categories as $v) {
                    $pCategory[] = $v->category_id;
                }
                foreach ($value->products_images as $v) {
                    $images[] = [
                        'id' => $v->id,
                        'title' => $v->title,
                        'alt' => $v->alt_text,
                        'imgThumbnail' => $v->img_thumbnail,
                        'imgSmall' => $v->img_small,
                        'imgBase' => $v->img_base,
                        'imgLarge' => $v->img_large,
                        'isThumbnail' => $v->is_thumbnail,
                        'isSmall' => $v->is_small,
                        'isBase' => $v->is_base,
                        'isLarge' => $v->is_large,
                    ];
                }
                foreach ($value->products_notes as $v) {
                    $notes[] = [
                        'id' => $v->id,
                        'title' => $v->title,
                        'description' => $v->description,
                    ];
                }

                $perfumeType = '';
                switch ($value->product_perfume_type) {
                    case 'attar':$perfumeType = 'Attar';
                        break;
                    case 'edp':$perfumeType = 'EDP- Eau De Parfum';
                        break;
                    case 'edt':$perfumeType = 'EDT-Eau De Toilette';
                        break;
                    case 'edit':$perfumeType = '';
                        break;
                    case 'pdt':$perfumeType = 'Parfum De Toilette';
                        break;
                    default:
                }
                $rProducts = [];
                $ids = explode(',', $value->related_ids);
                if (count($ids) > 0) {
                    $rProducts = $this->getRelatedProduct($ids);
                }
                $data[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'title' => $value->title,
                    'skuCode' => $value->sku_code,
                    'urlKey' => $value->url_key,
                    'tagClass' => $this->getTagClass($value->tag_line),
                    'tagLine' => empty($value->tag_line) ? '' : $value->tag_line,
                    'size' => $value->size,
                    'sizeUnit' => $value->size_unit,
                    'price' => $value->price,
                    'offerPrice' => $value->offer_price,
                    'perfumeType' => $perfumeType,
                    'qty' => $value->qty,
                    'sold' => $value->best_seller,
                    'comboCode' => !empty($value->combo_code) ? $value->combo_code : '',
                    'refillCode' => !empty($value->refill_code) ? $value->refill_code : '',
                    'isStock' => $value->is_stock,
                    'isCart' => in_array($value->id, $cartIds) ? 1 : 0,
                    'isWishlist' => in_array($value->id, $wishlistIds) ? 1 : 0,
                    'offerPrice' => $value->offer_price,
                    'offerFrom' => $value->offer_from,
                    'offerTo' => $value->offer_to,
                    'gender' => $value->gender,
                    'shortDescription' => $value->short_description,
                    'description' => $value->description,
                    'metaTitle' => $value->meta_title,
                    'metaKeyword' => $value->meta_keyword,
                    'metaDescription' => $value->meta_description,
                    'images' => $images,
                    'brand' => $brand,
                    'notes' => $notes,
                    'categories' => $pCategory,
                    'related' => $rProducts,
                    'reviews' => $this->getProductReviews($value->id),
                    'custReviews' => $this->totalProductReviews($value->id),
                    'progressRating' => $this->getReviewRatingProgress($value->id)
                ];
            }
        }
        //pr($data);die;
        return $data;
    }

    public function getProductByCategory($categoryId, $userId = 0, $limit = 20)
    {
        $data = [];
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cart = [353]; //array_column($cart['cart'], 'id');
        $query = TableRegistry::get('Products')->find('all', ['fields' => ['goods_tax','id', 'name', 'title', 'skuCode'=>'sku_code', 'urlKey'=>'url_key', 'size', 'sizeUnit'=>'size_unit', 'sold'=>'best_seller', 'price', 'isStock'=>'is_stock', 'offerPrice'=>'offer_price', 'offerFrom'=>'offer_from', 'offerTo'=>'offer_to', 'gender', 'description'=>'short_description','tagLine'=>'Products.tag_line'], 'limit' => $limit, 'conditions' => ['Products.is_active' => 'active']])
            ->contain([
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                    },
                ],
            ])
            ->contain([
                'ProductsImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'url'=>'img_base'])->where(['ProductsImages.is_base' => 1, 'ProductsImages.is_active' => 'active']);
                    },
                ],
            ])
            ->matching("ProductsCategories", function ($q) use ($categoryId) {
                return $q->where(['ProductsCategories.category_id' => $categoryId]);
            })
            ->order(['Products.is_stock' => 'ASC', 'Products.sort_order' => 'ASC'])->hydrate(0)->toArray();
        return array_map(function($v) use ($cart){
            $images = array_map(function($v1){ return array_slice($v1, 1); },$v['products_images']);
            $tax = explode('_', $v['goods_tax']);
            $v = array_slice($v, 1,16);
            $v['isCart'] = in_array($v['id'], $cart) ? 1 : 0;
            $v['taxTitle'] = $tax[0] ?? '';
            $v['taxValue'] = $tax[1] ?? 0;
            $v['taxType'] = $tax[2] ?? '';
            $v['tagClass'] = $this->getTagClass($v['tagLine']);
            $v['images'] = $images[0] ?? [];
            $v['reviews'] = $this->getProductReviews($v['id']);
            $v['custReviews'] = $this->totalProductReviews($v['id']);
            return $v;
        }, $query);
    }

    public function getRelatedProduct($ids = [], $userId = 0)
    {
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cart = array_column($cart['cart'], 'id');
        $query = TableRegistry::get('Products')->find('all', ['fields' => ['goods_tax','id', 'name', 'title', 'skuCode'=>'sku_code', 'urlKey'=>'url_key', 'size', 'sizeUnit'=>'size_unit', 'sold'=>'best_seller', 'price', 'isStock'=>'is_stock', 'offerPrice'=>'offer_price', 'offerFrom'=>'offer_from', 'offerTo'=>'offer_to', 'gender', 'description'=>'short_description','tagLine'=>'Products.tag_line'], 'conditions' => ['Products.id IN' => $ids, 'Products.is_active' => 'active']])
            ->contain([
                'ProductsImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'url'=>'img_large'])->where(['ProductsImages.is_large' => 1, 'ProductsImages.is_active' => 'active']);
                    }
                ],
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                    }
                ]
            ])->order(['Products.sort_order' => 'ASC'])->hydrate(0)->toArray();
        return array_map(function($v) use ($cart){
            $images = array_map(function($v1){ return array_slice($v1, 1); },$v['products_images']);
            $tax = explode('_', $v['goods_tax']);
            $v = array_slice($v, 1,17);
            $v['isCart'] = in_array($v['id'], $cart) ? 1 : 0;
            $v['taxTitle'] = $tax[0] ?? '';
            $v['taxValue'] = $tax[1] ?? 0;
            $v['taxType'] = $tax[2] ?? '';
            $v['tagClass'] = $this->getTagClass($v['tagLine']);
            $v['images'] = $images[0] ?? [];
            $v['reviews'] = $this->getProductReviews($v['id']);
            $v['custReviews'] = $this->totalProductReviews($v['id']);
            return $v;
        }, $query);
    }

    public function getProductReviews($productId, $limit = 10, $page = 1)
    {
        $query = TableRegistry::get('Reviews')->find('all', ['fields' => ['customer_name', 'id', 'title', 'description', 'rating', 'created'], 'conditions' => ['Reviews.product_id' => $productId, 'Reviews.is_active' => 'approved']])
            ->contain([
                'Customers' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['firstname', 'lastname', 'id', 'image']);
                    },
                ],
            ])->order(['Reviews.created' => 'DESC'])->limit($limit)->page($page)->hydrate(0)->toArray();             
        return array_map(function($v){
            $v['customer']['name'] = !empty($v['customer_name']) ? $v['customer_name'] : $v['customer']['firstname'] . ' ' . $v['customer']['lastname'];
            $v['customer'] = array_slice($v['customer'],2);
            $v['created'] = date('F j, Y h:i:s A', strtotime($v['created']));
            return array_slice($v,1);
        },$query);
    }

    public function getReviewRatingProgress($productId)
    {
        $data = [['rating'=>5], ['rating'=>4], ['rating'=>3], ['rating'=>2], ['rating'=>1]];
        $query = TableRegistry::get('Reviews')->find('all', ['fields' => ['rating', 'review'=>'count(*)'], 'group'=>['rating'], 'conditions' => ['Reviews.product_id' => $productId, 'Reviews.is_active' => 'approved']])->hydrate(0)->toArray();
        $sum   = array_sum(array_column($query, 'review'));
        for( $i = 0; $i < 5; $i++ ){
            $data[$i]['review'] = $data[$i]['progress'] = 0;
            foreach ( $query as $value ) {
                if( $data[$i]['rating'] == $value['rating'] ){
                    $data[$i]['review'] = $value['review'];
                    $data[$i]['progress'] =  floor( ($value['review'] * 100) / $sum );
                    break;
                }
            }
        }
        return $data;
    }

    public function totalProductReviews($productId)
    {
        $query = TableRegistry::get('Reviews')->find();
        $query = $query->select(['total' => $query->func()->count('*'), 'totalRating' => $query->func()->sum('rating')])->group(['customer_id'])->where(['product_id' => $productId, 'is_active' => 'approved'])->hydrate(0)->toArray();
        $total = array_sum(array_column($query,'total'));
        $rating = array_sum(array_column($query,'totalRating'));
        $rating = ($total > 0) ? floor($rating / $total):0;
        return ['customers'=>count($query),'rating'=>$rating];
    }

    public function getProductImages($id = 0)
    {
        return TableRegistry::get('ProductsImages')->find('all', ['fields' => ['id', 'imgOrder'=>'img_order', 'title', 'alt'=>'alt_text', 'imgThumbnail'=>'img_thumbnail', 'imgSmall'=>'img_small', 'imgBase'=>'img_base', 'imgLarge'=>'img_large', 'isThumbnail'=>'is_thumbnail', 'isSmall'=>'is_small', 'isBase'=>'is_base', 'isLarge'=>'is_large', 'exclude', 'isActive'=>'is_active'], 'conditions' => ['product_id' => $id]])->hydrate(0)->toArray();
    }

    public function getTagClass($tagline)
    {
        switch ($tagline) {
            case 'Best Seller':$class = ''; break;
            case 'Money':$class = 'corner_money'; break;
            case 'New':$class = 'corner_new'; break;
            case 'Premium':$class = 'corner_premium'; break;
            case 'Trending':$class = 'corner_trending'; break;
            default: $class = '';
        }
        return $class;
    }

    public function getDiscounts($price)
    {
        $discount = 20;
        $discountedPrice = ($price - ($price * $discount) / 100);
        return ['discount' => $discount, 'price' => $discountedPrice, 'label' => '20% Off'];
    }

    public function getStoreDiscounts($param)
    {
        $res = [];
        $price = $param['price'] ?? 0;
        $offerPrice = $param['offer'] ?? 0;
        $fromDate = $param['from'] ?? '';
        $toDate = $param['to'] ?? '';
        $coupon = $param['coupon'] ?? '';
        $product = $param['product'] ?? [];
        $this->Coupon = new CouponComponent(new ComponentRegistry());
        $couponOffer = $this->Coupon->getRulesByCoupon($coupon, 'salesapi@perfumebooth.com', $product); //pr($couponOffer);die;
        if (isset($couponOffer['status']) && $couponOffer['status']) {
            $res = ['original' => $price, 'price' => $price - $couponOffer['couponDiscount'], 'coupon' => $coupon];
            if ($couponOffer['discountType'] == 'percentage') {
                $res['label'] = $couponOffer['discountValue'] . '% Off';
            } 
        } else {
            $status = 0;
            $now = time();
            if (!empty($fromDate) && !empty($toDate)) {
                if ((strtotime($toDate) > $now) && (strtotime($fromDate) < $now)) {
                    $status = 1;
                }
            } else if (empty($fromDate) && !empty($toDate)) {
                if (strtotime($toDate) > $now) {
                    $status = 1;
                }
            } else if (!empty($fromDate) && empty($toDate)) {
                if (strtotime($fromDate) <= $now) {
                    $status = 1;
                }
            } else {
                $status = 1;
            }
            if ($status) {
                if (($offerPrice > 20) && ($price > $offerPrice)) {
                    $res = ['original' => $price, 'price' => $offerPrice, 'from' => $fromDate, 'to' => $toDate];
                }
            }
        }
        return $res;
    }

}
