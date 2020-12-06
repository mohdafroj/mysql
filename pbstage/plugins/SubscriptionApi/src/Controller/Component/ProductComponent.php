<?php
namespace SubscriptionApi\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ProductComponent extends Component
{
    public $perfumes1 = [4, 7, 8]; 
    public $perfumes2 = [3, 6, 11];
    public $images = [];
    /***** This is Product Component of SubscriptionApi Plugin that get brands list *****/
    public function __construct () {
        $this->images = [
            [
                'alt'=> "Defaut Image",
                'base'=> PC['IMAGE'],
                'large'=> PC['IMAGE'],
                'small'=> PC['IMAGE'],
                'title'=> "Defaut Image"
            ]
        ];
    }

    /***** This is Product Component of SubscriptionApi Plugin that check discount validity of product *****/
    public function discountValidity($price, $discount, $fromDate = '', $toDate = '')
    {
        $res = [];
        $status = 0;
        //$price = 100; $discount=10; $fromDate = '2019-03-13'; $toDate = '';
        $now  = time();
        if (!empty($fromDate) && !empty($toDate)) {
            if ( ( strtotime($toDate) > $now ) && ( strtotime($fromDate) < $now ) ) {
                $status = 1;
            }
        } else if ( empty($fromDate) && !empty($toDate) ) {
            if ( strtotime($toDate) > $now ) {
                $status = 1;
            }
        } else if ( !empty($fromDate) && empty($toDate) ) {
            if ( strtotime($fromDate) <= $now ) {
                $status = 1;
            }
        }else{
            $status = 1;
        }
        if( $status && ($discount > 0) ){
            $discountedPrice = ($price - ($price * $discount) / 100);
            if( $discountedPrice > 0 ){
                $res = ['discount' => $discount, 'original' => $price, 'price' => $discountedPrice,'from' => $fromDate, 'to' => $toDate, 'label' => $discount.'% Off'];
            }
        }
        return $res;
    }

