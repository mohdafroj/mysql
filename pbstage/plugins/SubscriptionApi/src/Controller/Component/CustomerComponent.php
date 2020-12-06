<?php
namespace SubscriptionApi\Controller\Component;

use SubscriptionApi\Controller\Component\Store;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Cake\Core\Configure;

class CustomerComponent extends Component
{
    public $REG_MOBILE = '/^[1-9]{1}[0-9]{9}$/';
    public $REG_ALPHA_SPACE = '/^[a-zA-Z ]*$/';
    public $REG_DATE = '/^\d{4}-\d{2}-\d{2}$/';
    public $REG_PINCODE = '/^\d{6}$/';
    /***** SubscriptionApi Plugin *****/
    public function isAuth($customerId)
    {   
        $status = 0;
        $data = [];
        $userToken = $this->request->getHeader('Authorization');
        $userToken = $userToken[0] ?? '';
        $userToken = explode(" ", $userToken);
        $userToken = $userToken[1] ?? '';
        //check jwt
        $res = $this->verifyToken($userToken);
        if (($res['status'] == 200) && ($res['data']['id'] == $customerId)) {
            $status = 1;
            $data = $res['data'];
        } else {
            //echo json_encode(['status' => $status, 'message' => 'Your login token expired, Please login again!']);die;
        }
        return ['status' => $status, 'data' => $data];
    }

    /***** SubscriptionApi Plugin, This is user authentication token *****/
    public function getToken($param)
    {
        $tokenParam = [
            'iat' => time(),
            'iss' => PC['COMPANY']['website'],
            'alg' => 'HS256',
            'data' => $param,
        ];
        return JWT::encode($tokenParam, Security::salt());
    }

    /***** SubscriptionApi Plugin, This is verify user authentication token *****/
    public function verifyToken($token)
    {
        $message = '';
        $status = 0;
        $data = [];
        try {
            $status = 200;
            $message = 'Token verified';
            $info = JWT::decode($token, Security::salt(), ['HS256']);
            $data = json_decode(json_encode($info->data), true);
        } catch (\Exception $e) {
            $message = 'Token expired';
            $status = 401;
        }
        return ['message' => $message, 'data' => $data, 'status' => $status];
    }

    /***** SubscriptionApi Plugin, This is generate user otp *****/
	public function generateOtp($length=6) {
		$chars = "0123456789";
		return substr(str_shuffle($chars), 0, $length);
    }

    /***** SubscriptionApi Plugin, This is check email status *****/
	public function getDmainEmailStatus(){
		$status = 0;
		if( in_array($_SERVER['SERVER_NAME'], PC['EMAIL_HOST']) ){
            $status = 1;
		}
		return $status;
	}


    /***** SubscriptionApi Plugin, This is get customer session data *****/
    public function getSesssionData($customerId, $md5Pass = '')
    {
        $data = $cart = [];
        $this->Membership = new MembershipComponent(new ComponentRegistry());
        $this->Store = new StoreComponent(new ComponentRegistry());
        $cart = $this->Store->getActiveCart($customerId);
        $cart = $cart['cart'] ?? [];
        try {
            $data = TableRegistry::get('SubscriptionApi.Customers')->get($customerId, ['fields' => ['id', 'firstname', 'lastname', 'email', 'gender', 'dob', 'mobile', 'image'], 'conditions' => ['is_active' => 'active']])->toArray();
            if (!empty($data)) {
                $data['api_token'] = $this->getToken($data);
                $data['cart'] = $cart;
                $data['member'] = $this->Membership->getMembership($customerId);
            }
        } catch (\Exception $e) {
            $data = [];
        }
        return $data;
    }

    /***** SubscriptionApi Plugin, This is validate customer customer account *****/
    public function validateLogin($username, $password, $currentUserId)
    {
        $customerTable = TableRegistry::get('SubscriptionApi.Customers');
        $customerData = [];
        $pass = md5($password);
        $customers = $customerTable->find('all', ['fields' => ['id'], 'conditions' => ['email' => $username, 'password' => $pass, 'is_active' => 'active']])->toArray();
        if (!empty($customers)) {
            $customer = $customerTable->get($customers[0]['id']);
            $customer->logdate = date("Y-m-d H:i:s");
            $customer->lognum = $customers[0]['lognum'] + 1;
            if ($customerTable->save($customer)) {
                TableRegistry::get('SubscriptionApi.Carts')->query()->update()->set(['customer_id'=>$customers[0]['id']])->where(['customer_id'=>$currentUserId])->execute();
                $customerData = $this->getSesssionData($customers[0]['id']);
            }
        }
        return $customerData;
    }

