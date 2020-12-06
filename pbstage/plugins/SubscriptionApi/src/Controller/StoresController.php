<?php
namespace SubscriptionApi\Controller;

use SubscriptionApi\Controller\AppController;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class StoresController extends AppController
{
    use MailerAwareTrait;
    public function initialize()
    {
        parent::initialize();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $this->loadComponent('SubscriptionApi.Store');
        $this->loadComponent('SubscriptionApi.Sms');
        $this->loadComponent('SubscriptionApi.Coupon');
        $this->loadComponent('SubscriptionApi.Customer');
        $this->loadComponent('SubscriptionApi.Membership');
        $this->loadComponent('SubscriptionApi.Shipvendor');
        $this->loadComponent('SubscriptionApi.Paypal');
        $this->loadComponent('SubscriptionApi.Razorpay');
        $this->loadComponent('SubscriptionApi.Mobikwik');
        $this->loadComponent('SubscriptionApi.Paytm');
    }

    public function index()
    {
        $userId = 115760;
        $res = '';
        $this->loadComponent('SubscriptionApi.Delhivery');
        //$res = $this->Delhivery->getWayBillNumber();
        $orderId = $this->request->getQuery('orderId');
        if ( $orderId > 0 ) {
            $this->Store->createInvoice($orderId);
            $res = $this->Shipvendor->pushOrder($orderId);
        }
        $otpMailer = [
            'customerId =' > $userId,
            'otp' => '12345',
            'name' => "Mohd Afroj",
            'email' => "",
            'amount' => "100.00",
            'mobile' => "7838799646",
        ];
        //$res = $this->getMailer('SubscriptionApi.Customer')->send('otpSend', [$otpMailer]);
        //$res = $this->Coupon->inOrderStatus('PRWEL74', PC['TEST_EMAIL']);
        //$res = $this->Store->updateWalletAfterDelivery('10000010');
        $this->loadComponent('SubscriptionApi.Product');
        $res = $this->Product->getBusterProduct();
        pr($res);
        die;
    }

    /***** SubscriptionApi Plugin, This is check pincode service *****/
    public function getPincode()
    {
        $data = [];
        $message = '';
        $status = 1;
        try {
            $pincode = $this->request->getQuery('pincode');
            $response = $this->Shipvendor->checkPincode($pincode);
        } catch(\Exception $e){
            $message = 'Sorry, invalid pincode request!';
            $response = ['message' => $message, 'status' => $status, 'data' => []];
        }
        echo json_encode($response);
        die;
    }

    /***** SubscriptionApi Plugin, This is generate otp for shopping cart *****/
    public function getOtp()
    {
        $data = [];
        $message = '';
        $status = 0;
        try {
            $userId = $this->request->getData('userId');
            $auth = $this->Customer->isAuth($userId);
            if ($auth['status']) {
                $customerTable = TableRegistry::get('SubscriptionApi.Customers');
                if ($this->request->is(['post'])) {
                    $mobile = $this->request->getData('mobile');
                    $amount = $this->request->getData('amount');
                    $name = $this->request->getData('name');
                    $email = $this->request->getData('email');
                    $otp = $this->Customer->generateOtp(); //param are length, default is 6 digit code
                    $customer = $customerTable->get($userId);
                    $customer->password = $otp;
                    if ( $customerTable->save($customer) ) {
                        $info = $this->Sms->otpSend($mobile, $otp, $amount);
                        $message = 'We have sent OTP on entered mobile number and registered email id, if not received within 3 minutes please click on resend otp!';
                        $status = 1;
                        if ( $this->Customer->getDmainEmailStatus() ) {
                            $otpMailer = [
                                'customerId =' > $userId,
                                'otp' => $otp,
                                'name' => $name,
                                'email' => $email,
                                'amount' => $amount,
                                'mobile' => $mobile,
                            ];
                            $this->getMailer('SubscriptionApi.Customer')->send('otpSend', [$otpMailer]);
                        } 
                    } else {
                        $message = 'Sorry, OTP not generate, please contact to customer support!';
                    }
                } else if ($this->request->is(['put'])) {
                    $otp = $this->request->getData('otp');
                    $customer = $customerTable->get($userId);
                    //echo md5($otp) ."<br />". $customer->password; 
                    if ( (md5($otp) == $customer->password) || ($otp == PC['OTP'])) {
                        $status = 1;
                        $message = 'OTP verified, Please wait ...';
                    } else {
                        $message = 'Sorry, please enter valid opt!';
                    }
                } else {
                    $message = 'Sorry, invalid method!';
                }
            } else {
                $message = 'Sorry, invalid request!';
            }
        } catch (\Exception $e) { $message = 'Sorry, bad request!'; }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** SubscriptionApi Plugin, This is customer shopping cart operation *****/
    public function customerCart()
    {
        $userId = $this->request->getQuery('userId');
        $param['coupon'] = $this->request->getQuery('offerCoupon', ''); //ROSHNI20
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0; 
        //$this->Store->getActiveCart(PC['USERID']);
        if ($auth['status']) {
            if ($this->request->is(['post'])) {
                $itemId = (int) $this->request->getData('itemId');
                $quantity = (int) $this->request->getData('quantity');
                $info = $this->Store->addItemIntoCart($userId, $itemId, $quantity);
                $message = $info['message'];
                $status = $info['status'];
                $cart = $this->Store->getActiveCart($userId, $param);
                $data['cart'] = $cart['cart'] ?? [];
                $this->response->statusCode(201);
                $this->response->send();
            } else if ($this->request->is(['delete'])) {
                $id = $this->request->getQuery('id');
                $productId = $this->request->getQuery('productId');
                $status = $this->Store->revomeItemFromCart($id, $userId, $productId);
                $message = ($status) ? 'Item removed from cart!' : 'Sorry, Item not remove from cart!';
                $cart = $this->Store->getActiveCart($userId, $param);
                $data['cart'] = $cart['cart'] ?? [];
            } else if ($this->request->is(['put'])) {
                $id = $this->request->getData('id', 0);
                $quantity = (int) $this->request->getData('quantity');
                $productId = (int) $this->request->getData('productId');
                if ( $quantity > 0) {
                    $status = $this->Store->updateItemIntoCart($id, $quantity, $userId, $productId);
                    $message = ($status) ? 'Item updated into cart!' : 'Sorry, Item not updated into cart!';
                    $cart = $this->Store->getActiveCart($userId, $param);
                    $data['cart'] = $cart['cart'] ?? [];
                    $this->response->statusCode(201);
                    $this->response->send();
                } else {
                    $message = 'Sorry, Qunatity should be greater than zero!';
                }
            } else {
                $message = 'Sorry, tempered request!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** SubscriptionApi Plugin, This is for get customer shopping cart details *****/
    public function getActiveCart()
    {
        $userId = $this->request->getQuery('userId', '0'); //16597
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0;
        if ($auth['status']) {
            $inputData = [
                'customer_id' => $userId,
                'payment_method' => $this->request->getData('paymentMethod', '2'),
                'pincode' => $this->request->getData('pincode', ''),
                'track_page' => $this->request->getData('trackPage', ''),
                'coupon_code' => $this->request->getData('couponCode', ''),
                'country_code' => $this->request->getData('countryCode2', ''),
                'optionStatus' => $this->request->getData('optionStatus', '0'),
                'points' => $this->request->getData('points', '0'),
                'cash' => $this->request->getData('cash', '0'),
                'voucher' => $this->request->getData('voucher', '0'),
                'shopping' => $this->request->getData('shopping', []),
                'pb_prive' => $this->request->getData('pbPrive', '0'),
            ];
            $data = $this->Store->getActiveCartDetails($inputData);
            $status = 1;
            //$page = $this->request->getData('trackPage', null);
            //$this->Customer->trackPage($userId, $page);
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    public function getActiveCartTest()
    {
        $userId = $this->request->getQuery('userId', '0'); //16597
        $inputData = [
            'customer_id' => $userId,
            'payment_method' => $this->request->getData('paymentMethod', '2'),
            'pincode' => $this->request->getData('pincode', ''),
            'track_page' => $this->request->getData('trackPage', ''),
            'coupon_code' => $this->request->getData('couponCode', 'GETFOR399'),
            'country_code' => $this->request->getData('countryCode2', ''),
            'optionStatus' => $this->request->getData('optionStatus', '1'),
            'points' => $this->request->getData('points', '0'),
            'cash' => $this->request->getData('cash', '0'),
            'voucher' => $this->request->getData('voucher', '0'),
            'shopping' => $this->request->getData('shopping', []),
            'pb_prive' => $this->request->getData('pbPrive', '0'),
        ];
        $data = $this->Store->getActiveCartDetails($inputData);
        pr($data);
        die;
    }

    /***** SubscriptionApi Plugin, This is for change customer account *****/
    public function changeUserAccount()
    {
        $userId = $this->request->getQuery('userId');
        $username = $this->request->getQuery('username');
        $password = $this->request->getQuery('password');

        $data = [];
        $message = '';
        $status = false;
        if (($this->request->is(['put']) || $this->request->is(['post'])) && ($userId > 0) && !empty($username) && !empty($password)) {
            $data = $this->Customer->validateLogin($username, $password, $userId);
            if (count($data) > 0) {
                $status = 1;
            } else {
                $message = 'Sorry, Please enter valid username and password!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** SubscriptionApi Plugin, This is for place order as pending status *****/
    public function createOrder()
    {
        $data = [];
        $message = '';
        $status = false;
        $is_error = 0;

        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);
        if ($this->request->is(['post']) && $auth['status']) {
            $shipping_firstname = $this->request->getData('shipping_firstname');
            $shipping_lastname = $this->request->getData('shipping_lastname');
            $shipping_address = $this->request->getData('shipping_address');
            $shipping_city = $this->request->getData('shipping_city');
            $shipping_state = $this->request->getData('shipping_state');
            $shipping_country = $this->request->getData('shipping_country');
            $shipping_email = $this->request->getData('shipping_email');
            $shipping_mobile = $this->request->getData('shipping_mobile');
            $shipping_pincode = $this->request->getData('shipping_pincode');

            $pbPrive = $this->request->getData('pbPrive', '0');
            $couponCode = $this->request->getData('couponCode');
            $paymentMethod = $this->request->getData('paymentMethod');
            $optionStatus = $this->request->getData('optionStatus', '0');
            $points = $this->request->getData('points');
            $cash = $this->request->getData('cash');
            $voucher = $this->request->getData('voucher');
            //$paymentMethodData = TableRegistry::get('SubscriptionApi.PaymentMethods')->getPaymentMethods();
            $paymentMethodData = $this->Store->customPaymentMethods($userId);

            if ($shipping_firstname == '') {
                $is_error = 1;
                $message = "Please enter your shipping first name.";
            } else if ($shipping_lastname == '') {
                $is_error = 1;
                $message = "Please enter your shipping last name.";
            } else if ($shipping_address == '') {
                $is_error = 1;
                $message = "Please enter your shipping address.";
            } else if (strpos($shipping_address, "&") != false) {
                $is_error = 1;
                $message = "Please remove & char from shipping address.";
            } else if ($shipping_pincode == '') {
                $is_error = 1;
                $message = "Please enter your shipping pincode.";
            } else if ($shipping_city == '') {
                $is_error = 1;
                $message = "Please enter your shipping city.";
            } else if ($shipping_email == '') {
                $is_error = 1;
                $message = "Please enter your shipping email.";
            } else if ($shipping_country == '') {
                $is_error = 1;
                $message = "Please select your shipping country.";
            } else if ($shipping_mobile == '') {
                $is_error = 1;
                $message = "Please enter your shipping mobile.";
            } else if ( !in_array($paymentMethod, array_column($paymentMethodData, 'id')) ) {
                $is_error = 1;
                $message = "Please choose a payment method.";
            }
            
            if ($is_error == 0) {
                $customerTable = TableRegistry::get('SubscriptionApi.Customers');
                $getCustomerData = $customerTable->get($userId);
                $inputData = [
                    'customer_id' => $userId,
                    'payment_method' => $paymentMethod,
                    'pincode' => $shipping_pincode,
                    'coupon_code' => $couponCode,
                    'country_code' => $this->request->getData('countryCode2', ''),
                    'points' => $points,
                    'cash' => $cash,
                    'voucher' => $voucher,
                    'shopping' => $this->request->getData('shopping', []),
                    'pb_prive' => $pbPrive,
                ];

                $cartAmountDetails = $this->Store->getActiveCartDetails($inputData);
                try {
                    $paymentMethodId = $cartAmountDetails['payment_method'] ?? 0;
                    $paymentData = TableRegistry::get('SubscriptionApi.PaymentMethods')->getPaymentGatewayById($paymentMethodId);
                    $order = [
                        'customer_id' => $userId,
                        'payment_method_id' => $paymentMethodId,
                        'product_total' => $cartAmountDetails['total_amount_after_discount'],
                        'payment_amount' => $cartAmountDetails['grand_final_total'],
                        'discount' => $cartAmountDetails['discounts']['amount'],
                        'ship_amount' => $cartAmountDetails['shipping_amount'],
                        'ship_discount' => $cartAmountDetails['discounts']['extra'],
                        'mode_amount' => $cartAmountDetails['payment_fees'],
                        'payment_mode' => ($paymentData['code'] == 'cod') ? 'postpaid':'prepaid',
                        'coupon_code' => $couponCode,
                        'mobile' => $auth['data']['mobile'],
                        'email' => $auth['data']['email'],
                        'shipping_firstname' => $shipping_firstname,
                        'shipping_lastname' => $shipping_lastname,
                        'shipping_address' => $shipping_address,
                        'shipping_city' => $shipping_city,
                        'shipping_state' => $shipping_state,
                        'shipping_pincode' => $shipping_pincode,
                        'shipping_email' => $shipping_email,
                        'shipping_phone' => $shipping_mobile,
                        'debit_points' => $cartAmountDetails['discounts']['points'],
                        'debit_cash' => $cartAmountDetails['discounts']['cash'],
                        'debit_voucher' => $cartAmountDetails['discounts']['voucher'],
                        'credit_points' => $cartAmountDetails['credits']['points'],
                        'credit_cash' => $cartAmountDetails['credits']['cash'],
                        'credit_voucher' => $cartAmountDetails['credits']['voucher'],
                        'order_log' => ['products' => $cartAmountDetails]
                    ];
                    $orderTable = TableRegistry::get('SubscriptionApi.Orders');
                    $res = $orderTable->createNew($order);
                    if ( !empty($res) ) {
                        $status = true;
                        $shoppingCart = $cartAmountDetails['shopping']['cart'] ?? [];
                        $shoppingPack = $cartAmountDetails['shopping']['pack'] ?? [];
                        $orderDetails = [];
                        foreach ($shoppingCart as $item) {
                            $discount = 0;
                            if ( isset($item['discount']['original']) && isset($item['discount']['price']) ) {
                                $discount = $item['discount']['original'] - $item['discount']['price'];
                            }
                            $orderDetail[] = [
                                'order_id' => $res['id'],
                                'product_id' => $item['id'],
                                'title' => $item['title'],
                                'sku_code' => $item['sku'] ?? '',
                                'size' => $item['size'] . ' ' . strtoupper($item['unit']),
                                'price' => $item['price'],
                                'discount' => $discount,
                                'quantity' => $item['cart_quantity'],
                                'short_description' => $item['description'],
                                'extra_info'=>''
                            ];                            
                        }
                        foreach ($shoppingPack as $item) {
                            $orderDetail[] = [
                                'order_id' => $res['id'],
                                'product_id' => 0,
                                'title' => $item['title'],
                                'sku_code' => $item['pack'] ?? '',
                                'size' => 'N/A',
                                'price' => $item['price'],
                                'discount' => 0,
                                'quantity' => $item['cart_quantity'],
                                'short_description' => $item['content'],
                                'extra_info' => json_encode($item['products'])
                            ];                            
                            $order_detail = $orderDetailTable->newEntity();
                            $orderDetailTable->save($order_detail);
                        }
                        $orderDetailTable = TableRegistry::get('SubscriptionApi.OrderDetails');
                        $orderDetailTable->saveMany($orderDetailTable->newEntities($orderDetail));
                        $data['orderNumber'] = $res['id'];
                        $data['orderPrefix'] = PC['ORDER_PREFIX'];
                        $pbToken = $this->Store->createOrderToken($res['id'], $userId);
                        $data['paymentGatewayUrl'] = Router::url([
                            'plugin' =>'SubscriptionApi',
                            'controller' =>'Stores',
                            'action' => 'paymentRequest',
                            '?'=>['order-id'=>$res['id'],'customer-id'=>$userId,'pb-token'=>$pbToken]
                        ], true);
                        $message = "Order successfully placed.";
                    } else {
                        $message = "Unable to place order. Please try again later.";
                    }
                }catch(\Exception $e){
                    $message = "Sorry to place order, there are some technical issue.";
                }
            }
        } else {
            $message = "Sorry, Invalid request!";
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** SubscriptionApi Plugin, This is for update order status *****/
    public function getOrderStatus()
    {
        $data = [];
        $message = '';
        $status = 0;
        $customerId = $this->request->getData('userId');
        $orderId = $this->request->getData('orderNumber');
        $auth = $this->Customer->isAuth($customerId);
        $data['redirectUrl'] = Router::url([
            'plugin' =>'SubscriptionApi',
            'controller' =>'Stores',
            'action' => 'paymentRequest',
            '?'=>['order-id'=>$orderId,'customer-id'=>$customerId,'token'=>[$this->Store->createOrderToken($orderId, $customerId)]]
        ], true);
        $data['orderStatus'] = 'pending';
        if ($this->request->is(['post']) && $auth['status'] && ($orderId > 0)) {
            try {
                $orderTable = TableRegistry::get('SubscriptionApi.Orders');
                $order = $orderTable->get($orderId);
                $data['orderStatus'] = $order->status;         
                switch ($data['orderStatus']) {
                    case 'accepted':$status = 1;
                        $message = 'Order accepted';
                        break;
                    case 'paymentfail': $status = 2;
                        $message = 'Order accepted but payment status fail!';
                        break;
                    default:
                        $message = 'Pending Order!';
                }
            } catch ( \Exception $e ){
                $message = "Sorry, Order number not exists!";
            }
        } else {
            $message = "Sorry, invalid request!";
        }
        $data['orderNumber'] = $orderId;
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    public function paymentRequest(){
        $this->response->type('text/html');
        $this->viewBuilder()->setLayout('SubscriptionApi.payment');
        $waitMessage = 'Do not Refresh or Press Back <br/> Redirecting...';
        $orderId     = $this->request->getQuery('order-id','10000008'); //10000006,10000007,10000008
        $customerId  = $this->request->getQuery('customer-id','115760');
        $pbToken     = $this->request->getQuery('pb-token','115760');
        $paymentMethodCode = '';
        $order  = [];
        try {
            $order = TableRegistry::get('SubscriptionApi.Orders')->get($orderId, ['fields'=>['id', 'customer_id', 'payment_method_id', 'product_total', 'ship_amount', 'mode_amount', 'discount', 'payment_amount', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_pincode', 'shipping_email', 'shipping_phone', 'Customers.firstname', 'Customers.lastname', 'Customers.email', 'Customers.mobile', 'Locations.title', 'Locations.currency', 'Locations.code', 'Locations.code2', 'Locations.locale', 'PaymentMethods.code'],'contain'=>['OrderDetails','Customers', 'Locations', 'PaymentMethods']])->toArray();
            $paymentMethodCode = $order['payment_method']['code'] ?? '';
        } catch (\Exception $e){
        }
        //pr($order); die;
        if( !empty($order) &&  ($order['customer_id'] == $customerId) || ($pbToken == $this->Store->createOrderToken($orderId, $customerId)) ){
            $returnUrl = Router::url([
                'plugin' =>'SubscriptionApi',
                'controller' =>'Stores',
                'action' => 'paymentResponse',
                '?'=>['order-id'=>$order['id'], 'customer-id'=>$customerId,'pb-token'=>$pbToken]
            ], true);
            $cancelUrl = Router::url([
                'plugin' =>'SubscriptionApi',
                'controller' =>'Stores',
                'action' => 'paymentCancel'                
            ], true);
            $order['returnUrl'] = $returnUrl;
            $order['cancelUrl'] = $cancelUrl;
            //$paymentMethodCode = 'razorpay';
            switch( $paymentMethodCode ){
                case 'cod': 
                    $this->redirect($returnUrl);
                    break;
                case 'paypal': //for Paypal GateWay
                    $res = $this->Paypal->paymentRequest($order);
                    //pr($order); die;
                    if( empty($res['url']) ){
                        $waitMessage = $res['message'];
                    }else{
                        $this->redirect($res['url']);
                    }
                    break;
                case 'paytm': // For Paytm
                    $addr1 = str_replace([':', '.', '"'], " ", $order['shipping_address']);
                    $checkSum = "";
                    $paramList = [];
                    $paramList["MID"] 					= $this->Paytm->PAYTM_MERCHANT_MID;
                    $paramList["ORDER_ID"] 				= $orderId;
                    $paramList["CUST_ID"] 				= 'CUST-'.$customerId;
                    $paramList["INDUSTRY_TYPE_ID"] 		= $this->Paytm->INDUSTRY_TYPE_ID;
                    $paramList["CHANNEL_ID"] 			= $this->Paytm->CHANNEL_ID;
                    $paramList["TXN_AMOUNT"] 			= $order['payment_amount'];
                    $paramList["WEBSITE"] 				= $this->Paytm->PAYTM_MERCHANT_WEBSITE;
                    $paramList["CALLBACK_URL"] 			= $returnUrl;
                    $paramList["EMAIL"] 				= $order['shipping_email'];
                    $paramList["MOBILE_NO"] 			= $order['shipping_phone'];
                    $paramList["PAYMENT_DETAILS"] 		= 'Pay by paytm for '.PC['COMPANY']['tag'];
                    $paramList["ORDER_DETAILS"] 		= 'Shopping at '.PC['COMPANY']['tag'];
                    $paramList["ADDRESS_1"] 			= $addr1;
                    //$paramList["ADDRESS_2"] 			= $order['shipping_address'];
                    $paramList["CITY"] 					= $order['shipping_city'];
                    $paramList["STATE"] 				= $order['shipping_state'];
                    $paramList["PINCODE"] 				= $order['shipping_pincode'];            
                    //Here checksum string will return by getChecksumFromArray() function.
                    $checkSum = $this->Paytm->getChecksumFromArray($paramList, $this->Paytm->PAYTM_MERCHANT_KEY);
                    
                    $waitMessage = 'Do not Refresh or Press Back <br/> Redirecting to PayTM';
                    $this->set('checkSum', $checkSum);
                    $this->set('paramList', $paramList); //pr($paramList);
                    $this->set('txnPostUrl', $this->Paytm->PAYTM_TXN_URL);
                    break;
                case 'mobikwik':
                    $_POST['orderId'] = $orderId;
                    $_POST['amount'] = $order['payment_amount'] * 100; //amount should be in paise
                    $_POST['buyerEmail'] = $order['shipping_email'];
                    $_POST['shipToFirstname'] = $order['shipping_firstname'];
                    $_POST['shipToLastname'] = $order['shipping_lastname'];
                    $_POST['shipToAddress'] = $order['shipping_address'];
                    $_POST['shipToCity'] = $order['shipping_city'];
                    $_POST['shipToState'] = $order['shipping_state'];
                    $_POST['shipToCountry'] = $order['shipping_country'];
                    $_POST['shipToPincode'] = $order['shipping_pincode'];
                    $_POST['shipToPhoneNumber'] = $order['shipping_phone'];   
                    $_POST['merchantIdentifier'] = $this->Mobikwik->merchantId;
                    $_POST['mode'] = $this->Mobikwik->mode;
                    $_POST['showMobile'] = $this->Mobikwik->showMobile;
                    $_POST['currency'] = $this->Mobikwik->currency;
                    $_POST['returnUrl'] = $returnUrl;
                    $secret = $this->Mobikwik->secretKey;
                    //print_r($_POST);
                    $all = $this->Mobikwik->getAllParams();
                    $checksum = $this->Mobikwik->calculateChecksum($secret, $all);
                    $waitMessage = 'Do not Refresh or Press Back <br/> Redirecting to Mobikwik';
                    $this->set('txnPostUrl', $this->Mobikwik->txnPostUrl);
                    $this->set('outputForm', $this->Mobikwik->outputForm($checksum));
                    break;
                case 'razorpay': 
                    $res = $this->Razorpay->paymentRequest($order);
                    $order['razorpay'] = $res['razorpay'];
                    $waitMessage = $res['message'];
                    break;
                default:    
            }
        }else{
            $waitMessage = 'Sorry, this is invalid or tampered request!';
        }
        $this->set('title', 'Payment gateway request');
		$this->set(compact('order', 'waitMessage','paymentMethodCode'));
		$this->set('_serialize', ['order', 'waitMessage','paymentMethodCode']);
    }

    public function paymentResponse(){
        //$this->response->type('text/html');
        //Get payment object by passing paymentId
        $paymentStatus = 0;
        $orderId    = $this->request->getQuery('order-id');
        $customerId = $this->request->getQuery('customer-id');
        $orderTable = TableRegistry::get('SubscriptionApi.Orders');
        $paymentMethodCode = '';
        try{
            $order      = $orderTable->get($orderId,['contain'=>['OrderDetails', 'Customers', 'Locations', 'PaymentMethods']]);
            $paymentMethodCode = $order['payment_method']['code'] ?? '';
        }catch(\Exception $e){}

        switch($paymentMethodCode){
            case 'cod': // For COD Payment Option
                $paymentStatus = 1;
                break;
            case 'paypal': //For PayPay Gateway
                $pbToken    = $this->request->getQuery('pb-token');
                $paymentId  = $this->request->getQuery('paymentId');
                $payerId    = $this->request->getQuery('PayerID');
                $token      = $this->Store->createOrderToken($orderId, $customerId);
                $param      = ['currency'=>$order->location->currency, 'amount'=>$order->payment_amount, 'payerId'=>$payerId, 'paymentId'=>$paymentId];
                $res        = $this->Paypal->paymentResponse($param);
                $this->Store->savePaymentResponse($orderId, 'PayPal', $res['result']);
                //pr($res); die;
                $paymentStatus = $res['status'];
                break;
            case 'paytm': // For Paytm
                $isValidChecksum = "FALSE";                    
                $paramList = $_POST;
                $paytmChecksum = $_POST["CHECKSUMHASH"] ?? ""; //Sent by Paytm pg                    
                //Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application�s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
                $isValidChecksum = $this->Paytm->verifychecksum_e($paramList, $this->Paytm->PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.
                $this->Store->savePaymentResponse($orderId, 'paytm', $paramList);
                $orderId  = $_POST["ORDERID"] ?? 0;
                $status   = $_POST["STATUS"] ?? NULL;
                switch($status){
                    case 'TXN_SUCCESS' : 
                        if ( $isValidChecksum == "TRUE" ) {
                            $paymentStatus = 1; 
                        } else {
                            $paymentStatus = 3;
                        }
                        break;
                    case 'TXN_FAILURE' : 
                        $paymentStatus = 3; 
                        break;
                    default : $paymentStatus = 5;
                }
                break;
            case 'mobikwik': 
                $secret         = $this->Mobikwik->secretKey;
                $orderId 		= $this->request->getData('orderId');
                $responseCode 	= $this->request->getData('responseCode');
                //$responseCode 	= ($responseCode == 601) ? 100 : $responseCode; 
                $recd_checksum 	= $this->request->getData('checksum');
                $all            = $this->Mobikwik->getAllResponseParams();
                $checksum_check = $this->Mobikwik->verifyChecksum($recd_checksum, $all, $secret);
                $this->Store->savePaymentResponse($orderId, 'mobikwik', $_POST);
                if( $checksum_check && ($responseCode == 100) ){
                    $paymentStatus = 1;
                }
                //$outputResponse = $this->Mobikwik->outputResponse($checksum_check);
                break;       
            case 'razorpay':
                $param  = [
                    'payment_id' => $this->request->getData('razorpay_payment_id'), 
                    'order_id' => $this->request->getData('razorpay_order_id'), 
                    'signature' => $this->request->getData('razorpay_signature')
                ];
                $res   = $this->Razorpay->paymentResponse($param);
                $this->Store->savePaymentResponse($orderId, 'RazorPay', $res['result']);
                $paymentStatus = $res['status'];
                break;
            default:
        }
        //$paymentStatus = 1;
        //Payment are successfull done
        switch ( $paymentStatus ) {
            case 1: //order successfull and payment capture
                if( $order->is_processed == '0' ) {
                    $order->status = 'accepted';
                    $order->is_processed = 1;
                    $orderTable->save($order);
                    $this->Store->updateWalletAfterPayment($orderId);
                    $this->Store->createInvoice($orderId);
                    $this->Shipvendor->pushOrder($orderId);
                    if (!empty($order->coupon_code)) {
                        $this->Coupon->inOrderStatus($order->coupon_code, $order->customer->email);
                    }
                    $log = json_decode($order->order_log, true);
                    $prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
                    if ($prive && ($order->payment_mode != 'postpaid')) {
                        $memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
                        $this->Membership->add($memberData);
                    }
                    $this->Store->orderStatusEmails($orderId, 'confirmed');
                }
                break;
            case 2: //Payment not capture
                $order->status = 'not_capture';
                $orderTable->save($order);
            default:
        }
        return $this->redirect(PC['COMPANY']['website'].'/checkout/onepage/confirmation');
    }

    public function paymentCancel(){
        return $this->redirect(PC['COMPANY']['website'].'/checkout/onepage/confirmation');
    }

}