    /***** This is Product Component of SubscriptionApi Plugin that get selected item data *****/
    public function getDetails($param)
    {
        $data = $rProducts = $pCategory = $reviews = $custReviews = $filterData = [];
        $cantainCategory = [6, 7];
        $userId = $param['userId'] ?? 0;
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cart = $cart['cart'];
        $cartIds = [];
        if (count($cart)) {$cartIds = array_column($cart, 'id');}
        
        $this->Customer = new CustomerComponent(new ComponentRegistry());
        $wishlist = $this->Customer->getWishlist($userId);
        $wishlistIds = [];
        if (count($wishlist)) {$wishlistIds = array_column($wishlist, 'id');}
        
        if (isset($param['id']) && ($param['id'] > 0)) {$filterData['Products.id'] = $param['id'];}
        if (isset($param['urlKey']) && ($param['urlKey'] != '')) {$filterData['Products.url_key'] = $param['urlKey'];}
        if (count($filterData) == 0) {$filterData['Products.id'] = -1;}
        $filterData['Products.is_active'] = 'active';
        //pr($filterData); die; 
        $query = TableRegistry::get('SubscriptionApi.Products')->find('all', ['fields' => ['id', 'sku_code', 'url_key', 'size', 'unit', 'quantity', 'product_perfume_type', 'sold', 'tag_line', 'is_stock', 'discount', 'discount_from', 'discount_to', 'gender', 'related_ids', 'meta_title', 'meta_keyword', 'meta_description'], 'conditions' => $filterData])
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'name', 'title', 'price', 'short_description', 'description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    },
                ],
                'ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                    },
                ],
                'ProductCategories.Categories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'name', 'url_key']);
                    }
                ],
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'country_name', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                    },
                ],
                'ProductNotes' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id','id','title', 'description'])->where(['ProductNotes.is_active' => 'active']);
                    },
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'large'=>'img_large'])->where(['ProductImages.is_active' => 'active', 'ProductImages.exclude' => '0'])->order(['ProductImages.img_order' => 'ASC']);
                    },
                ],
            ])
            ->hydrate(0)
            ->toArray(); 
        foreach ($query as $value) {
            $isContain = 0;
            $pCategory = array_column($value['product_categories'],'category_id');
            foreach ($pCategory as $v) {
                if (in_array($v, $cantainCategory)) {$isContain = 1;}
            }
            $categories = array_map(function ($item) {
                return $item['category'];
            }, $value['product_categories']);
            $perfumeType = '';
            switch ($value['product_perfume_type']) {
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
            if (!empty($value['product_prices'])) {
                $price    = $value['product_prices'][0]['price'];
                $discount = $this->discountValidity($price, $value['discount'], $value['discount_from'], $value['discount_to']);
                $price    = $discount['price'] ?? $price;
                $images = array_map(function ($v) { return array_slice($v,1); },$value['product_images']);
                if ( count($images) == 0 ) { $images = $this->images; }
                $data = [
                    'id' => $value['id'],
                    'name' => $value['product_prices'][0]['name'],
                    'title' => $value['product_prices'][0]['title'],
                    'priceLogo' => $value['product_prices'][0]['price_logo'],
                    'price' => $price,
                    'discount' => $discount,
                    'sku' => $value['sku_code'],
                    'urlKey' => $value['url_key'],
                    'tagClass' => $this->getTagClass($value['tag_line']),
                    'tagLine' => $value['tag_line'] ?? '',
                    'size' => $value['size'],
                    'unit' => $value['unit'],
                    'perfumeType' => $perfumeType,
                    'quantity' => $value['quantity'],
                    'sold' => $value['sold'],
                    'isStock' => ($value['quantity'] > 0) ? $value['is_stock'] : 'out_of_stock',
                    'isCart' => in_array($value['id'], $cartIds) ? 1 : 0,
                    'isWishlist' => in_array($value['id'], $wishlistIds) ? 1 : 0,
                    'isContain' => $isContain,
                    'gender' => $value['gender'],
                    'shortDescription' => !empty($value['product_prices'][0]['short_description']) ? $value['product_prices'][0]['short_description']:'N/A',
                    'description' => !empty($value['product_prices'][0]['description']) ? $value['product_prices'][0]['description']:'N/A',
                    'metaTitle' => $value['meta_title'],
                    'metaKeyword' => $value['meta_keyword'],
                    'metaDescription' => $value['meta_description'],
                    'images' => $images,
                    'oos_image' =>PC['OOS_IMAGE'],
                    'notes' =>  array_map(function ($v) { return array_slice($v,1); },$value['product_notes']),
                    'brand' => $value['brand'],
                    'categories' => $categories,
                    'progressRating' => $this->getReviewRatingProgress($value['id'])
                ];
            }
		}
        //pr($data); die;
        if (count($data) > 0) {
            $ids = empty($value['related_ids']) ? [] : explode(',', $value['related_ids']);
            if (count($ids) > 0) {
                $rProducts = $this->getRelatedProduct($ids, $userId);
            }
            if (count($rProducts) == 0) {
                foreach ($data['categories'] as $v) {
                    $rProducts = $this->getProductByCategory($v['id'], $userId);
                    if ( count($rProducts) > 0 ) {break;}
                }
            }
            $reviews = $this->getProductReviews($data['id']);
            $custReviews = $this->totalProductReviews($data['id']);
        }

        $data['related'] = $rProducts;
        $data['reviews'] = $reviews;
		$data['custReviews'] = $custReviews;				
        return $data;
    }

    /***** This is Product Component of SubscriptionApi Plugin that get categories related item *****/
    public function getProductByCategory($categoryId, $userId = 0, $limit = 20)
    {
        $data = [];
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cart = $cart['cart'];
        $cartIds = array_column($cart, 'id');
        if (count($cart)) {$cartIds = array_column($cart, 'id');}

        $query = TableRegistry::get('SubscriptionApi.Products')->find('all', ['fields' => ['id', 'sku_code', 'url_key', 'size', 'unit', 'sold', 'tag_line', 'is_stock', 'discount', 'discount_from', 'discount_to', 'gender'], 'limit' => $limit, 'conditions' => ['Products.is_active' => 'active']])
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'title', 'name', 'price', 'short_description', 'description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    },
                ],
                'ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                    },
                ],
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                    },
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'url'=>'img_large'])->where(['ProductImages.is_large' => 1, 'ProductImages.is_active' => 'active']);
                    },
                ]
            ])
            ->matching("ProductCategories", function ($q) use ($categoryId) {
                return $q->where(['ProductCategories.category_id' => $categoryId]);
            })
            ->order(['Products.is_stock' => 'ASC', 'Products.sort_order' => 'ASC'])
            ->hydrate(0)
            ->toArray();        
        foreach ($query as $value) {
			if( !empty($value['product_prices']) ){
                $price    = $value['product_prices'][0]['price'];
                $discount = $this->discountValidity($price, $value['discount'], $value['discount_from'], $value['discount_to']);
                $price    = $discount['price'] ?? $price;
                $images   = array_map(function($v){ return array_slice($v,1); }, $value['product_images']);
                if ( count($images) == 0 ) { $images = $this->images; }
				$data[] = [
                    'id' => $value['id'],
                    'name' => $value['product_prices'][0]['name'],
                    'title' => $value['product_prices'][0]['title'],
                    'price' => $price,
                    'price_logo' => $value['product_prices'][0]['price_logo'],
                    'description' => !empty($value['product_prices'][0]['short_description']) ? $value['product_prices'][0]['short_description']:'N/A',
                    'sku' => $value['sku_code'],
                    'urlKey' => $value['url_key'],
                    'tagClass' => $this->getTagClass($value['tag_line']),
                    'tagLine' => $value['tag_line'],
                    'size' => $value['size'],
                    'unit' => $value['unit'],
                    'sold' => $value['sold'],
                    'isStock' => $value['is_stock'],
                    'isCart' => in_array($value['id'], $cartIds) ? 1 : 0,
                    'discount' => $discount,
                    'gender' => $value['gender'],
                    'brand' => $value['brand'],
                    'images' => $images[0],
                    'oos_image' =>PC['OOS_IMAGE'],
                    'reviews' => $this->getProductReviews($value['id']),
                    'custReviews' => $this->totalProductReviews($value['id'])
                ];
		    }
        } 
        return $data;
    }

    /***** This is Product Component of SubscriptionApi Plugin that get related item data *****/
    public function getRelatedProduct($ids = [], $userId = 0)
    {
        $data = [];
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($userId);
        $cart = $cart['cart'];
        $cartIds = [];
        if (count($cart)) {$cartIds = array_column($cart, 'id');}
        $query = TableRegistry::get('SubscriptionApi.Products')->find('all', ['fields' => ['id', 'sku_code', 'url_key', 'size', 'unit', 'sold', 'tag_line', 'is_stock', 'discount', 'discount_from', 'discount_to', 'gender'], 'conditions' => ['Products.id IN' => $ids, 'Products.is_active' => 'active']])
                ->contain([
                    'ProductPrices' => [
                        'queryBuilder' => function ($q) {
                            return $q->select(['id', 'product_id', 'title', 'name', 'price', 'short_description', 'description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                        },
                    ],
                    'ProductPrices.Locations' => [
                        'queryBuilder' => function ($q) {
                            return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                        },
                    ],
                    'Brands' => [
                        'queryBuilder' => function ($q) {
                            return $q->select(['id', 'title', 'image', 'description'])->where(['Brands.is_active' => 'active']);
                        },
                    ],
                    'ProductImages' => [
                        'queryBuilder' => function ($q) {
                            return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'url'=>'img_large'])->where(['ProductImages.is_large' => 1, 'ProductImages.is_active' => 'active']);
                        },
                    ],
                ])
                ->order(['Products.sort_order' => 'ASC'])
                ->hydrate(0)
				->toArray();
				//		pr($query); die;
        foreach ($query as $value) {
			if( !empty($value['product_prices']) ){
                $price    = $value['product_prices'][0]['price'];
                $discount = $this->discountValidity($price, $value['discount'], $value['discount_from'], $value['discount_to']);
                $price    = $discount['price'] ?? $price;
                $images   = array_map(function ($v) { return array_slice($v,1); },$value['product_images']);
                if ( count($images) == 0 ) { $images = $this->images; }
				$data[] = [
                    'id' => $value['id'],
                    'name' => $value['product_prices'][0]['name'],
                    'title' => $value['product_prices'][0]['title'],
                    'price' => $price,
                    'price_logo' => $value['product_prices'][0]['price_logo'],
                    'description' => !empty($value['product_prices'][0]['short_description']) ? $value['product_prices'][0]['short_description']:'N/A',
                    'discount' => $discount,
                    'sku' => $value['sku_code'],
                    'urlKey' => $value['url_key'],
                    'tagClass' => $this->getTagClass($value['tag_line']),
                    'tagLine' => $value['tag_line'],
                    'size' => $value['size'],
                    'unit' => $value['unit'],
                    'sold' => $value['sold'],
                    'isStock' => $value['is_stock'],
                    'isCart' => in_array($value['id'], $cartIds) ? 1 : 0,
                    'gender' => $value['gender'],
                    'brand' => $value['brand'],
                    'oos_image' => PC['OOS_IMAGE'],
                    'images' => $images[0] ?? [],
                    'reviews' => $this->getProductReviews($value['id']),
                    'custReviews' => $this->totalProductReviews($value['id']),
                ];
			}
        }
        return $data;
    }

    /***** This is Product Component of SubscriptionApi Plugin that get product reviews data *****/
    public function getProductReviews($productId, $limit = 10, $page = 1)
    {
        $data = [];
        $query = TableRegistry::get('SubscriptionApi.Reviews')->find('all', ['fields' => ['id', 'title', 'customer_name', 'description', 'rating', 'created'], 'conditions' => ['Reviews.product_id' => $productId, 'Reviews.is_active' => 'approved']])
            ->contain([
                'Customers' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'firstname', 'lastname', 'image']);
                    },
                ],
            ])
            ->order(['Reviews.created' => 'DESC'])
            ->limit($limit)
            ->page($page)
            ->toArray(); //pr($query); die;
        foreach ($query as $value) {
            $customer = [];
            if ($value->customer) {
                $customer = [
                    'id' => $value->customer->id,
                    'name' => !empty($value->customer_name) ? $value->customer_name : $value->customer->firstname . ' ' . $value->customer->lastname,
                    'image' => $value->customer->image,
                ];
            }
            $data[] = [
                'id' => $value->id,
                'title' => $value->title,
                'description' => $value->description,
                'rating' => $value->rating,
                'created' => date('F j, Y h:i:s A', strtotime($value->created)),
                'customer' => $customer,
            ];
        }
        return $data;
    }

    /***** This is Product Component of SubscriptionApi Plugin that get product total reviews data *****/
    public function totalProductReviews($productId)
    {
        $total = $rating = 0;
        $query = TableRegistry::get('SubscriptionApi.Reviews')->find();
        $query = $query->select(['total' => $query->func()->count('*'), 'totalRating' => $query->func()->sum('rating')])
            ->group(['customer_id'])
            ->where(['product_id' => $productId, 'is_active' => 'approved'])
            ->toArray();
        $customers = count($query);
        foreach ($query as $value) {
            $total = $total + $value->total;
            $rating = $rating + $value->totalRating;
        }
        $rating = ($total > 0) ? ceil($rating / $total) : 0;
        return ['customers' => $customers, 'rating' => $rating];
    }

    /***** This is Product Component of SubscriptionApi Plugin that get product images data *****/
    public function getProductImages($id = 0)
    {
        $data = [];
        $dataTable = TableRegistry::get('SubscriptionApi.ProductImages');
        $query = $dataTable->find('all', ['fields' => ['id', 'product_id', 'img_order', 'title', 'alt_text', 'img_thumbnail', 'img_small', 'img_base', 'img_large', 'is_thumbnail', 'is_small', 'is_base', 'is_large', 'exclude', 'is_active'], 'conditions' => ['product_id' => $id]])
            ->toArray();
        foreach ($query as $v) {
            $data[] = [
                'id' => $v->id,
                'imgOrder' => $v->img_order,
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
                'exclude' => $v->exclude,
                'isActive' => $v->is_active,
            ];
        }
        return $data;
    }

    public function getReviewRatingProgress($productId)
    {
        $data = [['rating'=>5], ['rating'=>4], ['rating'=>3], ['rating'=>2], ['rating'=>1]];
        $query = TableRegistry::get('SubscriptionApi.Reviews')->find('all', ['fields' => ['rating', 'review'=>'count(*)'], 'group'=>['rating'], 'conditions' => ['Reviews.product_id' => $productId, 'Reviews.is_active' => 'approved']])->hydrate(0)->toArray();
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

    /***** This is Product Component of SubscriptionApi Plugin that get product related tag class *****/
    public function getTagClass($tagline)
    {
        $class = '';
        switch ($tagline) {
            case 'Best Seller':$class = '';
                break;
            case 'Money':$class = 'corner_money';
                break;
            case 'New':$class = 'corner_new';
                break;
            case 'Premium':$class = 'corner_premium';
                break;
            case 'Trending':$class = 'corner_trending';
                break;
            default: //Best Seller
        }
        return $class;
    }

    public function manageNotes ($notes) {
        $top = $middle = $base = '';
        foreach ($notes as $value ) {
            switch ($value['title']) {
                case 'base_note' : $base .= $value['description'].','; break;
                case 'middle_note': $middle .= $value['description'].','; break;
                case 'top_note': $top .= $value['description'].','; break;
                default: 
            }
        }
        $res = [];
        if ( !empty($top) ) {
            $res[] = ['title'=>'Top Note', 'description'=>$top];
        }
        if ( !empty($middle) ) {
            $res[] = ['title'=>'Middle Note', 'description'=>$middle];
        }
        if ( !empty($base) ) {
            $res[] = ['title'=>'Base Note', 'description'=>$base];
        }
        return $res;
    }
    /***************This is for all products **********************/
    public function getAllProducts ($filter) {
        $data = [];
        try {
            $filter['Products.is_stock'] = 'in_stock';
            $filter['Products.is_active'] = 'active';
            $dataTable = TableRegistry::get('SubscriptionApi.Products');
            $query = $dataTable->find('all', ['fields' => ['id', 'url_key', 'sku_code', 'size', 'unit', 'sold', 'tag_line', 'gender', 'quantity', 'discount',  'discount_from',  'discount_to', 'is_stock', 'is_combo'], 'conditions' => $filter, 'order'=>['Products.is_stock' => 'asc', 'Products.sort_order' => 'asc']])
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'name', 'title', 'price', 'short_description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    }
                ],
                'ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                    }
                ],
                'ProductCategories.Categories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'name', 'url_key']);
                    }
                ],
                'ProductNotes' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'title', 'description'])->where(['ProductNotes.is_active' => 'active'])->order(['title'=>'DESC']);
                    }
                ],
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'description', 'image'])->where(['Brands.is_active' => 'active']);
                    }
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'exclude', 'title', 'alt'=>'alt_text', 'small' => 'img_small', 'base' => 'img_base', 'large' => 'img_large'])->where(['ProductImages.is_large' => 1, 'ProductImages.is_active' => 'active']);
                    }
                ]
            ])->hydrate(0)->toArray();
            //pr($query); die;//
            foreach ($query as $value) {
                $categories = array_map(function ($item) {
                    return $item['category'];
                }, $value['product_categories']);
                $images = array_filter($value['product_images'], function ($item) {
                    return $item['exclude'] != 1;
                });
                $images = array_values($images); 
                $images = array_map(function($item){
                    array_splice($item, 0, 2);
                    return $item;
                }, $images);
                $notes = $this->manageNotes($value['product_notes']);
                if( !empty($value['product_prices']) ){
                    $price = $value['product_prices'][0]['price'];
                    $discount = $this->discountValidity($price, $value['discount'], $value['discount_from'], $value['discount_to']);
                    $price = $discount['price'] ?? $price;
                    if ( count($images) == 0 ) { $images = $this->images; }
                    $data[] = [
                        'id' => $value['id'],
                        'name' => $value['product_prices'][0]['name'],
                        'title' => $value['product_prices'][0]['title'],
                        'shortDescription' => !empty($value['product_prices'][0]['short_description']) ? $value['product_prices'][0]['short_description']:'N/A',
                        'urlKey' => $value['url_key'],
                        'sku' => $value['sku_code'],
                        'tagClass' => $this->getTagClass($value['tag_line']),
                        'tagLine' => empty($value['tag_line']) ? '' : $value['tag_line'],
                        'price' => $price,
                        'priceLogo' => $value['product_prices'][0]['price_logo'],
                        'discount' => $discount,
                        'quantity' => $value['quantity'],
                        'gender' => $value['gender'],
                        'brand' => $value['brand'],
                        'categories' => $categories,
                        'notes' => $notes,
                        'size' => $value['size'],
                        'unit' => $value['unit'],
                        'sold' => $value['sold'],
                        'isStock' => ($value['quantity'] > 0) ? $value['is_stock'] : 'out_of_stock',
                        'oos_image' =>PC['OOS_IMAGE'],
                        'images' => $images
                    ];
                }
            }
        } catch (\Exception $e) {}
        return $data;
    }

    /***************This is for all products **********************/
    public function getScentMatch ($param) {
        $data = $filter = [];
        //try {
            $filter['Products.is_stock'] = 'in_stock';
            $filter['Products.is_active'] = 'active';
            $filter['Products.is_combo'] = '0';
            if ( !empty($param['gender']) ) {
                $gender = explode(",", $param['gender']);
                $filter['Products.gender IN'] = $gender;
            }
            
            $dataTable = TableRegistry::get('SubscriptionApi.Products');
            $query = $dataTable->find('all', ['fields' => ['id', 'url_key', 'sku_code', 'size', 'unit', 'sold', 'tag_line', 'gender', 'family_ids', 'quantity', 'discount',  'discount_from',  'discount_to', 'is_stock', 'is_combo'], 'conditions' => $filter, 'order'=>['Products.is_stock' => 'asc', 'Products.sort_order' => 'asc']])
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'name', 'title', 'price', 'short_description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    }
                ],
                'ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                    }
                ],
                'ProductNotes' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'title', 'description'])->where(['ProductNotes.is_active' => 'active'])->order(['title'=>'DESC']);
                    }
                ],
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'description', 'image'])->where(['Brands.is_active' => 'active']);
                    }
                ],
                'ProductCategories.Categories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'name', 'url_key']);
                    }
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'title', 'alt'=>'alt_text', 'small' => 'img_small', 'base' => 'img_base', 'large' => 'img_large'])->where(['ProductImages.exclude' => '0', 'ProductImages.is_active' => 'active'])->order(['img_order'=>'ASC']);
                    }
                ]
            ]);
            if ( !empty($param['families']) ) {
                $family = str_replace(',','|',$param['families']);
				$query = $query->where(["concat(',',family_ids,',') REGEXP ',($family),'"]);
            }
			$query = $query->hydrate(0)->toArray();
            //pr($query); die;
            foreach ($query as $value) {
                $categories = array_map(function ($item) {
                    return $item['category'];
                }, $value['product_categories']);
                $images = array_map(function($item){
                    array_splice($item, 0, 1);
                    return $item;
                }, $value['product_images']);
                $notes = $this->manageNotes($value['product_notes']);
                if( !empty($value['product_prices']) ){
                    $price = $value['product_prices'][0]['price'];
                    $discount = $this->discountValidity($price, $value['discount'], $value['discount_from'], $value['discount_to']);
                    $price = $discount['price'] ?? $price;
					$familyData = [];
					$familyId = explode(',', $value['family_ids']);
					if ( sizeof($familyId) ) {
						$query = TableRegistry::get('SubscriptionApi.Families')->find('all', ['conditions' => ['id IN'=>$familyId,'is_active' => 'active'], 'order'=>['fscore' => 'desc']])->hydrate(0)->toArray();
						$familyData = $query[0] ?? ['fscore'=>50];
					}
                    if ( count($images) == 0 ) { $images = $this->images; }
                    $data[] = [
                        'id' => $value['id'],
                        'name' => $value['product_prices'][0]['name'],
                        'title' => $value['product_prices'][0]['title'],
                        'shortDescription' => !empty($value['product_prices'][0]['short_description']) ? $value['product_prices'][0]['short_description']:'N/A',
                        'urlKey' => $value['url_key'],
                        'sku' => $value['sku_code'],
                        'tagClass' => $this->getTagClass($value['tag_line']),
                        'tagLine' => empty($value['tag_line']) ? '' : $value['tag_line'],
                        'price' => $price,
                        'priceLogo' => $value['product_prices'][0]['price_logo'],
                        'discount' => $discount,
                        'quantity' => $value['quantity'],
                        'gender' => $value['gender'],
                        'brand' => $value['brand'],
                        'categories' => $categories,
                        'notes' => $notes,
                        'size' => $value['size'],
                        'unit' => $value['unit'],
                        'sold' => $value['sold'],
                        'isStock' => ($value['quantity'] > 0) ? $value['is_stock'] : 'out_of_stock',
                        'family' => $familyData,
                        'oos_image' =>PC['OOS_IMAGE'],
                        'images' => $images
                    ];
                }
            }
        //} catch (\Exception $e) {}
        return $data;
    }

    public function getHashProduct ($param) {
        $data = $filter = [];
        $couponCode = $param['coupon'] ?? '';
        //try {
            //$filter['Products.id'] = '16';
            $filter['Products.is_stock'] = 'in_stock';
            $filter['Products.is_active'] = 'active';
            if ( !empty($param['gender']) ) {
                $gender = explode(",", $param['gender']);
                $filter['Products.gender IN'] = $gender;
            }
            $this->Coupon = new CouponComponent(new ComponentRegistry());
            $dataTable = TableRegistry::get('SubscriptionApi.Products');
            $query = $dataTable->find('all', ['fields' => ['id', 'url_key', 'sku_code', 'size', 'unit', 'sold', 'tag_line', 'title_color', 'gender', 'family_ids', 'quantity', 'discount',  'discount_from',  'discount_to', 'is_stock', 'is_combo'], 'conditions' => $filter, 'order'=>['Products.is_stock' => 'asc', 'Products.sort_order' => 'asc']])
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'name', 'title', 'price', 'short_description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    }
                ],
                'ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                    }
                ],
                'ProductNotes' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'title', 'description'])->where(['ProductNotes.is_active' => 'active'])->order(['title'=>'DESC']);
                    }
                ],
                'Brands' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'description', 'image'])->where(['Brands.is_active' => 'active']);
                    }
                ],
                'ProductCategories.Categories' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'name', 'url_key']);
                    }
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'title', 'alt'=>'alt_text', 'small' => 'img_small', 'base' => 'img_base', 'large' => 'img_large'])->where(['ProductImages.exclude' => '0', 'ProductImages.is_active' => 'active'])->order(['img_order'=>'asc']);
                    }
                ]
            ]);
            /*if ( !empty($param['favoritePerfumes']) ) {
                $favoritePerfumes = str_replace(',','|',$param['favoritePerfumes']);
				$query = $query->matching('ProductPrices', function ($q) use ($favoritePerfumes){
					return $q->where(["concat(ProductPrices.title) REGEXP '($favoritePerfumes)'"]);
				});
            }*/
            if ( !empty($param['families']) ) {
                $family = str_replace(',','|',$param['families']);
				$query = $query->where(["concat(',',family_ids,',') REGEXP ',($family),'"]);
            }
			$query = $query->hydrate(0)->toArray();
            //pr($query); die;
            $products = [];
            foreach ($query as $value) {
                $categories = array_map(function ($item) {
                    return $item['category'];
                }, $value['product_categories']);                
                $images = array_map(function($item){
                    array_splice($item, 0, 1);
                    return $item;
                }, $value['product_images']);
                $notes = $this->manageNotes($value['product_notes']);
                if( !empty($value['product_prices']) ) {
                    $price = $value['product_prices'][0]['price'];
                    $discount = $this->discountValidity($price, $value['discount'], $value['discount_from'], $value['discount_to']);
                    $price = $discount['price'] ?? $price;
					$familyData = [];
					$familyId = explode(',', $value['family_ids']);
					if ( sizeof($familyId) ) {
						$query = TableRegistry::get('SubscriptionApi.Families')->find('all', ['conditions' => ['id IN'=>$familyId,'is_active' => 'active'], 'order'=>['fscore' => 'desc']])->hydrate(0)->toArray();
						$familyData = $query[0] ?? ['fscore'=>50];
					}
                    if ( count($images) == 0 ) { $images = $this->images; }
                    if ( empty( $discount ) ) { //Calculate discount by coupon code
                        //$products = [];
                        $products[] = ['id' => $value['id'], 'quantity' => 1, 'price' => $price, 'category'=>array_column($categories, 'id'), 'brand'=>$value['brand']['id'] ?? 0];
                        /*$res = $this->Coupon->getRulesByCoupon($couponCode, '', $products); //pr($res);die;
                        $couponDiscount = $res['couponDiscount'] ?? 0;
                        if ( $couponDiscount > 0 ) { 
                            $discount = ['discount' => $couponDiscount, 'original' => $price];
                            $price = $price - $couponDiscount;
                            $discount['price'] = $price;
                        }*/
                    }
                    $data[] = [
                        'id' => $value['id'],
                        'name' => $value['product_prices'][0]['name'],
                        'title' => $value['product_prices'][0]['title'],
                        'shortDescription' => !empty($value['product_prices'][0]['short_description']) ? $value['product_prices'][0]['short_description']:'N/A',
                        'urlKey' => $value['url_key'],
                        'sku' => $value['sku_code'],
                        'tagClass' => $this->getTagClass($value['tag_line']),
                        'tagLine' => empty($value['tag_line']) ? '' : $value['tag_line'],
                        'title_color' => $value['title_color'],
                        'price' => $price,
                        'priceLogo' => $value['product_prices'][0]['price_logo'],
                        'discount' => $discount,
                        'quantity' => $value['quantity'],
                        'gender' => $value['gender'],
                        'brand' => $value['brand'],
                        'categories' => $categories,
                        'notes' => $notes,
                        'isCombo' => $value['is_combo'],
                        'size' => $value['size'],
                        'unit' => $value['unit'],
                        'sold' => $value['sold'],
                        'isStock' => ($value['quantity'] > 0) ? $value['is_stock'] : 'out_of_stock',
                        'family' => $familyData,
                        'images' => $images,
                        'oos_image' =>PC['OOS_IMAGE']
                    ];
                }
            }
            if ( !empty($couponCode) ) {
                $res = $this->Coupon->getRulesByCoupon($couponCode, '', $products); 
                $products = $res['products'] ?? [];
                $totalProducts = count($data);
                foreach ($products as $value) {
                    $couponDiscount = $value['discount'] ?? 0;
                    for($i=0; $i < $totalProducts; $i++) {
                        if ( $data[$i]['id'] == $value['id'] ) {
                            if ( empty($data[$i]['discount']) ) {
                                $price = $data[$i]['price'];
                                $discount = ['discount' => $couponDiscount, 'original' => $price];
                                $price = $price - $couponDiscount;
                                $discount['price'] = $price;
                                $data[$i]['discount'] = $discount;
                                $data[$i]['price'] = $price;
                            }
                            break;
                        }
                    }
                }
            }
            //pr($res);die;
        //} catch (\Exception $e) {}
        return $data;
    }

    /***** This is Product Component of SubscriptionApi Plugin that get categories related item *****/
    public function getBusterProduct($categoryId = 10)
    {
        $data = [];
        $query = TableRegistry::get('SubscriptionApi.Products')->find('all', ['fields' => ['id', 'gender'], 'conditions' => ['Products.is_active' => 'active','Products.is_stock' => 'in_stock', 'Products.quantity >' => 1]])
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'title', 'name', 'price', 'short_description', 'description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    },
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'id', 'title', 'alt'=>'alt_text', 'url'=>'img_large'])->where(['ProductImages.is_large' => 1, 'ProductImages.is_active' => 'active']);
                    },
                ]
            ])
            ->matching("ProductCategories", function ($q) use ($categoryId) {
                return $q->where(['ProductCategories.category_id' => $categoryId]);
            })
            ->order(['Products.is_stock' => 'ASC', 'Products.sort_order' => 'ASC'])
            ->hydrate(0)
            ->toArray();        
        foreach ($query as $value) {
			if( !empty($value['product_prices']) ){
                $images   = array_map(function($v){ return array_slice($v,1); }, $value['product_images']);
                if ( count($images) == 0 ) { $images = $this->images; }
				$data[] = [
                    'id' => $value['id'],
                    'name' => $value['product_prices'][0]['name'],
                    'title' => $value['product_prices'][0]['title'],
                    'price' => $value['product_prices'][0]['price'],
                    'gender' => $value['gender'],
                    'images' => $images[0],
                    'oos_image' =>PC['OOS_IMAGE']
                ];
		    }
        } 
        return $data;
    }

    public function getPackages ($products) {
        return [
            [
                'id'=>1,
                'title' => 'MPF DARING EAU DE PARFUM FOR WOMEN 100ML',
                'image' =>'',
                'price' => 999,
                'gender' => 'male',
                'crossprice' => 2000,
                'pack' => 'pack1',
                'brand' => ['id'=>1,'title'=>'3 Compbo Pack'],
                'categories' => [['id'=>1,'name'=>'Compbo Pack']],
                'products' => $products['pack1'],
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.'
            ],
            [
                'id'=>2,
                'title' => 'MPF DARING EAU DE PARFUM FOR WOMEN 100ML',
                'image' =>'',
                'price' => 999,
                'gender' => 'female',
                'crossprice' => 2000,
                'pack' => 'pack2',
                'brand' => ['id'=>1,'title'=>'3 Compbo Pack'],
                'categories' => [['id'=>1,'name'=>'Compbo Pack']],
                'products' => $products['pack2'],
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.'
            ],
            [
                'id'=>3,
                'title' => 'MPF DARING EAU DE PARFUM FOR WOMEN 100ML',
                'image' =>'',
                'price' => 999,
                'gender' => 'unisex',
                'crossprice' => 2000,
                'pack' => 'custom',
                'brand' => ['id'=>1,'title'=>'3 Compbo Pack'],
                'categories' => [['id'=>1,'name'=>'Compbo Pack']],
                'products' => [
                    ['id'=>0, 'title' => 'Add Product', 'image' => 'assets/images/plus.svg'],
                    ['id'=>0, 'title' => 'Add Product', 'image' => 'assets/images/plus.svg'],
                    ['id'=>0, 'title' => 'Add Product', 'image' => 'assets/images/plus.svg']
                ],
                'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.'
            ]
        ];
    }

	public function getFamilyNameByNotes($notes)
	{
		$algoNotes = TableRegistry::get('SubscriptionApi.AlgoNotes')->find('All')->where(['note_name in' => $notes, 'status' => 1])->limit(1)->hydrate(0)->toArray();
		return $algoNotes[0]['group_name'] ?? '';
	}

}