    public function generatePassword($length = 8)
    {
        //$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $chars = "0123456789";
        $password = substr(str_shuffle($chars), 0, $length);
        return $password;
    }

    public function getReviews($customerId, $limit = 20, $page = 1)
    {
        $data = []; //100136750
        $reviewTable = TableRegistry::get('SubscriptionApi.Reviews');
        $dataTable = TableRegistry::get('SubscriptionApi.Products');
        $query = $dataTable->find('all', ['fields' => ['id', 'sku_code', 'url_key', 'size', 'unit', 'is_stock', 'discount', 'discount_from', 'discount_to', 'gender'], 'limit' => $limit, 'conditions' => ['Products.is_active' => 'active']])
            ->distinct(['Products.id'])
            ->innerJoinWith("Reviews", function ($q) use ($customerId) {
                $q = $q->find('all');
                return $q->select(['product_id', 'id', 'title', 'customer_name', 'description', 'rating', 'created', 'total' => $q->func()->count('*'), 'totalRating' => $q->func()->sum('rating')])->where(['Reviews.customer_id' => $customerId]);
            })
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'title', 'name', 'price', 'short_description'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'),'ProductPrices.is_active' => 'active']);
                    },
                ],
                'ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'price_logo'=>'currency_logo', 'title']);
                    },
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'title', 'alt_text', 'img_small'])->where(['ProductImages.is_small' => 1, 'ProductImages.is_active' => 'active']);
                    }
                ]
            ])
            ->page($page)
            ->order(['Products.created' => 'DESC'])
            ->hydrate(0)
            ->toArray();
        //pr($query);die;
        foreach ($query as $value) {
            $reviews = $images = [];
            foreach ($value['product_images'] as $v) {
                $images = [
                    'id' => $v['id'],
                    'alt' => $v['alt_text'],
                    'title' => $v['title'],
                    'url' => $v['img_small']
                ];
            }
            $r = $reviewTable->find();
            $r = $r->select(['id', 'product_id', 'title', 'customer_name', 'description', 'rating', 'created', 'total' => $r->func()->count('*'), 'totalRating' => $r->func()->sum('rating')])
                ->where(['customer_id' => $customerId, 'product_id' => $value['id']])
                ->group(['product_id'])
                ->contain([
                    'Customers' => [
                        'queryBuilder' => function ($q) {
                            return $q->select(['id', 'firstname', 'lastname', 'image']);
                        },
                    ],
                ])
                ->toArray();
            //pr($r);die;
            foreach ($r as $v) {
                $customer = [];
                if ($v->customer) {
                    $customer = [
                        'id' => $v->customer->id,
                        'name' => !empty($v->customer_name) ? $v->customer_name : $v->customer->firstname . ' ' . $v->customer->lastname,
                        'image' => $v->customer->image,
                    ];
                }
                $reviews = [
                    'id' => $v->id,
                    'title' => $v->title,
                    'description' => $v->description,
                    'totalReviews' => $v->total,
                    'totalRating' => $v->totalRating,
                    'finalRating' => ceil($v->totalRating / $v->total),
                    'created' => date('Y-m-d', strtotime($v->created)),
                    'customer' => $customer,
                ];
            } //pr($value); die;
            $data[] = [
                'id' => $value['id'],
                'title' => $value['product_prices'][0]['title'] ?? '',
                'skuCode' => $value['sku_code'],
                'urlKey' => $value['url_key'],
                'size' => $value['size'],
                'unit' => $value['unit'],
                'price' => $value['product_prices'][0]['price'] ?? 0,
                'isStock' => $value['is_stock'],
                //'offerPrice' => $value['offer_price'],
                'discountFrom' => $value['discount_from'],
                'discountTo' => $value['discount_to'],
                'gender' => $value['gender'],
                'description' => $value['product_prices'][0]['short_description'] ?? '',
                'images' => $images,
                'reviews' => $reviews,
            ];
        }
        return $data;
    }

    /***** This is SubscriptionApi Plugin *****///get customers addresses
    public function changeAccount($currentCustomerId, $newCustomerId)
    {
        if ($currentCustomerId > 0 && $newCustomerId > 0) {
            $dataTable = TableRegistry::get('SubscriptionApi.Carts');
            $cart = $this->getMiniCart($currentCustomerId);
            $items = [];
            foreach ($cart as $value) {
                $items[] = [
                    'customer_id' => $newCustomerId,
                    'product_id' => $value['id'],
                    'quantity' => $value['cart_quantity'],
                ];
            }
            if (count($items) > 0) {
                $dataTable->query()->delete()->where(['customer_id' => $newCustomerId])->execute();
                $items = $dataTable->newEntities($items);
                $dataTable->saveMany($items);

                $dataTable = TableRegistry::get('SubscriptionApi.Customers');
                $customer = $dataTable->get($currentCustomerId);
                $customer->api_token = null;
                $dataTable->save($customer);
            }
        }
        return true;
    }

    /***** This is SubscriptionApi Plugin *****///get customers addresses
    public function addAddresses($data = [])
    {
        $addressId = $data['id'] ?? 0;
        $userId = $data['userId'] ?? 0;
        $firstname = $data['firstname'] ?? '';
        $lastname = $data['lastname'] ?? '';
        $address = $data['address'] ?? '';
        $city = $data['city'] ?? '';
        $pincode = $data['pincode'] ?? 0;
        $mobile = $data['mobile'] ?? '';
        $email = $data['email'] ?? '';
        $state = $data['state'] ?? '';
        $country = $data['country'] ?? '';
        $setDefault = $data['setdefault'] ?? 0;
        $status = 0;
        $message = '';
        $saveAction = 1;
        if ( empty($firstname) ) {
            $saveAction = 0;
            $message = 'Please enter First Name!';
        } else if ( empty($lastname) ) {
            $saveAction = 0;
            $message = 'Please enter last Name!';
        } else if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            $saveAction = 0;
            $message = 'Please enter a valid email id!';
        } else if ( !preg_match($this->REG_MOBILE, $mobile) ) {
            $saveAction = 0;
            $message = 'Mobile number should be 10 digit!';
        } else if ( empty($address) ) {
            $saveAction = 0;
            $message = 'Please enter your address!';
        } else if ( !preg_match($this->REG_PINCODE, $pincode) ) {
            $saveAction = 0;
            $message = 'Pincode should be 6 digit numeric number!';
        } else if ( empty($city) ) {
            $saveAction = 0;
            $message = 'Please enter city name!';
        } else if ( empty($state) ) {
            $saveAction = 0;
            $message = 'Please select your state!';
        } else if ( empty($country) ) {
            $saveAction = 0;
            $message = 'Please select your country!';
        }
        if (  $saveAction ) {
            $addressTable = TableRegistry::get('SubscriptionApi.Addresses');
            if ($setDefault == 1) {$addressTable->query()->update()->set(['set_default' => '0'])->where(['customer_id' => $userId])->execute();}
            $query = ($addressId > 0) ? $addressTable->get($addressId) : $addressTable->newEntity();
            $query->customer_id = $userId;
            $query->firstname = $firstname;
            $query->lastname = $lastname;
            $query->address = $address;
            $query->city = $city;
            $query->pincode = $pincode;
            $query->mobile = $mobile;
            $query->email = $email;
            $query->state = $state;
            $query->country = $country;
            $query->set_default = $setDefault;
            if ($addressTable->save($query)) {
                $status = 1;
                $message = 'Record save successfully!';
            } else {
                $message = 'Sorry, Please try again!';
            }
        }
        return ['status' => $status, 'message' => $message];
    }

    /***** This is SubscriptionApi Plugin *****///get customers addresses
    public function getAddresses($customerId = 0)
    {
        $data = [];
        if ($customerId > 0) {
            $query = TableRegistry::get('SubscriptionApi.Addresses')->find('all', ['conditions' => ['customer_id' => $customerId], 'order' => ['id' => 'DESC']])->hydrate(false)->toArray();
            if (!empty($query)) {
                if (!in_array('1', array_column($query, 'set_default'))) {
                    $query[0]['set_default'] = "1";
                }
            }
            $data['address'] = $query;
            $data['states'] = $this->getStates();
            $data['locations'] = TableRegistry::get('SubscriptionApi.Locations')->find('all', ['fields' => ['id', 'title', 'code','code2'], 'conditions' => ['is_active' => 'active'], 'order' => ['title']])->hydrate(false)->toArray();
        }
        return $data;
    }

    /***** This is SubscriptionApi Plugin *****///get customers addresses
    public function addCustomerReviews($userId, $itemId, $rating, $title, $description)
    {
        $status = false;
        if (($userId > 0) && ($itemId > 0) && ($rating > 0) && !empty($title) && !empty($description)) {
            $dataTable = TableRegistry::get('SubscriptionApi.Reviews');
            $review = $dataTable->newEntity();
            $review->customer_id = $userId;
            $review->product_id = $itemId;
            $review->location_id = Configure::read('countryApiId');
            $review->title = $title;
            $review->description = $description;
            $review->rating = $rating;
            $review->offer = 1;
            $review->location_ip = $this->request->clientIp();
            $status = ($dataTable->save($review)) ? true : false;
        }
        return $status;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getMiniCart($userId)
    {
        $data = [];
        $this->Product = new ProductComponent(new ComponentRegistry());
        $dataTable = TableRegistry::get('SubscriptionApi.Products');
        $query = $dataTable->find('all', ['fields' => ['id', 'sku_code', 'url_key', 'size', 'unit', 'gender', 'is_stock', 'discount', 'discount_from', 'discount_to'], 'conditions' => ['Products.is_active' => 'active']])
            ->contain([
                'ProductPrices' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'id', 'title', 'name','price'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    }
                ],
                'ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                    }
                ],
                'ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'product_id', 'title', 'alt_text', 'img_thumbnail'])->where(['ProductImages.is_thumbnail' => 1, 'ProductImages.is_active' => 'active']);
                    }
                ]
            ])
            ->matching("Carts", function ($q) use ($userId) {
                return $q->select(['id', 'quantity'])->where(['Carts.customer_id' => $userId]);
            })
            ->hydrate(0)
            ->toArray();
        //pr($query); die;
        foreach ($query as $value) {
            $price    = $value['product_prices'][0]['price'];
            $discount = $this->Product->discountValidity($price, $value['discount'], $value['discount_from'], $value['discount_to']);
            $price    = $discount['price'] ?? $price;
            $data[] = [
                'id' => $value['id'],
                'sku_code' => $value['sku_code'],
                'url_key' => $value['url_key'],
                'size' => $value['size'],
                'unit' => $value['unit'],
                'cart_id' => $value['_matchingData']['Carts']['id'],
                'cart_quantity' => $value['_matchingData']['Carts']['quantity'],
                'cart_quantity_price' => $price * $value['_matchingData']['Carts']['quantity'],
                'is_stock' => ($value['is_stock'] == 'in_stock') ? true : false,
                'title' => $value['product_prices'][0]['title'],
                'price' => $price,
                'discount' => $discount,
                'gender' => $value['gender'],
                'image' => $value['product_images'][0]['img_thumbnail'] ?? ''
            ];
        }
        return $data;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getTrackUrl($courierId, $trackCode){
        $res = '';
        if( !empty($courierId) && !empty($trackCode) ){
            if( $courierId == 3 ){
                $res = 'https://www.delhivery.com/track/package/'.$trackCode;
            }else{
                $res = 'https://shiprocket.co/tracking/'.$trackCode;
            } 
        }
        return $res;
    }			

    /***** This is SubscriptionApi Plugin *****/
    public function getOrders($customerId, $orderBy = '', $offset = 0)
    {
        $data = [];
        $limit = 50;
        try {
            $filterCondition['Orders.customer_id'] = $customerId;
            if (!empty($orderBy)) {
                $filterCondition['Orders.status'] = 'cancelled';
            } else {
                //$filterCondition['Orders.status !='] = 'cancelled';
            }
            $query = TableRegistry::get('SubscriptionApi.Orders')->find('all', ['fields' => ['id', 'payment_mode', 'product_total', 'payment_amount', 'discount', 'ship_amount', 'mode_amount', 'coupon_code', 'tracking_code', 'status', 'created', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode', 'shipping_country', 'shipping_email', 'shipping_email', 'shipping_phone', 'debit_points', 'credit_points', 'transaction_ip'], 'limit' => $limit, 'order' => ['Orders.id' => 'DESC'], 'conditions' => $filterCondition])
            ->contain([
                'Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['title','price_logo'=>'currency_logo']);
                    }
                ],
                'Couriers' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id','title']);
                    }
                ],
                'PaymentMethods' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['title']);
                    }
                ],
                'OrderDetails' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['order_id', 'product_id', 'id', 'title', 'sku_code', 'size', 'price', 'quantity', 'discount']);
                    }
                ],
                'OrderComments' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['order_id', 'id', 'given_by', 'status', 'comment']);
                    }
                ],
                'OrderDetails.Products' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['url_key']);
                    }
                ]
            ])
            ->hydrate(0)->toArray();
            $count = 0;
            foreach ($query as $value) {
                $orderDetails = array_map(function($v){
                    $queryImg = TableRegistry::get('SubscriptionApi.ProductImages')->find('all', ['fields' => ['img_small'], 'conditions' => ['product_id' => $v['product_id'], 'is_small' => 1, 'is_active' => 'active']])->toArray();
                    $v['product']['image'] = $queryImg[0]->img_small ?? '';
                    $v['product']['id'] = $v['product_id'];
                    return array_slice($v, 2);
                }, $value['order_details']);
                
                $data[$count] = $value;
                $data[$count]['tracking_link'] = $this->getTrackUrl($value['courier']['id'] ?? 0, $value['tracking_code']);
                $data[$count]['payment_method'] = $value['payment_method']['title'] ?? '';
                $data[$count]['courier'] = $value['courier']['title'] ?? '';
                $data[$count]['location'] = $value['location']['title'] ?? '';
                $data[$count]['created'] = date("d M Y", strtotime($value['created']));
                $data[$count]['order_details'] = $orderDetails;
                $data[$count]['order_prefix'] = PC['ORDER_PREFIX'];
                $data[$count]['order_comments'] = array_map( function($v){return array_slice($v,1); }, $value['order_comments']);
                $count++;
            }
        } catch (\Exception $e) {}
        //pr($data);die;
        return $data;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getOrdersDetails($customerId = 0, $orderId = 0, $emailContent=1)
    { //$userId = 44593; $orderNumber=100075942;
        $data = [];
        $query = TableRegistry::get('SubscriptionApi.Orders')->find('all', ['fields' => ['id', 'payment_mode', 'product_total', 'payment_amount', 'discount', 'ship_amount', 'mode_amount', 'coupon_code', 'tracking_code', 'status', 'created', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode', 'shipping_country', 'shipping_email', 'shipping_phone', 'debit_points', 'credit_points', 'debit_cash', 'credit_cash','debit_voucher', 'credit_voucher', 'created', 'transaction_ip'], 'conditions' => ['Orders.customer_id' => $customerId, 'Orders.id' => $orderId]])
        ->contain([
            'Locations' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['title','price_logo'=>'currency_logo']);
                }
            ],
            'Customers' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id','firstname', 'lastname','email']);
                }
            ],
            'Couriers' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id','title']);
                }
            ],
            'PaymentMethods' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['title']);
                }
            ],
            'OrderDetails' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['order_id', 'product_id', 'id', 'title', 'sku_code', 'size', 'price', 'quantity', 'discount']);
                }
            ],
            'OrderComments' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['order_id', 'id', 'given_by', 'status', 'comment']);
                }
            ],
            'OrderDetails.Products' => [
                'queryBuilder' => function ($q) {
                    return $q->select(['id','url_key']);
                }
            ]
        ])
        ->hydrate(0)->toArray();
        $count = 0;
        foreach ($query as $value) {
            $orderDetails = array_map(function($v){
                $queryImg = TableRegistry::get('SubscriptionApi.ProductImages')->find('all', ['fields' => ['img_small'], 'conditions' => ['product_id' => $v['product_id'], 'is_small' => 1, 'is_active' => 'active']])->toArray();
                $v['product']['image'] = $queryImg[0]->img_small ?? '';
                //$v['product']['id'] = $v['product_id'];
                return array_slice($v, 1);
            }, $value['order_details']);
            $data = $value;
            $data['tracking_link'] = $this->getTrackUrl($value['courier']['id'] ?? 0, $value['tracking_code']);
            $data['payment_method'] = $value['payment_method']['title'] ?? '';
            $data['courier'] = $value['courier']['title'] ?? '';
            $data['location'] = $value['location']['title'] ?? '';
            $data['created'] = date("d F Y", strtotime($value['created']));
            $data['order_details'] = $orderDetails;
            $data['order_prefix'] = PC['ORDER_PREFIX'];            $data['order_comments'] = array_map( function($v){return array_slice($v,1); }, $value['order_comments']);
            if ( $emailContent ) {
                $data['content'] = $this->productListInEmail($orderDetails);
            }
        }
        //pr($data);
        return $data;
    }

    public function productListInEmail ($productList) {
        $content = '';
        foreach ($productList as $value) {
            $content .= 
            '<table width="100%" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                            <tr>
                                <td>
                                    <table width="100" border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="'.$value['product']['image'].'" alt="'.$value['title'].'" width="100%" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                    </table>
                                </td>
                                
                                <td style="border-left:1px solid #ccc;" width="15">&nbsp;</td>
                                
                                <td>
                                    
                                    <table width="250" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p style="font-size:14px; color:#3b4e76; margin:0; font-weight:700;">
                                                    '.$value['title'].'
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <p style="font-size:13px; color:#363636; margin:0;">
                                                                Size '.$value['size'].'
                                                            </p>
                                                        </td>
                                                        <td align="right">
                                                            <p style="font-size:13px; color:#363636; margin:0; font-style:italic;">
                                                                '.$value['quantity'].' Qty
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p style="font-size:14px; color:#363636; margin:0; font-weight:700;">
                                                    '.number_format($value['price'], 2).'
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            ';
        }
        return $content;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function reorderOrder($customerId, $orderId)
    {
        $cartItem = [];
        $cartStatus = false;
        $status = false;
        $message = '';
        $productsTable = TableRegistry::get('SubscriptionApi.Products');
        $cartsTable = TableRegistry::get('SubscriptionApi.Carts');
        $order = $this->getOrdersDetails($customerId, $orderId);
        if (isset($order['order_details']) && count($order['order_details']) > 0) {
            foreach ($order['order_details'] as $v) {
                $prod = $productsTable->find('all', ['conditions' => ['id' => $v['product_id'], 'is_stock' => 'in_stock', 'is_active' => 'active']])->toArray();
                if (empty($prod)) {
                    $message = 'Sorry, some product are not available!';
                }else{
                    $cartStatus = true;
                    $cartItem[] = [
                        'product_id' => $v['product_id'],
                        'quantity' => $v['quantity'],
                    ];
                }
            }
        
        }
        if ((count($cartItem) > 0) && $cartStatus) {
            $cartsTable->query()->delete()->where(['customer_id' => $customerId])->execute();
            foreach ($cartItem as $v) {
                $cart = $cartsTable->newEntity();
                $cart->customer_id = $customerId;
                $cart->product_id = $v['product_id'];
                $cart->quantity = $v['quantity'];
                if ($cartsTable->save($cart)) {
                    $status = true;
                };
            }
        } 
        return ['status' => $status, 'message' => $message];
    }

    /***** This is SubscriptionApi Plugin *****/
    public function cancelOrder($customerId = 0, $orderId = 0)
    {
        $this->Store = new StoreComponent(new ComponentRegistry());
        $data = [];
        $status = false;
        $message = "Sorry, you can not do this!";
        $invoiceTable = TableRegistry::get('SubscriptionApi.Invoices');
        $dataTable = TableRegistry::get('SubscriptionApi.Orders');
        $query = $dataTable->find('all', ['conditions' => ['customer_id' => $customerId, 'id' => $orderId]])->toArray();
        foreach ($query as $value) {
            if (in_array($value->status, ['accepted', 'proccessing'])) {
                $order = $dataTable->get($value->id);
                $order->status = 'cancelled_by_customer';
                if ($dataTable->save($order)) {
                    $invoiceData = $invoiceTable->find('all', ['conditions' => ['order_id' => $value->id]])->toArray();
                    if (!empty($invoiceData) && isset($invoiceData[0])) {
                        $id_invoice = $invoiceData[0]->id;
                        $invoice = $invoiceTable->get($id_invoice);
                        $invoice->status = 'cancelled';
                        $invoiceTable->save($invoice);
                    }
                    $this->Store->reverseAfterCancelOrder($value->id);
                    $status = true;
                    $message = "Order cancelled successfully!";
                    $data = ['status' => 'cancel'];
                }
            }
        }
        return ['data' => $data, 'status' => $status, 'message' => $message];
    }

    /***** This is SubscriptionApi Plugin that get Customer wishlist items *****/
    public function getWishlist($customerId)
    {
        $data = [];
        $whishList = TableRegistry::get('SubscriptionApi.Wishlists')->find('all',['fields'=>'product_id'])->where(['customer_id' => $customerId])->hydrate(0)->toArray();
        $whishList = array_column($whishList, 'product_id');
        array_push($whishList,0);
        $query = TableRegistry::get('SubscriptionApi.ProductPrices')->find('all', ['fields' => ['title', 'price', 'short_description'], 'conditions' => ['ProductPrices.location_id' => Configure::read('countryApiId'),'ProductPrices.is_active' => 'active']])
            ->contain([
                'Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'title', 'price_logo'=>'currency_logo']);
                    }
                ],
                'Products' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'sku_code', 'url_key', 'size', 'unit', 'gender', 'is_stock'])->where(['Products.is_active' => 'active']);
                    }
                ],
                'Products.ProductImages' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['product_id', 'id', 'title', 'alt_text', 'img_base'])->where(['ProductImages.is_base' => 1, 'ProductImages.is_active' => 'active']);
                    }
                ]
            ])
            ->where(['ProductPrices.product_id IN' => $whishList])
            ->hydrate(0)
            ->toArray(); //pr($query);die;
        $count = 0;
        foreach ($query as $value) {
            $images = $value['product']['product_images'] ?? [];
            $images = array_map(function($v){return array_slice($v, 1); },$images);
            $data[$count] = $value;
            $data[$count]['product']['product_images'] = $images[0] ?? [];
            $count++;
        };
        return $data;
    }

    function getStates() {
        $data = [
        'india' => [
            ['id'=>1, 'title'=>'Andaman and Nicobar Islands'],
            ['id'=>2, 'title'=> 'Andhra Pradesh'],
            ['id'=>3, 'title'=> 'Arunachal Pradesh'],
            ['id'=>4, 'title'=> 'Assam'],
            ['id'=>5, 'title'=> 'Bihar'],
            ['id'=>6, 'title'=> 'Chandigarh'],
            ['id'=>7, 'title'=> 'Chhattisgarh'],
            ['id'=>8, 'title'=> 'Dadra and Nagar Haveli'],
            ['id'=>9, 'title'=> 'Daman and Diu'],
            ['id'=>10, 'title'=> 'Delhi'],
            ['id'=>11, 'title'=> 'Goa'],
            ['id'=>12, 'title'=> 'Gujarat'],
            ['id'=>13, 'title'=> 'Haryana'],
            ['id'=>14, 'title'=> 'Himachal Pradesh'],
            ['id'=>15, 'title'=> 'Jammu and Kashmir'],
            ['id'=>16, 'title'=> 'Jharkhand'],
            ['id'=>17, 'title'=> 'Karnataka'],
            ['id'=>18, 'title'=> 'Kerala'],
            ['id'=>19, 'title'=> 'Lakshadweep'],
            ['id'=>20, 'title'=> 'Madhya Pradesh'],
            ['id'=>21, 'title'=> 'Maharashtra'],
            ['id'=>22, 'title'=> 'Manipur'],
            ['id'=>23, 'title'=> 'Meghalaya'],
            ['id'=>24, 'title'=> 'Mizoram'],
            ['id'=>25, 'title'=> 'Nagaland'],
            ['id'=>26, 'title'=> 'Orissa'],
            ['id'=>27, 'title'=> 'Puducherry'],
            ['id'=>28, 'title'=> 'Punjab'],
            ['id'=>29, 'title'=> 'Rajasthan'],
            ['id'=>30, 'title'=> 'Sikkim'],
            ['id'=>31, 'title'=> 'Tamil Nadu'],
            ['id'=>32, 'title'=> 'Telangana'],
            ['id'=>33, 'title'=> 'Tripura'],
            ['id'=>34, 'title'=> 'Uttar Pradesh'],
            ['id'=>35, 'title'=> 'Uttarakhand'],
            ['id'=>36, 'title'=> 'West Bengal']
        ],
        'usa' => [
                ['id'=>1, 'title'=>'Alabama'],
                ['id'=>2, 'title'=> 'Alaska'],
                ['id'=>3, 'title'=> 'Arizona'],
                ['id'=>4, 'title'=> 'Arkansas'],
                ['id'=>5, 'title'=> 'California'],
                ['id'=>6, 'title'=> 'Colorado'],
                ['id'=>7, 'title'=>'Connecticut'],
                ['id'=>8, 'title'=>'Delaware'],
                ['id'=>9, 'title'=>'Florida'],
                ['id'=>10, 'title'=>'Georgia'],
                ['id'=>11, 'title'=>'Hawaii'],
                ['id'=>12, 'title'=>'Idaho'],
                ['id'=>13, 'title'=>'Illinois'],
                ['id'=>14, 'title'=>'Indiana'],
                ['id'=>15, 'title'=>'Iowa'],
                ['id'=>16, 'title'=>'Kansas'],
                ['id'=>17, 'title'=>'Kentucky'],
                ['id'=>18, 'title'=>'Louisiana'],
                ['id'=>19, 'title'=>'Maine'],
                ['id'=>20, 'title'=>'Maryland'],
                ['id'=>21, 'title'=>'Massachusetts'],
                ['id'=>22, 'title'=>'Michigan'],
                ['id'=>23, 'title'=>'Minnesota'],
                ['id'=>24, 'title'=>'Mississippi'],
                ['id'=>25, 'title'=>'Missouri'],
                ['id'=>26, 'title'=>'Montana'],
                ['id'=>27, 'title'=>'Nebraska'],
                ['id'=>28, 'title'=>'Nevada'],
                ['id'=>29, 'title'=>'New Hampshire'],
                ['id'=>30, 'title'=>'New Jersey'],
                ['id'=>31, 'title'=>'New Mexico'],
                ['id'=>32, 'title'=>'New York'],
                ['id'=>33, 'title'=>'North Carolina'],
                ['id'=>34, 'title'=>'North Dakota'],
                ['id'=>35, 'title'=>'Ohio'],
                ['id'=>36, 'title'=>'Oklahoma'],
                ['id'=>37, 'title'=>'Oregon'],
                ['id'=>38, 'title'=>'Pennsylvania'],
                ['id'=>39, 'title'=>'Rhode Island'],
                ['id'=>40, 'title'=>'South Carolina'],
                ['id'=>41, 'title'=>'South Dakota'],
                ['id'=>42, 'title'=>'Tennessee'],
                ['id'=>43, 'title'=>'Texas'],
                ['id'=>44, 'title'=>'Utah'],
                ['id'=>45, 'title'=>'Vermont'],
                ['id'=>46, 'title'=>'Virginia'],
                ['id'=>47, 'title'=>'Washington'],
                ['id'=>48, 'title'=>'West Virginia'],
                ['id'=>49, 'title'=>'Wisconsin'],
                ['id'=>50, 'title'=>'Wyoming']
            ]
        ];
        return $data['india'];
    }

    function getReferEarn ($customerId) { 
        $currentTime = time();
        $query = TableRegistry::get('SubscriptionApi.Customers')->find()
            ->select(['firstname', 'lastname', 'email', 'mobile', 'r.order_id', 'r.status', 'r.created'])
            ->join([
                'table' => 'customer_referrals',
                'alias' => 'r',
                'type' => 'INNER',
                'conditions' => 'r.referral_id = Customers.id AND r.customer_id = '.$customerId
            ])
            ->hydrate(0)
            ->toArray();
        $earned = $redeemed = $holding = $pending = $expired = [];
        foreach( $query as $value ) {
            $earnTime = strtotime($value['r']['created']);
            $temp = [
                'name' => $value['firstname'].' '.$value['lastname'],
                'mobile' => $value['mobile'],
                'email' => $value['email'],
                'created' => date('d M Y', $earnTime)
            ];
            if ( ($value['r']['order_id'] != 0) && ($value['r']['status'] == 0) && ( $currentTime <= ($earnTime + PC['REFER_TIME']) ) ) {
                $earned[] = $temp;
            } else if ( ($value['r']['order_id'] != 0) && ($value['r']['status'] == 2) ) {
                $redeemed[] = $temp;
            } else if ( ($value['r']['order_id'] != 0) && ($value['r']['status'] == 1) && ( $currentTime <= ($earnTime + PC['REFER_TIME']) ) ) {
                $holding[] = $temp;
            } else if ( ($value['r']['order_id'] == 0) && ( $currentTime <= ($earnTime + PC['REFER_TIME']) ) ) {
                $pending[] = $temp;
            } else {
                $expired[] = $temp;
            }
        }
        $referData = ['earned'=>$earned, 'redeemed'=>$redeemed, 'holding'=>$holding, 'pending'=>$pending, 'expired'=>$expired];
        $referCode = $this->customerIdEncode($customerId);
        $referLink = PC['COMPANY']['website'].'/registration?refer='.$referCode;
        return ['refer'=>$referData, 'referCode'=>$referCode, 'referLink'=>$referLink];
    }
     

    public function customerIdEncode($customerId)
    {
        $nums = str_split($customerId);
        $encript = '';
        if(is_array($nums))
        {
            foreach($nums as $num)
                $encript .= REFER_AND_EARN[$num];
        }
        return $encript;
    }

    public function customerIDdecode($encript)
    {
        $num_array = array_flip(REFER_AND_EARN);
        $chars = str_split($encript);
        $decript = '';
        if(is_array($chars))
        {
            foreach($chars as $char)
            {
                if(isset($num_array[$char]))
                $decript .=$num_array[$char];
            }
            if ( !empty($decript) ) { $decript = (int)$decript; }
        }
        return $decript;
    }


}
