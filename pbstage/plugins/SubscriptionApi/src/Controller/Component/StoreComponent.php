<?php
namespace SubscriptionApi\Controller\Component;

use SubscriptionApi\Controller\Component\Customer;
use SubscriptionApi\Controller\Component\Delhivery;
use SubscriptionApi\Controller\Component\Shipvendor;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\Exception;
use Cake\I18n\Date;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class StoreComponent extends Component
{
    use MailerAwareTrait;
    public $images = [];
    public $addBooterPrice = 300;
    public function __construct () {
        $this->images = [
            [
                'alt'=> "Defaut Image",
                'base'=> PC['IMAGE'],
                'url'=> PC['IMAGE'],
                'small'=> PC['IMAGE'],
                'title'=> "Defaut Image"
            ]
        ];
    }
    public function getItems($skus = [])
    {
        $items = [];
        $orderSku = array_column($skus, 'sku_code');
        if (count($skus)) {
            $productTable = TableRegistry::get('SubscriptionApi.Products');
            $productImageTable = TableRegistry::get('SubscriptionApi.ProductImages');
            $query = $productTable->find('all', ['fields' => ['id', 'sku_code', 'short_description'], 'conditions' => ['sku_code IN' => $orderSku, 'is_active' => 'active']])
                ->hydrate(0)->toArray();
            foreach ($query as $item) {
                $productImages = $productImageTable->find('all', ['fields' => ['id', 'title', 'alt' => 'alt_text', 'url' => 'img_large'], 'conditions' => ['is_large' => '1', 'is_active' => 'active', 'product_id' => $item['id']]])->hydrate(false)->toArray();
                for ($i = 0; $i < count($skus); $i++) {
                    if (($item['sku_code'] == $skus[$i]['sku_code']) && ($item['sku_code'] != 'PB00000122')) {
                        $skus[$i]['images'] = $productImages;
                        $skus[$i]['short_description'] = $item['short_description'];
                        $items[] = $skus[$i];
                        break;
                    }
                }
            }
        }
        return $items;
    }

    public function createOrderToken($orderId, $customerId){
        return md5('(*$%'.$orderId .'&*^'. $customerId.'~@#$');
    }
    
    /***** This is Store Component of SubscriptionApi Plugin that get active cart data *****/
    public function getActiveCart($userId, $param = [] )
    {
        $cart_data = [];
        $cart_total = 0;
        $cart_quantity = 0;
        $weight = 0;
        $this->Product = new ProductComponent(new ComponentRegistry());
        $cartData = TableRegistry::get('SubscriptionApi.Carts')->find('all', ['conditions' => ['customer_id' => $userId]])
            ->contain([
                'Products'=>[
                    'queryBuilder'=>function($q){
                        return $q->select(['id','sku_code','url_key','quantity','size','unit','discount','discount_from','discount_to','gender','is_stock','is_active']);
                    }
                ],
                'Products.ProductPrices'=>[
                    'queryBuilder'=>function($q){
                        return $q->select(['id','product_id','title','name','price','price1','price2','price3','short_description','is_active'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
                    }
                ],
                'Products.ProductPrices.Locations' => [
                    'queryBuilder' => function ($q) {
                        return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                    },
                ],
                'Products.Brands'=>[
                    'queryBuilder'=>function($q){
                        return $q->select(['id', 'title', 'image','country_name']);
                    }
                ],
                'Products.ProductImages'=>[
                    'queryBuilder'=>function($q){
                        return $q->select(['id', 'product_id', 'title', 'alt' => 'alt_text', 'url' => 'img_small'])->where(['is_small' => '1', 'is_active' => 'active']);
                    }
                ],
                'Products.productCategories'=>[
                    'queryBuilder'=>function($q){
                        return $q->select(['product_id','category_id']);
                    }
                ],
                'Products.productCategories.Categories'=>[
                    'queryBuilder'=>function($q){
                        return $q->select(['id','title', 'name']);
                    }
                ]
            ])
            ->hydrate(0)
            ->toArray(); //pr($cartData); die;
        $product_cart_id = 0;
        $products = [];
        foreach ($cartData as $value) {
            $product_cart_id = $value['id'];
            //check if product are in stock, active, quantity, and cart quantity are availables
            if ($value['product']['is_stock'] == 'in_stock' && ($value['product']['is_active'] == 'active') && ($value['product']['quantity'] >= $value['quantity']) && ($value['quantity'] > 0) ) {
                //check if product prices, title, name are availables
                if( !empty($value['product']['product_prices']) ){
                    $price = $value['product']['product_prices'][0]['price'];
                    $discount = $this->Product->discountValidity($price, $value['product']['discount'], $value['product']['discount_from'], $value['product']['discount_to']);
                    $price  = $discount['price'] ?? $price;
                    $images =$value['product']['product_images'];
                    if ( empty ($images) ) {
                        $images = $this->images;
                    }
                    $tier1 = $value['product']['product_prices'][0]['price1'];
                    $tier2 = $value['product']['product_prices'][0]['price2'];
                    $tier3 = $value['product']['product_prices'][0]['price3'];
                    if ( empty( $discount ) &&  ($price > $this->addBooterPrice) ) {
                        $categoryIds = array_column($value['product']['product_categories'],'category_id');
                        $products[] = ['id' => $value['product']['id'], 'quantity' => $value['quantity'], 'price' => $price,'tier1' => $tier1,'tier2' => $tier2,'tier3' => $tier3,'category'=>$categoryIds, 'brand'=>$value['product']['brand']['id'] ?? 0];
                    }
                    $cart_data[] = [
                        'id'=>$value['product']['id'],
                        'name'=>$value['product']['product_prices'][0]['name'],
                        'title'=>$value['product']['product_prices'][0]['title'],
                        'price_logo'=>$value['product']['product_prices'][0]['price_logo'],
                        'sku'=>$value['product']['sku_code'],
                        'url_key'=>$value['product']['url_key'],
                        'size'=>$value['product']['size'],
                        'unit'=>$value['product']['unit'],
                        'cart_id'=>$product_cart_id,
                        'cart_quantity'=>$value['quantity'],
                        'price'=>$price,
                        'discount'=>$discount,
                        'gender'=>$value['product']['gender'],
                        'categories'=>array_column($value['product']['product_categories'],'category'),
                        'images'=>$images,
                        'brand'=>$value['product']['brand'],
                        'description'=>$value['product']['product_prices'][0]['short_description']
                    ];
                    $weight += $value['quantity'] * $value['product']['size'];
                    $cart_quantity += $value['quantity'];
                }else{
                    $this->revomeItemFromCart($product_cart_id);
                }
            }else{
                $this->revomeItemFromCart($product_cart_id);
            }
        }
        $coupon = $param['coupon'] ?? '';
        $customerEmail = $param['email'] ?? '';
        $coupon_total_discount = 0;
        $coupon_status = 0;
        $coupon_free_shipping = 'no';
        $coupon_message = '';
        if ((count($cart_data) == 1) && ( ($cart_data[0]['id'] == PC['PRIVE_PRODUCT_ID']) || ($cart_data[0]['price'] <= $this->addBooterPrice) ) ) {
            $this->revomeItemFromCart($product_cart_id);
            $cart_data = [];
            $cart_total = 0;
            $cart_quantity = 0;
            $weight = 0;
        } else {
            $this->Coupon = new CouponComponent(new ComponentRegistry());
            $res = $this->Coupon->getRulesByCoupon($coupon, $customerEmail, $products); //pr($products);//die;
            $coupon_total_discount = $res['couponDiscount'] ?? 0;
            $coupon_status = $res['status'] ?? 0;
            $coupon_free_shipping = $res['freeShip'] ?? 'no';
            $tier_status = $res['tierStatus'] ?? 0;
            $coupon_message = $res['msg'] ?? '';
            $coupon_products = $res['products'] ?? []; //pr($coupon_products);//die;
            $counter = count($cart_data);
            for ( $i = 0; $i < $counter; $i++ ) {
                $price = 0;
                $discounts = [];
                foreach ( $coupon_products as $dis ) {
                    if ( $tier_status && ($dis['id'] == $cart_data[$i]['id']) ) {
                        $price = $dis['tier'];
                        break;
                    } else if ( $dis['id'] == $cart_data[$i]['id'] ) {
                        $price = ( $dis['price'] - $dis['discount'] );
                        $original = $dis['price'];
                        $discounts = ['price'=>$price, 'original'=>$dis['price'], 'discount'=>$dis['discount']];
                        break;
                    }
                }
                if ( $price > 0 ) {
                    $cart_data[$i]['price'] = $price;
                    $cart_data[$i]['discount'] = $discounts;
                    $cart_total += $cart_data[$i]['cart_quantity'] * $price;
                } else {
                    $cart_total += $cart_data[$i]['cart_quantity'] * $cart_data[$i]['price'];
                }
            } 
        }
        //pr($cart_data);die;
        return [
            'cart' => $cart_data,
            'cart_total' => $cart_total,
            'cart_quantity' => $cart_quantity,
            'weight' => $weight,
            'coupon_total_discount' => $coupon_total_discount,
            'coupon_status' =>$coupon_status,
            'coupon_free_shipping' =>$coupon_free_shipping,
            'coupon_message' =>$coupon_message,
            'location'=>TableRegistry::get('SubscriptionApi.Locations')->get(Configure::read('countryApiId'), ['fields'=>['id','title','code','currency','logo'=>'currency_logo']])->toArray()
        ];
    }

    /***** SubscriptionApi Plugin, This is for added item into shopping cart *****/
    public function addItemIntoCart($userId, $itemId, $quantity)
    {
        $message = 'Sorry, Invalid data!';
        $status = 0;
        $userId = (int) $userId;
        $itemId = (int) $itemId;
        $quantity = (int) $quantity;
        if (($userId > 0) && ($itemId > 0) && ($quantity > 0)) {
            $cartTable = TableRegistry::get('SubscriptionApi.Carts');
            $query = $cartTable->find('all', ['conditions' => ['customer_id' => $userId, 'product_id' => $itemId]])->toArray();
            if (count($query) > 0) {
                $message = 'Sorry, Item already exists into your cart!';
            } else {
                $cart = $cartTable->newEntity();
                $cart->customer_id = $userId;
                $cart->product_id = $itemId;
                $cart->quantity = $quantity;
                $status = ($cartTable->save($cart)) ? 1 : 0;
                $message = $status ? 'One item added into cart!' : 'Sorry, try aogain!';
            }
        }
        return ['message' => $message, 'status' => $status];
    }

    /***** SubscriptionApi Plugin, This is for added item into shopping cart *****/
    public function updateItemIntoCart($id, $quantity, $userId=0, $productId=0)
    {
        $status = 0;
        try { //echo $quantity; die;
            $dataTable = TableRegistry::get('SubscriptionApi.Carts');
            if ( $userId > 0 && $productId > 0 ) { 
                if ( $userId && $productId ) {
                    $query = $dataTable->find('all', ['id'])->where(['product_id'=>$productId, 'customer_id'=>$userId])->hydrate(0)->toArray();
                    if ( count($query) ) {
                        $id = $query[0]['id'];
                    }
                }
            }
            if ( $id > 0 ) {
                $cart = $dataTable->get($id);
                $cart->quantity = $quantity;
                $status = ($dataTable->save($cart)) ? 1 : 0;
            }
        } catch (\Exception $ex) {}
        return $status;
    }

    /***** SubscriptionApi Plugin, This is for remove item from shopping cart *****/
    public function revomeItemFromCart($id, $userId=0, $productId=0)
    {
        $status = 0;
        try { //echo json_decode($_REQUEST); die;
            $dataTable = TableRegistry::get('SubscriptionApi.Carts');
            if ( $userId && $productId ) {
                $query = $dataTable->find('all', ['id'])->where(['product_id'=>$productId, 'customer_id'=>$userId])->hydrate(0)->toArray();
                if ( count($query) ) {
                    $id = $query[0]['id'];
                }
            }
            $cart = $dataTable->get($id);
            $status = ($dataTable->delete($cart)) ? 1 : 0;
        } catch (\Exception $ex) {}
        return $status;
    }

    /***** SubscriptionApi Plugin, This is for remove all item from shopping cart of a user *****/
    public function emptyCart($userId)
    {
        $status = 0;
        try {
            $cart = TableRegistry::get('SubscriptionApi.Carts')->query()->delete()->where(['customer_id' => $userId])->execute();
            $status = ($cart) ? 1 : 0;
        } catch (\Exception $ex) {}
        return $status;
    }

    /***** SubscriptionApi Plugin, This is for update stock after order placed *****/
    public function updateStockAfterOrderPlaced($details)
    {
        $productsTable = TableRegistry::get('SubscriptionApi.Products');
        foreach ($details as $value) {
            $query = $productsTable->find('all',['fields'=>['id'],'conditions'=>['id'=>$value['product_id']]]);
            if( !empty($query) ){
                $product = $productsTable->get($value['product_id']);
                $remainQty = $product->quantity - $value['quantity'];
                if ($remainQty <= $product->out_stock_qty) {
                    $product->is_stock = 'out_of_stock';
                }
                $product->quantity = $remainQty;
                $product->sold = $product->sold + 1;
                $productsTable->save($product);
            }
        }
        return 1;
    }

    /***** SubscriptionApi Plugin, This is for update stock after order cancel *****/
    public function updateStockAfterOrderCancel($details)
    {
        $productsTable = TableRegistry::get('SubscriptionApi.Products');
        foreach ($details as $value) {
            $product = $productsTable->get($value['productId']);
            $remainQty = $product->quantity + $value['quantity'];
            if ($remainQty > $product->out_stock_qty) {
                $product->is_stock = 'in_stock';
            }
            $product->quantity = $remainQty;
            $productsTable->save($product);
        }
        return 1;
    }

    /***** SubscriptionApi Plugin, This is for shipping calculation *****/
    public function calculateShippingFromQuantity($quantity)
    {
        $res = 0;
        switch( $quantity ){
            case 0: $res =  0; break;
            case 1: $res =  45; break;
            case 2: $res =  75; break;
            case 3: $res =  100; break;
            default:$res = 100 + ($quantity - 3) * 30;
        }
        return $res;
    }

    public function getCreditDetailsAfterOrder($order_amount, $credit_voucher_quantity)
    {
        $points = round(($order_amount * PC['POINTS_REUTRN']) / 100, 2);
        return ['points' => $points];
    }

    /***** SubscriptionApi Plugin, This is for get Active Cart Details *****/
    public function getActiveCartDetails($inputData=[])
    {
        $data = [];
        try {
            $userId = $inputData['customer_id']; //= 115760;
            $payment_method = $inputData['payment_method'] ?? '';
            $track_page = $inputData['track_page'] ?? '';
            $couponCode = $inputData['coupon_code'] ?? '';
            $countryCode2 = $inputData['country_code'] ?? '';
            $optionStatus = $inputData['optionStatus'] ?? 0;
            $pointsStatus = $inputData['points'] ?? 0;
            $cashStatus = $inputData['cash'] ?? 0;
            $voucherStatus = $inputData['voucher'] ?? 0;
            $pbPrive = $inputData['pb_prive'] ?? 0;
            $number_format = $inputData['number_format'] ?? 0;
            $shopping = $inputData['shopping'] ?? [];
            $cartQuantity = 0;
            $this->Customer = new CustomerComponent(new ComponentRegistry());
            $this->Product = new ProductComponent(new ComponentRegistry());
            $getCustomerData = TableRegistry::get('SubscriptionApi.Customers')->get($userId);
            $customer_points = $getCustomerData->points;
            $customer_cash = $getCustomerData->cash;
            $customer_voucher = $getCustomerData->voucher;
            $discount_amount = 0;
            $discount_points = 0;
            $discount_cash = 0;
            $discount_voucher = 0;
            $credit_points = 0;
            $credit_cash = 0;
            $credit_voucher = 0;
            $grand_total_before_shipping = 0;
            $shipping_amount = 0;
            $grand_total = 0;
            $payment_fees = 0;
            $grand_final_total = 0;
            $products = []; 
            $quantity = 0;
            $busterProducts = $this->Product->getBusterProduct();
            $busterIds = array_column($busterProducts, 'id');
            //create filter for coupon code validation
            $couponMsg = '';
            $referData = [];
            $cartCouponFilter = [];
            if ( !empty($couponCode) ) {
                $referData = $this->referralCode($userId, $couponCode);
                $couponMsg = $referData['message'];
                if ( empty($couponMsg) ) {
                    $cartData = $this->getActiveCart($userId, ['coupon'=>$couponCode, 'email'=>$getCustomerData->email]);
                }
            }
            // calculate sum of cart product with respective quantity
            if ( empty($cartData) ) {
                $cartData = $this->getActiveCart($userId); //pr($cartData); die;
            }
            $total_amount_of_cart = $cartData['cart_total'] ?? 0;
            $cartQuantity = $cartData['cart_quantity'] ?? 0;
            $shopping['cart'] = $cartData['cart'];
            foreach ($cartData['cart'] as $k=>$p) {
                $price = $p['discount']['original'] ?? $p['price'];
                if ( in_array($p['id'], $busterIds) ) {
                    $busterProducts = [];
                }
                $products[] = ['id' => $p['id'], 'quantity' => $p['cart_quantity'] ?? 1, 'price' => $price, 'category'=>array_column($p['categories'], 'id'), 'brand'=>$p['brand']['id'] ?? 0];
            }
            /* calculate sum of pack with respective quantity
            foreach ($shopping['pack'] as $k=>$p) {
                $quantity = $p['cart_quantity'] ?? 1;
                $shopping['pack'][$k]['cart_quantity'] = $quantity;
                $cartQuantity += $quantity;
                $total_amount_of_cart += $quantity * $p['price'];
            }*/
            $total_amount_after_discount = $total_amount_of_cart;

            if ( $total_amount_of_cart > 0) {
                //check pb points status and manage user points discount
                if ( $pointsStatus && ($customer_points > 0) ) {
                    if ( ($total_amount_of_cart > 0) && ($total_amount_of_cart < 500) ) {
                        $discount_points = ($total_amount_of_cart * PC['POINTS_DISCOUNT_1']) / 100;
                    } else if ( ($total_amount_of_cart >= 500) && ($total_amount_of_cart < 1000) ) {
                        $discount_points = ($total_amount_of_cart * PC['POINTS_DISCOUNT_2']) / 100;
                    } else if ( ($total_amount_of_cart >= 1000) && ($total_amount_of_cart < 2000) ) {
                        $discount_points = ($total_amount_of_cart * PC['POINTS_DISCOUNT_3']) / 100;
                    } else if ($total_amount_of_cart >= 2000) {
                        $discount_points = ($total_amount_of_cart * PC['POINTS_DISCOUNT_4']) / 100;
                    }
    
                    if ($discount_points > $customer_points) {
                        $discount_points = $customer_points;
                    }
                    if ($discount_points > $total_amount_after_discount) {
                        $discount_points = $total_amount_after_discount;
                    }
                    $discount_amount += $discount_points;
                    $total_amount_after_discount -= $discount_points;
                }
    
                //for cash amount
                if( $cashStatus && ($customer_cash > 0) )
                {
                    if($customer_cash > $total_amount_after_discount)
                    {
                        $discount_cash	= $total_amount_after_discount - 10;
                    } else {
                        $discount_cash  = $customer_cash;
                    }
                    $customer_cash                  -= $discount_cash;
                    $discount_amount				+= $discount_cash;
                    $total_amount_after_discount	-= $discount_cash;
                }
                
                $shipping_amount = $this->calculateShippingFromQuantity($cartQuantity);
    
                //open tag for coupon code
                $discount_coupon = 0;
                if ( !empty($couponCode) ) {
                    if ( empty($couponMsg) ) {
                        $coupon = $cartData;
                        if ($coupon['coupon_status']) {
                            $discount_coupon = $coupon['coupon_total_discount'];
                            $discount_amount = $discount_amount + $discount_coupon;
                            if ($coupon['coupon_free_shipping'] == 'yes') {
                                $shipping_amount = 0;
                            }
                        } else {
                            $couponCode = '';
                        }
                        $couponMsg = $coupon['coupon_message'];
                    } else {
                        $customer_cash += $referData['cash'];
                        $couponCode = '';
                    }
                }
                //close tag for coupon code
                $paymentMethodCode = '';
                $payment_method_data = $this->customPaymentMethods($userId);
                //$payment_method_data = TableRegistry::get('SubscriptionApi.PaymentMethods')->getPaymentMethods();
                foreach ($payment_method_data as $method ) {
                    if ( $method['id'] ==  $payment_method ) {
                        $payment_fees = $method['fees'];
                        $paymentMethodCode = $method['code'];
                        break;
                    }
                }
                //free shipping on prepaid orders and Rs 50 on regular orders
                $shipping_amount = 50; // ( $paymentMethodCode == 'cod' ) ? 50 : 0;
                $shipping_amount = ( $track_page == 'cart' ) ? 0 : $shipping_amount;
                //20% cashback on all orders.
                $credit_cash = round((float) $total_amount_after_discount / 5,2);
                $data['buster'] = ['products'=>$busterProducts, 'price'=>'299', 'cross'=>'599', 'label'=>'Add another perfume for <b>₹299</b> <del> ₹599</del> <span> 50% Off</span>'];
                $data['currency'] = "&#x20B9;";
                $data['cart_total'] = $total_amount_of_cart;
                $data['shopping'] = $shopping;
                $data['total_amount_after_discount'] = $total_amount_after_discount;
                $data['coupon_code'] = $couponCode;
                $data['coupon_msg'] = $couponMsg;
                $data['payment_method'] = $payment_method;
                $data['payment_method_data'] = $payment_method_data;
                $data['payment_fees'] = $payment_fees;
                $data['shipping_amount'] = $shipping_amount;
    
                $data['grand_total'] = ceil($total_amount_after_discount + $data['shipping_amount'] + $data['payment_fees']);
                $data['grand_total_at_cart'] = ceil($total_amount_after_discount + $data['shipping_amount']);
                $data['grand_final_total'] = ceil($total_amount_after_discount + $data['shipping_amount'] + $data['payment_fees']);
    
                $data['customer'] = [
                    'points' => $customer_points,
                    'cash' => $customer_cash,
                    'voucher' => $customer_voucher
                ];
                
                $data['credits'] = [
                    'points' => $credit_points,
                    'cash' => $credit_cash,
                    'voucher' => $credit_voucher,
                    'message' => 'Rs- '.$credit_cash.' will be credited into your account once the order is delivered.'
                ];
    
                $data['discounts'] = [
                    'amount' => $discount_amount,
                    'coupon' => $discount_coupon,
                    'points' => $discount_points,
                    'cash' => $discount_cash,
                    'voucher' => $discount_voucher,
                    'extra'=>0
                ];
            }    
        }catch(\Exception $e){ }
        return $data;
    }

    //Set custom payment method to test
    public function customPaymentMethods($userId) {
        //Afroj, Ganpati, Praween
        $customPaymentId = 5; //mobikwik=6, paytm=5 // 9953914266
        $payment_method_data = TableRegistry::get('SubscriptionApi.PaymentMethods')->getPaymentMethods();
        if ( in_array($userId, ['115759','44418','115760']) ) {
            $check = 1;
            foreach ($payment_method_data as $value) {
                if ( $value['id'] == $customPaymentId ) {
                    $check = 0;
                }
            }
            if ( $check ) {
                switch ($customPaymentId) {
                    case 6: 
                        $payment_method_data[] = ['id'=>6,'title'=>'MobiKwik','code'=>'mobikwik','fees'=>0,'message'=>''];
                        break;
                    case 5:
                        $payment_method_data[] = ['id'=>5,'title'=>'PayTM','code'=>'paytm','fees'=>0,'message'=>''];
                        break;
                    default:
                }
            }
        }
        return $payment_method_data;
    }

    /*****  SubscriptionApi Plugin, This is for debit account after order placed *****/
    public function updateWalletAfterPayment ( $orderId ) {
        $orderTable = TableRegistry::get('SubscriptionApi.Orders');
        $order = $orderTable->get($orderId);
        if ($order) {
            $transaction_type = 0;
            $id_referrered_customer = 0;
            $voucher = $order->debit_voucher;
            $cash = $order->debit_cash;
            $points = $order->debit_points;
            $comments = "Wallet deducted for placing Order #" . $orderId;
            $this->logPBWallet($order->customer_id, $transaction_type, $id_referrered_customer, $orderId, $cash, $points, $voucher, $comments);
        }
    }

    public function updateWalletAfterDelivery($orderId)
    {
        $orderTable = TableRegistry::get('SubscriptionApi.Orders');
        $order = $orderTable->get($orderId);
        if ($order && $order->is_points_credited == 0) {
            $order->is_points_credited = 1;
            $orderTable->save($order);
            $transaction_type = 1;
            $id_referrered = 0;
            $cash = $order->credit_cash;
            $points = $order->credit_points;
            $voucher = $order->credit_voucher;
            $comments = "Wallet credited after placing Order #" . $orderId;
            $this->logPBWallet($order->customer_id, $transaction_type, $id_referrered, $orderId, $cash, $points, $voucher, $comments);

            $customerTable = TableRegistry::get('SubscriptionApi.Customers');
            $customer = $customerTable->get($order->customer_id);
            if ($customer) {
                $id_referrer = $customer->id_referrer;
                if ( ( $id_referrer > 0 ) && ( $customer->referral_status == 0 ) ) {
                    $customer->referral_status = 1; // Set referral status to done
                    $customerTable->save($customer);
                    $cash     = 200.00; //($order->product_total * 5) / 100;
                    $points   = 0; //($order->product_total * 10) / 100;
                    $voucher  = 0;
                    $comments = "Wallet credited for reffering a customer who placed successfull Order #" . $orderId;
                    $this->logPBWallet($id_referrer, $transaction_type, $order->customer_id, $orderId, $cash, $points, $voucher, $comments);
                    $userData = ['id'=>$customer->id, 'email'=>$customer->email, 'name'=>$customer->firstname.' '.$customer->lastname, 'mobile'=>$customer->mobile, 'cash'=>$cash, 'toName'=>$order->shipping_firstname.' '.$order->shipping_lastname];
                    $this->getMailer('SubscriptionApi.Customer')->send('orderReferCredit', [$userData]);
                    $this->Sms = new SmsComponent(new ComponentRegistry());
                    $this->Sms->orderReferralTo($customer->mobile, $cash, $order->shipping_firstname.' '.$order->shipping_lastname);
                }
            }
        }
    }

    /*****Add Referral Code Amount in Wallets*******/
    public function referralCode($customerId, $code) {
        $comments = '';
        $cash = 0;
        $customerTable = TableRegistry::get('SubscriptionApi.Customers');
        try {
            $this->Customer = new CustomerComponent(new ComponentRegistry());
            $id_referrer = $this->Customer->customerIDdecode($code);
            $cust     = $customerTable->get($id_referrer);
            $customer = $customerTable->get($customerId);
            if ( empty($customer->id_referrer) || ($customer->id_referrer == 0) ) {                
                $customer->id_referrer = $id_referrer;
                $customer->referral_status = 1; // Set referral status to done
                $customerTable->save($customer);
                $orderId  = 0;
                $cash     = 100.00;
                $points   = 0; 
                $voucher  = 0;
                $comments = "Your accouunt has been credited with Rs $cash for using reffering code #".$code;
                $this->logPBWallet($customerId, 1, $id_referrer, $orderId, $cash, $points, $voucher, $comments);
                $userData = ['id'=>$customerId, 'email'=>$customer->email, 'name'=>$customer->firstname.' '.$customer->lastname, 'mobile'=>$customer->mobile, 'cash'=>$cash, 'code'=>$code];
                $this->getMailer('SubscriptionApi.Customer')->send('referCredit', [$userData]);
                $this->Sms = new SmsComponent(new ComponentRegistry());
                $this->Sms->referralCode($customer->mobile, $cash, $code);
            } else {
                $comments = "Sorry, you already credited your wallet for referral code!";
            }
        } catch (\Exception $e) {
        }
        return ['cash'=>$cash, 'message'=>$comments];
    }

    /***** SubscriptionApi Plugin, This is for debit/credit account  *****/
    public function logPBWallet($customerId, $transaction_type, $id_referrered_customer = 0, $orderId = 0, $cash = 0, $points = 0, $voucher = 0, $comments = '')
    {
        if ($cash > 0 || $points > 0 || $voucher > 0) {
            if ($customerId > 0) {
                $customerTable = TableRegistry::get('SubscriptionApi.Customers');
                $customer = $customerTable->get($customerId);
                if ($transaction_type == 1) {
                    $customer->points = $customer->points + $points;
                    $customer->cash = $customer->cash + $cash;
                    $customer->voucher = $customer->voucher + $voucher;
                    $customerTable->save($customer);
                } else if ($transaction_type == 0) {
                    $customer->points = $customer->points - $points;
                    $customer->cash = $customer->cash - $cash;
                    $customer->voucher = $customer->voucher - $voucher;
                    $customerTable->save($customer);
                }

                $pbPointsTable = TableRegistry::get('SubscriptionApi.CustomerLogs');
                $wallet_log = $pbPointsTable->newEntity();
                $wallet_log->customer_id = $customerId;
                $wallet_log->id_referrered_customer = $id_referrered_customer;
                $wallet_log->order_id = $orderId;
                $wallet_log->cash = $cash;
                $wallet_log->points = $points;
                $wallet_log->voucher = $voucher;
                $wallet_log->transaction_type = $transaction_type;
                $wallet_log->comments = $comments;
                $wallet_log->transaction_ip = $_SERVER['REMOTE_ADDR'];
                $pbPointsTable->save($wallet_log);
            }
        }
    }

    public function createInvoice($orderId)
    {
        try {
            $invoiceTable = TableRegistry::get('SubscriptionApi.Invoices');
            $invoiceOrder = $invoiceTable->find('all', ['fields' => ['id'], 'conditions' => ['order_id' => $orderId], 'limit' => 1])->toArray();

            if (empty($invoiceOrder)) {
                $orderTable = TableRegistry::get('SubscriptionApi.Orders');
                $order = $orderTable->get($orderId);
                $invoice = $invoiceTable->newEntity();
                $invoice->customer_id = $order->customer_id;
                $invoice->location_id = $order->location_id;
                $invoice->payment_method_id = $order->payment_method_id;
                $invoice->order_id = $orderId;
                $invoice->payment_mode = $order->payment_mode;
                $invoice->product_total = $order->product_total;
                $invoice->payment_amount = $order->payment_amount;
                $invoice->discount = $order->discount;
                $invoice->ship_method = $order->ship_method;
                $invoice->ship_amount = $order->ship_amount;
                $invoice->mode_amount = $order->mode_amount;
                $invoice->coupon_code = $order->coupon_code;
                $invoice->tracking_code = $order->tracking_code;
                $invoice->created = $order->created;
                $invoice->status = $order->status;
                $invoice->shipping_firstname = $order->shipping_firstname;
                $invoice->shipping_lastname = $order->shipping_lastname;
                $invoice->shipping_address = $order->shipping_address;
                $invoice->shipping_city = $order->shipping_city;
                $invoice->shipping_state = $order->shipping_state;
                $invoice->shipping_country = $order->shipping_country;
                $invoice->shipping_pincode = $order->shipping_pincode;
                $invoice->shipping_email = $order->shipping_email;
                $invoice->shipping_phone = $order->shipping_phone;
                $invoice->transaction_ip = $order->transaction_ip;
                $invoice->courier_id = $order->courier_id;

                if ( $invoiceTable->save($invoice) && ($invoice->id > 0) ) {
                    $orderDetailsTable = TableRegistry::get('SubscriptionApi.OrderDetails');
                    $orderDetails = $orderDetailsTable->find('all', ['conditions' => ['order_id' => $orderId]])->toArray();
                    $invoiceDetailsTable = TableRegistry::get('SubscriptionApi.InvoiceDetails');
                    foreach ($orderDetails as $temp_row) {
                        $invoice_detail = $invoiceDetailsTable->newEntity();
                        $invoice_detail->invoice_id = $invoice->id;
                        $invoice_detail->title = $temp_row['title'];
                        $invoice_detail->sku_code = $temp_row['sku_code'];
                        $invoice_detail->size = $temp_row['size'];
                        $invoice_detail->price = $temp_row['price'];
                        $invoice_detail->quantity = $temp_row['quantity'];
                        $invoice_detail->short_description = $temp_row['short_description'];
                        $invoiceDetailsTable->save($invoice_detail);
                    }
                }
            }
        } catch (\Exception $e) {}
    }

    public function cancelOrder($orderId = 0) {
        $dataTable = TableRegistry::get('SubscriptionApi.Orders');
        $query = $dataTable->find('all', ['conditions' => ['Orders.id' => $orderId]])->toArray();
        foreach ($query as $value) {
            if (in_array($value->status, ['accepted', 'proccessing'])) {
                $order = $dataTable->get($value->id);
                $order->status = 'cancelled';
                if ($dataTable->save($order)) {
                    $this->reverseAfterCancelOrder($value->id);
                    $this->changeInvoiceStatus($value->id, 'cancelled');
                    return true;
                }
            }
        }
        return false;
    }

    public function reverseAfterCancelOrder($orderId) {
        if ($orderId > 0) {
            $dataTable = TableRegistry::get('SubscriptionApi.Orders');
            $order = $dataTable->get($orderId);
            if (!empty($order)) {
                if ($order->is_points_reversed == 0) {
                    $order->is_points_reversed = 1;
                    if ($dataTable->save($order)) {
                        if ($order->debit_cash > 0 || $order->debit_points > 0 || $order->debit_voucher > 0) {
                            $transaction_type = 1; //account should be credit
                            $cash = $order->debit_cash;
                            $points = $order->debit_points;
                            $voucher = $order->debit_voucher;
                            $comments = "Wallet credited after cancelling an Order #" . $orderId;
                            $this->logPBWallet($order->customer_id, $transaction_type, 0, $orderId, $cash, $points, $voucher, $comments);
                        }
                        if ($order->credit_cash > 0 || $order->credit_points > 0 || $order->credit_voucher > 0) {
                            $transaction_type = 0; //Account should be debit
                            $cash = $order->credit_cash;
                            $points = $order->credit_points;
                            $voucher = $order->credit_voucher;
                            $comments = "Wallet debited after cancelling an Order #" . $orderId;
                            $this->logPBWallet($order->customer_id, $transaction_type, 0, $orderId, $cash, $points, $voucher, $comments);
                        }
                    }
                }
            }
        }
    }

    public function changeOrderStatus($orderId, $status) {
        $dataTable = TableRegistry::get('SubscriptionApi.Orders');
        $query = $dataTable->find('all', ['conditions' => ['Orders.id' => $orderId]])->toArray();
        foreach ($query as $value) {
            $previousStatus = $value->status;
            $order = $dataTable->get($value->id);
            $order->status = $status;
            if ($dataTable->save($order)) {
                $this->changeInvoiceStatus($value->id, $status);
                if ($status == 'cancelled' || $status == 'cancelled_by_customer') {
                    if ($previousStatus != 'cancelled' && $previousStatus != 'cancelled_by_customer') {
                        $this->reverseAfterCancelOrder($value->id);
                    }
                }
                return true;
            }
        }
        return false;
    }

    public function changeInvoiceStatus($orderId, $status) {
        $dataTable = TableRegistry::get('SubscriptionApi.Invoices');
        $query = $dataTable->find('all', ['conditions' => ['Invoices.order_id' => $orderId]])->toArray();
        foreach ($query as $value) {
            $invoice = $dataTable->get($value->id);
            $invoice->status = $status;
            if ($dataTable->save($invoice)) {
                return true;
            }
        }
        return false;
    }

    public function orderStatusEmails($orderId, $mailType) {
        $customerId = 0;
        $oDetails = [];
        $orderTable = TableRegistry::get('SubscriptionApi.Orders');
        $order = $orderTable->get($orderId);
        if (empty($order)) {
            exit;
        } else {
            $customerId = $order->customer_id;
        }
        $this->Customer = new CustomerComponent(new ComponentRegistry());
        $this->Sms = new SmsComponent(new ComponentRegistry());
        $oDetails = $this->Customer->getOrdersDetails($customerId, $orderId);

        if (!empty($oDetails)) {
            $oDetails['customerId'] = $customerId;
            $oDetails['currentDate'] = date("d F Y");
            $callMailFun = '';
            switch ( $mailType ) {
                case 'confirmed':
                    $callMailFun = 'orderConfirmed';
                    $this->updateStockAfterOrderPlaced($oDetails['order_details']);
                    $text = '';
                    $total = count($oDetails['order_details']);
                    if ( $total > 1) {
                        $total = $total - 1;
                        $text = $oDetails['order_details'][0]['title'] . " + $total";
                    } else {
                        $text = isset($oDetails['order_details'][0]['title']) ? $oDetails['order_details'][0]['title']:'';
                    }
                    $this->emptyCart($customerId);
                    $this->Sms->orderSend($oDetails['shipping_phone'], $orderId, $oDetails['payment_amount'], $oDetails['payment_method'], $text);
                    break;
                case 'delivered':
                    $callMailFun = 'orderDelivered';
                    $this->Sms->orderDelivered($oDetails['shipping_phone'], $orderId, $oDetails['currentDate']);
                    if ( ($oDetails['credit_cash'] > 0) || ($oDetails['credit_points'] > 0) || ($oDetails['credit_voucher'] > 0) ) {
                        $credits = [
                            'cash' => $oDetails['credit_cash'],
                            'points' => $oDetails['credit_points'],
                            'voucher' => $oDetails['credit_voucher']
                        ];    
                        $this->Sms->orderAccountCredit($oDetails['shipping_phone'], $credits);
                        $this->getMailer('SubscriptionApi.Customer')->send('orderAccountCredit', [$oDetails]);
                    }
                    break;
                case 'intransit':
                    $callMailFun = 'orderIntransit';
                    $this->Sms->orderIntransit($oDetails['shipping_phone'], $orderId, $oDetails['currentDate']);
                    break;
                case 'dispatched':
                    $callMailFun = 'orderDispatched';
                    $this->Sms->orderDispatched($oDetails['shipping_phone'], $orderId, $oDetails['currentDate']);
                    break;
                case 'cancelled':
                    $callMailFun = 'orderCancelled';
                    $this->Sms->orderCancellled($oDetails['shipping_phone'], $orderId);
                    break;
                case 'review':
                    $callMailFun = 'orderReview';
                    break;
                default: 
            }
            if ( !empty($callMailFun) ) {
                $this->getMailer('SubscriptionApi.Customer')->send($callMailFun, [$oDetails]);
            }
        }
        return $oDetails;
    }

    public function savePaymentResponse($orderId, $pgName, $response) {
        $status = 0;
        if (!empty($orderId) && !empty($pgName) ) {
            $pgTable = TableRegistry::get('SubscriptionApi.PgResponses');
            $pg = $pgTable->newEntity();
            $pg->order_id = $orderId;
            $pg->pg_name = $pgName;
            $pg->pg_data = json_encode($response);
            $saveResponse = $pgTable->save($pg);
            if ( !empty($saveResponse) ) {
                $status = 1;
            }
        }
        return $status;
    }

}
