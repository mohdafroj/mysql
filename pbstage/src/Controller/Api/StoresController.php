<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Cake\Mailer\MailerAwareTrait;
use Cake\Core\Configure;

class StoresController extends AppController
{
    use MailerAwareTrait;
    public function initialize()
    {
        parent::initialize();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $this->loadComponent('CommonLogic');
        $this->loadComponent('Sms');
        $this->loadComponent('Store');
        $this->loadComponent('Coupon');
        $this->loadComponent('Delhivery');
        $this->loadComponent('Customer');
        $this->loadComponent('Membership');
        $this->loadComponent('Shipvendor');
        $this->loadComponent('Shiproket');
    }

    public function index()
    {
        header('Content-Type: text/html');
        $response = '';
        $pincode  = $this->request->getQuery('param1',0);
        $shipmentIds = [13077600,13077063,13074380,13074332,13073794,13075365,13071355,13071389,13070358,13069644,13066942,13066341,13066250,13071616,13064542,13064213,13063811,13062858,13061618,13059956,13071552,13058207,13058045,13057952,13057885,13057346,13056668,13056075,13055675,13055321,13055320,13055228,13054520,13054283,13054056,13053989,13053881,13053848,13053154,13052701,13051959,13051550,13051186,13071979];
        //$response = $this->Shiproket->sendOrderByAdmin(100066425, 2);
        //$response = $this->Delhivery->getPincode(110024);
        //$response = $this->Store->getDefaultPaymentGateway();
        $redirectUrl = str_replace('pb/','new/checkout/onepage/success', Router::url('/', true));
		$response = $this->Store->getCart($pincode);
        pr($response);
        die;
    }

    public function autoMem()
    {
        $customerTable = TableRegistry::get('Customers');
        $customer = $customerTable->find('all', ['fields' => ['Customers.id'], 'limit' => 10000])
            ->join([
                'm' => [
                    'table' => 'memberships',
                    'type' => 'INNER',
                    'conditions' => 'm.customer_id != Customers.id',
                ],
            ])
            ->toArray();
        $total = count($customer); //pr($customer);
        if ($total > 0) {
            foreach ($customer as $va) {
                $memberData = $this->Membership->getPlanData($va->id, 0);
                $this->Membership->add($memberData);
            }
        } else {
            'Added to all customers!';
        }
        die;
    }

    public function activeMember($customerId = 0, $orderId = 0)
    {
        if (($customerId > 0) && ($orderId > 0)) {
            $memberData = $this->Membership->getPlanData($customerId, $orderId);
            $this->Membership->add($memberData);
        } else {

        }
        die;
    }

    public function getPincode()
    {
        $data = [];
        $message = '';
        $status = 0;
        $pincode = $this->request->getQuery('pincode');
        if ($this->request->is(['get']) && is_numeric($pincode) && (strlen($pincode) == 6)) {
            $response = $this->Shipvendor->checkPincode($pincode);
        } else {
            $message = 'Sorry, invalid pincode request!';
            $response = ['message' => $message, 'status' => $status, 'data' => $data];
        }
        echo json_encode($response);
        die;
    }

    public function getOtp()
    {
        $data = [];
        $message = '';
        $status = 0;
        try {
            $userId = $this->request->getData('userId');
            $auth = $this->Customer->isAuth($userId);
            if ($auth['status']) {
                if ($this->request->is(['post'])) {
                    $mobile = $this->request->getData('mobile');
                    $amount = $this->request->getData('amount');
                    $name = $this->request->getData('name');
                    $email = $this->request->getData('email');
                    $otp = $this->Sms->generateOtp(); //param are length, default is 6 digit code
                    $info = $this->Sms->otpSend($mobile, $otp, $amount);
                    //if ($info['status'] == 'OK') {
                        $customerTable = TableRegistry::get('Customers');
                        $customer = $customerTable->get($userId);
                        $customer->password = $otp;
                        $customerTable->save($customer);
                        $status = 1;
                        $message = 'We have sent OTP on entered mobile number and registered email id, if not received within 3 minutes please click on resend otp!';
                        $otpMailer = [
                            'customerId =' > $userId,
                            'otp' => $otp,
                            'name' => $name,
                            'email' => $email,
                            'amount' => $amount,
                            'mobile' => $mobile,
                        ];
                        if ($this->Store->isPerfumeBooth()) {
                            $this->getMailer('Customer')->send('otpSend', [$otpMailer]);
                        }
                    //} else {
                        //$message = $info['message'];
                    //}
                } else if ($this->request->is(['put'])) {
                    $otp = $this->request->getData('otp');
                    $customerTable = TableRegistry::get('Customers');
                    $customer = $customerTable->get($userId);
                    if (md5($otp) == $customer->password) {
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
        } catch (\Exception $e) {
            $message = 'Sorry, exceptional request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    //###########Please write code for all customer api after this ///////////////############################################
    public function customerCart()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0;
        if ($auth['status']) {
            if ($this->request->is(['get'])) {
                $inputData = [
                    'customer_id' => $userId,
                    'payment_method' => $this->request->getData('paymentMethod', '2'),
                    'pincode' => $this->request->getData('pincode', ''),
                    'coupon_code' => $this->request->getData('couponCode', ''),
                    'gift_voucher_status' => $this->request->getData('giftVoucherStatus', '1'),
                    'pb_points_status' => $this->request->getData('pbPointsStatus', '0'),
                    'pb_cash_status' => $this->request->getData('pbCashStatus', '1'),
                    'pb_prive' => $this->request->getData('pbPrive', '0'),
                ];
                //$data                = $this->Store->getActiveCartDetails($inputData);
                $status = true;
                //$page = $this->request->getData('trackPage', NULL);
                //$this->Customer->trackPage($userId, $page);
            } else if ($this->request->is(['post'])) {
                $itemId = $this->request->getData('itemId');
                $qty = $this->request->getData('qty');
                $info = $this->Store->addItemIntoCart($userId, $itemId, $qty);
                $message = $info['message'];
                $status = $info['status'];
                $cart = $this->Store->getActiveCart($userId);
                $data['cart'] = $cart['cart'] ?? [];
                $this->response->statusCode(201);
                $this->response->send();
            } else if ($this->request->is(['delete'])) {
                $id = $this->request->getQuery('id');
                $status = $this->Store->revomeItemFromCart($id);
                $message = ($status) ? 'Item removed from cart!' : 'Sorry, Item not remove from cart!';
                $cart = $this->Store->getActiveCart($userId);
                $data['cart'] = $cart['cart'] ?? [];
            } else if ($this->request->is(['put'])) {
                $id = $this->request->getData('id');
                $qty = $this->request->getData('qty');
                if (is_numeric($qty) && ($qty > 0)) {
                    $status = $this->Store->updateItemIntoCart($id, $qty);
                    $message = ($status) ? 'Item updated into cart!' : 'Sorry, Item not updated into cart!';
                    $cart = $this->Store->getActiveCart($userId);
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

    public function getActiveCart()
    {
        $userId = $this->request->getQuery('userId', '0'); //16597
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($auth['status']) {
            $inputData = [
                'customer_id' => $userId,
                'payment_method' => $this->request->getData('paymentMethod', '0'),
                'pincode' => $this->request->getData('pincode', ''),
                'coupon_code' => $this->request->getData('couponCode', ''),
                'gift_voucher_status' => $this->request->getData('giftVoucherStatus', '1'),
                'pb_points_status' => $this->request->getData('pbPointsStatus', '0'),
                'pb_cash_status' => $this->request->getData('pbCashStatus', '1'),
                'pb_prive' => $this->request->getData('pbPrive', '0'),
            ];
            $data = $this->Store->getActiveCartDetails($inputData);
            $status = true;
            $page = $this->request->getData('trackPage', null);
            $this->Customer->trackPage($userId, $page);
            //unset($data['cart'],$data['payment_method_data']);
            //echo json_encode($data);
            //pr($data);
            //die;
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        //pr($response);
        echo json_encode($response);
        die;
    }

    public function removeProductFromCart()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['delete']) && $auth['status']) {
            $cartId = $this->request->getQuery('cartId');
            $status = $this->Store->revomeItemFromCart($cartId);
            $message = ($status) ? 'Item removed from cart!' : 'Sorry, Item not remove from cart!';
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    public function updateProductQuantity()
    {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['put']) && $auth['status']) {
            $cartId = $this->request->getData('cartId');
            $quantity = $this->request->getData('qty');
            if ($cartId > 0 && $quantity > 0) {
                $status = $this->Store->updateItemIntoCart($cartId, $quantity);
                $message = ($status) ? 'Item updated into cart!' : 'Sorry, Item not updated into cart!';
            } else {
                $message = 'Sorry, Qunatity should be greater than zero!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    public function changeUserAccount()
    {
        $userId = $this->request->getQuery('userId');
        $username = $this->request->getQuery('username');
        $password = $this->request->getQuery('password');

        $data = [];
        $message = '';
        $status = false;
        if (($this->request->is(['put']) || $this->request->is(['post'])) && $userId > 0 && $username != '' && $password != '') {
            $data = $this->Customer->validateLogin($username, $password);
            if (count($data) > 0) {
                $updateCart = $this->Store->updateCartAccount($userId, $data['id']);
                if ($updateCart) {
                    $status = true;
                } else {
                    $message = 'Sorry, Unable to change account. Please try again later!';
                }
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
            $shipping_country = $this->request->getData('shipping_country', 'India');
            $shipping_email = $this->request->getData('shipping_email');
            $shipping_mobile = $this->request->getData('shipping_mobile');
            $shipping_pincode = $this->request->getData('shipping_pincode');

            $pbPrive = $this->request->getData('pbPrive', '0');
            $couponCode = $this->request->getData('couponCode');
            $paymentMethod = $this->request->getData('paymentMethod');
            $giftVoucherStatus = $this->request->getData('giftVoucherStatus');
            $pbCashStatus = $this->request->getData('pbCashStatus');
            $pbPointsStatus = $this->request->getData('pbPointsStatus');
            $paymentMethodData = $this->Store->getActivePaymentGatewayData();
            
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
            } else if ($shipping_mobile == '') {
                $is_error = 1;
                $message = "Please enter your shipping mobile.";
            } else if ( !in_array($paymentMethod, array_column($paymentMethodData, 'id')) ) {
                $is_error = 1;
                $message = "Please select a payment method.";
            }

            if ($is_error == 0) {
                $customerTable = TableRegistry::get('Customers');
                $getCustomerData = $customerTable->get($userId);
                $inputData = [
                    'customer_id' => $userId,
                    'payment_method' => $paymentMethod,
                    'pincode' => $shipping_pincode,
                    'coupon_code' => $couponCode,
                    'gift_voucher_status' => $giftVoucherStatus,
                    'pb_points_status' => $pbPointsStatus,
                    'pb_cash_status' => $pbCashStatus,
                    'pb_prive' => $pbPrive,
                ];

                $cartAmountDetails = $this->Store->getActiveCartDetails($inputData);
                $orderLog = $cartAmountDetails;
                unset($orderLog['cart'], $orderLog['payment_method_data']);
                $orderLog = json_encode($orderLog);
                
                $pgId = $cartAmountDetails['payment_method'];
                $orderTable = TableRegistry::get('Orders');
                $order = $orderTable->newEntity();
                $order->customer_id = $userId;
                $order->payment_method_id = $pgId;
                $order->payment_mode = ($pgId == 1) ? 'postpaid' : 'prepaid';
                $order->product_total = $cartAmountDetails['total_amount_after_discount'];
                $order->payment_amount = $cartAmountDetails['grand_final_total'];
                $order->discount = $cartAmountDetails['discounts']['amount'];
                $order->ship_amount = $cartAmountDetails['shipping_amount'];
                $order->ship_discount = $cartAmountDetails['discounts']['extra'];
                $order->mode_amount = $cartAmountDetails['payment_fees'];
                $order->coupon_code = $couponCode;
                $order->mobile = $getCustomerData->mobile;
                $order->email = $getCustomerData->email;
                $order->credit_mailer = 0;
                $order->status = 'pending';
				$order->zone = $this->Store->getZoneIdByPincode($shipping_pincode);
                $order->shipping_firstname = $shipping_firstname;
                $order->shipping_lastname = $shipping_lastname;
                $order->shipping_address = $shipping_address;
                $order->shipping_city = $shipping_city;
                $order->shipping_state = $shipping_state;
                $order->shipping_country = $shipping_country;
                $order->shipping_pincode = $shipping_pincode;
                $order->shipping_email = $shipping_email;
                $order->shipping_phone = $shipping_mobile;
                $order->billing_firstname = '';
                $order->billing_lastname = '';
                $order->billing_address = '';
                $order->billing_city = '';
                $order->billing_state = '';
                $order->billing_country = '';
                $order->billing_pincode = '';
                $order->billing_email = '';
                $order->billing_phone = '';
                $order->gift_voucher_amount = $cartAmountDetails['discounts']['voucher'];
                $order->pb_points_amount = $cartAmountDetails['discounts']['points'];
                $order->pb_cash_amount = $cartAmountDetails['discounts']['cash'];
                $order->credit_gift_amount = $cartAmountDetails['credits']['voucher'];
                $order->credit_points_amount = $cartAmountDetails['credits']['points'];
                $order->credit_cash_amount = $cartAmountDetails['credits']['cash'];
                $order->delhivery_response = '';
                $order->packing_slip = '';
                $order->cancel_response = '';
                $order->order_log = $orderLog;
                $order->transaction_ip = $_SERVER['REMOTE_ADDR'];
                $orderArray = $orderTable->save($order)->toArray();
                $order_id = $orderArray['id'];
                $order->order_number = $order_id;
                $orderTable->save($order);

                $status = true;
                if ($order_id > 0) {
                    if (count($cartAmountDetails['cart']['cart']) > 0) {
                        $orderDetailTable = TableRegistry::get('OrderDetails');
                        foreach ($cartAmountDetails['cart']['cart'] as $cart_item) {
                            $order_detail = $orderDetailTable->newEntity();
                            $order_detail->order_id = $order_id;
                            $order_detail->product_id = $cart_item['id'];
                            $order_detail->title = $cart_item['title'];
                            $order_detail->sku_code = $cart_item['sku_code'];
                            $order_detail->size = $cart_item['size'] . ' ' . strtoupper($cart_item['size_unit']); //size_unit
                            $order_detail->price = $cart_item['price'];
                            $order_detail->discount = 0;
                            $order_detail->tax_amount = 0;
                            $order_detail->qty = $cart_item['cart_qty'];
                            $order_detail->short_description = $cart_item['description'];
                            $orderDetailTable->save($order_detail);
                        }
                    }
                    $data['orderNumber'] = $order_id;
                    $data['shippingState'] = $shipping_state;
                    $data['shippingCountry'] = $shipping_country;
                    
                    $pbToken = $this->Store->createOrderToken($order_id, $userId);
                    $data['paymentGatewayUrl'] = Router::url([
                        'prefix' =>'api',
                        'controller' =>'Stores',
                        'action' => 'paymentRequest',
                        '?'=>['order-id'=>$order_id,'customer-id'=>$userId,'pb-token'=>$pbToken]
                    ], true);
                } else {
                    $message = "Unable to place order. Please try again later.";
                }
            }

        } else {
            $message = "Sorry, Invalid request!";
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    public function updateOrderDetailsAfterPG() //this is new update code for order status
    {
        $data = [];
        $message = '';
        $status = false;

        $userId = $this->request->getData('userId');
        $orderNumber = $this->request->getData('orderNumber');
        $pgStatus = $this->request->getData('pgStatus');
        $pgName = $this->request->getData('pgName');
        $pgData = $this->request->getData('pgData');
        $auth = $this->Customer->isAuth($userId);

        $getStatus = "";
        if ($this->request->is(['post']) && $auth['status'] && ($orderNumber > 0)) {
            $pgData = $this->Store->pgResEnDe($pgData, 'd');
            $this->Store->pgResposes($orderNumber, $pgName, $pgData);

            $orderTable = TableRegistry::get('Orders');
            $order = $orderTable->get($orderNumber);
            switch ($pgName) {
                case 'mobikwik':
                    if (in_array($pgStatus, [100, 601])) {
                        $getStatus = 'accepted';
                    } else {
                        $getStatus = 'pending';
                    }
                    break;
                case 'payu':
                    switch ($pgStatus) {
                        case 100:$getStatus = 'accepted';
                            break;
                        case 300:$getStatus = 'paymentfail';
                            break;
                        default:$getStatus = 'pending';
                    }
                    break;
                case 'paytm':
                    switch ($pgStatus) {
                        case 100:$getStatus = 'accepted';
                            break;
                        case 300:$getStatus = 'paymentfail';
                            break;
                        default:$getStatus = 'pending';
                    }
                    break;
                case 'cod':
                    $getStatus = 'accepted';
                    break;
                default:
            }

            if (in_array($getStatus, ['accepted', 'paymentfail', 'pending'])) {
                $oDetails = $this->Customer->getOrdersDetails($userId, $orderNumber);
                //pr($oDetails);
                $order->status = $getStatus;
                if (count($oDetails) == 0) {
                    $message = 'Sorry, customer not belong to this order number!';
                } else if ($orderTable->save($order)) {
                    switch ($getStatus) {
                        case 'accepted':
                            $message = 'Order accepted';
                            if ($order->is_processed == '0') {
                                $order->is_processed = 1;
                                $orderTable->save($order);

                                $this->Store->updateWalletAfterPayment($orderNumber);
                                //$this->Store->updateStockAfterOrderPlaced($oDetails['details']);
                                $this->Store->createInvoice($orderNumber);

                                if (!empty($order->coupon_code)) {
                                    $customerTable = TableRegistry::get('Customers');
                                    $customer = $customerTable->get($order->customer_id);
                                    $this->Coupon->inOrderStatus($order->coupon_code, $customer->email);
                                }
                                $log = json_decode($order->order_log, true);
                                $prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
                                if ($prive && ($order->payment_mode != 'postpaid')) {
                                    $memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
                                    $this->Membership->add($memberData);
                                }
                            }
                            break;
                        case 'paymentfail':
                            $message = 'Order accepted but payment status fail!';
                            break;
                        default:
                            $message = 'Pending Order!';
                    }
                } else {
                    $message = 'Pending Order';
                }
                if (!empty($oDetails) && ($getStatus == 'accepted')) {
                    $text = '';
                    $total = count($oDetails['details']);
                    if (count($oDetails['details']) > 1) {
                        $total = $total - 1;
                        $text = $oDetails['details'][0]['title'] . " + $total";
                    } else {
                        $text = $oDetails['details'][0]['title'];
                    }

                    $this->Sms->orderSend($oDetails['shippingPhone'], $orderNumber, $oDetails['paymentAmount'], $oDetails['paymentMethodName'], $text);
                    $oDetails['customerId'] = $userId;
                    $this->Customer->trackPage($userId);
                    $this->Store->emptyCart($userId);
                    if ($this->Store->isPerfumeBooth()) {
                        $this->getMailer('Customer')->send('orderConfirmed', [$oDetails]);
                    }
                }
            } else {
                $message = 'Invalid status of order!';
            }
            $status = true;
        } else {
            $message = "Sorry, invalid request!";
        }
        $data['orderStatus'] = $message;
        $data['returnStatus'] = $getStatus;
        $data['orderNumber'] = $orderNumber;

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** ApiManager Plugin, This is for update order status *****/
    public function getOrderStatus()
    {
        $message = '';
        $customerId = $this->request->getData('userId');
        $orderId = $this->request->getData('orderNumber');
        $auth = $this->Customer->isAuth($customerId);
        $response['status'] = 'pending';
        $response['redirectUrl'] = Router::url([
            'prefix' =>'api',
            'controller' =>'Stores',
            'action' => 'paymentRequest',
            '?'=>['order-id'=>$orderId,'customer-id'=>$customerId,'token'=>[$this->Store->createOrderToken($orderId, $customerId)]]
        ], true);
        if ( $this->request->is(['post']) && $auth['status'] ) {
            try {
                $orderTable = TableRegistry::get('Orders');
                $order = $orderTable->get($orderId);
                $response['status'] = $order->status;         
                switch ($response['status']) {
                    case 'accepted':
                        $message = 'Order accepted';
                        break;
                    case 'paymentfail':
                        $message = 'Order accepted but payment status fail!';
                        break;
                    default:
                        $message = 'Pending Order!';
                }            
            } catch ( \Exception $e ){
                $message = "Order number not exists!";
            }
        } else {
            $message = "Sorry, invalid request!";
        }
        $response['message'] = $message;
        echo json_encode($response);
        die;
    }

    public function paymentRequest(){
        $this->loadComponent('Mobikwik');
        $this->loadComponent('Paytm');
        $this->loadComponent('Payu');
        $this->loadComponent('Paypal');
        $this->response->type('text/html');
        $this->viewBuilder()->setLayout('pg');
        $order       = [];
        $waitMessage = 'Do not Refresh or Press Back <br/> Redirecting...';
        $orderId     = $this->request->getQuery('order-id'); //100117999
        $customerId  = $this->request->getQuery('customer-id','98954');
        $pbToken     = $this->request->getQuery('pb-token','98954');
        $paymentMethodCode = '';
        try {
            $order = TableRegistry::get('Orders')->get($orderId, ['fields'=>['id', 'customer_id', 'payment_method_id', 'product_total', 'ship_amount', 'mode_amount', 'discount', 'payment_amount', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_pincode', 'shipping_email', 'shipping_phone', 'Customers.firstname', 'Customers.lastname', 'Customers.email', 'Customers.mobile', 'PaymentMethods.code'],'contain'=>['OrderDetails', 'Customers', 'PaymentMethods']])->toArray();
            $paymentMethodCode = $order['payment_method']['code'] ?? '';
            $paymentMethodCode = strtolower($paymentMethodCode);
            if ( !in_array($paymentMethodCode, ['cod', 'paytm', 'paypal', 'payu', 'mobikwik']) ){
                $paymentMethodCode = 'paytm';
            }
        } catch ( \Exception $e ){}
        //pr($order); die;
        if( !empty($order) && ($order['customer_id'] == $customerId) && ($pbToken == $this->Store->createOrderToken($orderId, $customerId)) ){
            $returnUrl = Router::url([
                'prefix' =>'api',
                'controller' =>'Stores',
                'action' => 'paymentResponse',
                '?'=>['order-id'=>$order['id'], 'customer-id'=>$customerId,'pb-token'=>$pbToken]
            ], true);
            $cancelUrl = Router::url([
                'prefix' =>'api',
                'controller' =>'Stores',
                'action' => 'paymentCancel'                
            ], true);

            $this->set('paymentMethodCode', $paymentMethodCode);
            switch ($paymentMethodCode) {
                case 'cod': // For COD
                    $this->redirect($returnUrl);
                    break;
                case 'payu': // For PayU Money
                    $waitMessage = 'Do not Refresh or Press Back <br/> Redirecting to PayuMoney';
                    $paramList = [];
                    $paramList["udf5"] 		= 'PerfumeBoothToPayuMoney';
                    $paramList["surl"] 		= $returnUrl;
                    $paramList["key"] 		= $this->Payu->payu_key;
                    $paramList["salt"] 		= $this->Payu->payu_salt;
                    $paramList["txnid"] 	= $orderId;
                    $paramList["amount"] 	= $order['payment_amount'];
                    $paramList["productinfo"] = 'Shopping at perfumebooth by PayuMoney';
                    $paramList["firstname"] = $order['shipping_firstname'].' '.$order['shipping_lastname'];
                    $paramList["email"] 	= $order['shipping_email'];
                    $paramList["mobile"] 	= $order['shipping_phone'];
                    $paramList["hash"] 		= $this->Payu->createHash($paramList);
                    $this->set('txnPostUrl', $this->Payu->payu_base_url);
                    $this->set('paramList', $paramList);
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
                    $paramList["PAYMENT_DETAILS"] 		= 'Pay by paytm for PerfumeBooth';
                    $paramList["ORDER_DETAILS"] 		= 'Shopping at PerfumeBooth';
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
                case 'paypal': // for Paypal GateWay
                    $order['location'] = [
                        'id' => 1,
                        'title' => 'India',
                        'code' => 'IND',
                        'code2' => 'IN',
                        'currency' => 'INR',
                        'currency_logo' => 'Rs',
                        'locale' => 'en_IN',
                        'is_active' => 'active'
                    ];
                    $order['returnUrl'] = $returnUrl;
                    $order['cancelUrl'] = $cancelUrl;
                    $res = $this->Paypal->paymentRequest($order);
                    //pr($res); die;
                    if( empty($res['url']) ){
                        $waitMessage = $res['message'];
                    }else{
                        $this->redirect($res['url']);
                    }
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
                default:    
            }
        }else{
            $waitMessage = 'Sorry, this is invalid or tampered request!';
        }
		$this->set('paymentMethodCode', $paymentMethodCode);
		$this->set(compact('order', 'waitMessage'));
		$this->set('_serialize', ['order', 'waitMessage']);
    }

    public function paymentResponse(){
        $getRequest = $this->request->input('json_decode');
        file_put_contents('/var/www/html/assets/paymentResponse.txt', PHP_EOL . json_encode($getRequest), FILE_APPEND);
        file_put_contents('/var/www/html/assets/paymentResponse.txt', PHP_EOL . json_encode($_POST), FILE_APPEND);

        $this->loadComponent('Mobikwik');
        $this->loadComponent('Paytm');
        $this->loadComponent('Payu');
        $this->loadComponent('Paypal');
        $paymentStatus = 0;
        $paymentMethodCode = ''; 
        $orderId    = $this->request->getQuery('order-id');
        $customerId = $this->request->getQuery('customer-id');
        try { 
            $orderTable = TableRegistry::get('Orders');
            $order      = $orderTable->get($orderId,['contain'=>['OrderDetails','PaymentMethods']]);
            $paymentMethodCode = $order['payment_method']['code'] ?? ''; // mobikwik/paytm/payu
            $paymentMethodCode = strtolower($paymentMethodCode);
            if ( !in_array($paymentMethodCode, ['cod', 'paytm', 'paypal', 'payu', 'mobikwik']) ){
                $paymentMethodCode = 'paytm';
            }
            //die;
        } catch ( \Exception $e ){}

        switch ( $paymentMethodCode ) {
            case 'cod': // For COD Payment Option
                $paymentStatus = 1;
                break;
            case 'payu': //For PayuMoney
                $paramList = $_POST;
                $orderId  = $paramList['txnid'] ?? 0;
                $status   = $paramList['status'] ?? NULL;
                $verifyHash = $this->Payu->verifyHash($paramList);
                $this->Store->pgResposes($orderId, 'payu', json_encode($paramList));
                switch($status){
                    case 'success': $paymentStatus = 1; break;
                    case 'failure': $paymentStatus = 3; break;
                    default:	
                }
                break;
            case 'paytm': // For Paytm
                $isValidChecksum = "FALSE";                    
                $paramList = $_POST;
                $paytmChecksum = $_POST["CHECKSUMHASH"] ?? ""; //Sent by Paytm pg                    
                //Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application�s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
                $isValidChecksum = $this->Paytm->verifychecksum_e($paramList, $this->Paytm->PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.
                $this->Store->pgResposes($orderId, 'paytm', json_encode($paramList));
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
            case 'paypal': //For PayPal Gateway
                $pbToken    = $this->request->getQuery('pb-token');
                $paymentId  = $this->request->getQuery('paymentId');
                $payerId    = $this->request->getQuery('PayerID');
                $param      = ['currency'=>'INR', 'amount'=>$order->payment_amount, 'payerId'=>$payerId,'paymentId'=>$paymentId];
                $res        = $this->Paypal->paymentResponse($param);
                $this->Store->pgResposes($orderId, 'paypal', $res['result']);
                //pr($res); die;
                $paymentStatus = $res['status'];
                break;
            case 'mobikwik': 
                $secret         = $this->Mobikwik->secretKey;
                $orderId 		= $this->request->getData('orderId');
                $responseCode 	= $this->request->getData('responseCode');
                //$responseCode 	= ($responseCode == 601) ? 100 : $responseCode; 
                $recd_checksum 	= $this->request->getData('checksum');
                $all            = $this->Mobikwik->getAllResponseParams();
                $checksum_check = $this->Mobikwik->verifyChecksum($recd_checksum, $all, $secret);
                $this->Store->pgResposes($orderId, 'mobikwik', json_encode($_POST));
                if( $checksum_check && ($responseCode == 100) ){
                    $paymentStatus = 1;
                }
                //$outputResponse = $this->Mobikwik->outputResponse($checksum_check);
                break;
            default:
        }
        //Payment are successfull done
        switch ( $paymentStatus ) {
            case 1: //order successfull and payment capture
                if( $order->is_processed == '0' ) {
                    $order->status = 'accepted';
                    $order->is_processed = 1;
                    $orderTable->save($order);
                    $this->Store->updateWalletAfterPayment($orderId);
                    $this->Store->createInvoice($orderId);
                    if ( $this->Store->isPerfumeBooth() ){
                        $this->Shipvendor->pushOrder($orderId);
                    }
                    if (!empty($order->coupon_code)) {
                        $customerTable = TableRegistry::get('Customers');
                        $customer = $customerTable->get($order->customer_id);
                        $this->Coupon->inOrderStatus($order->coupon_code, $customer->email);
                    }
                    $log = json_decode($order->order_log, true);
                    $prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
                    if ($prive && ($order->payment_mode != 'postpaid')) {
                        $memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
                        $this->Membership->add($memberData);
                    }
                    $oDetails = $this->Customer->getOrdersDetails($order->customer_id, $orderId);
                    if (!empty($oDetails)) {
                        $this->Store->updateStockAfterOrderPlaced($oDetails['details']);
                        $text = '';
                        $total = count($oDetails['details']);
                        if (count($oDetails['details']) > 1) {
                            $total = $total - 1;
                            $text = $oDetails['details'][0]['title'] . " + $total";
                        } else {
                            $text = $oDetails['details'][0]['title'];
                        }    
                        $oDetails['customerId'] = $order->customer_id;
                        $this->Store->emptyCart($order->customer_id);
                        $this->getMailer('Customer')->send('orderConfirmed', [$oDetails]);
                    }
                }
                break;
            case 2: //Payment not capture
                $order->status = 'not_capture';
                $orderTable->save($order);
                break;
            case 3: //Payment not capture
                $order->status = 'paymentfail';
                $orderTable->save($order);
                break;
            case 5: //Payment not capture
            default:
        }
        if( $this->Store->isPerfumeBooth() ){
            $redirectUrl = str_replace('pb/','checkout/onepage/success', Router::url('/', true));
        }else{
            $redirectUrl = str_replace('pb/','new/checkout/onepage/success', Router::url('/', true));
        }
        return $this->redirect($redirectUrl);
    }

    public function paymentCancel(){
        if( $this->Store->isPerfumeBooth() ){
            $redirectUrl = str_replace('pb/','checkout/onepage/success', Router::url('/', true));
        }else{
            $redirectUrl = str_replace('pb/','new/checkout/onepage/success', Router::url('/', true));
        }
        return $this->redirect($redirectUrl);
    }
    
    public function pushOrderToVendors() { //this is new update code for order status

        $userId = $this->request->getData('userId');
        $orderNumber = $this->request->getData('orderNumber');
				$auth = $this->Customer->isAuth($userId);
				$message = '';
        if ($this->request->is(['post']) && $auth['status'] && ($orderNumber > 0)) {
						$this->Shipvendor->pushOrder($orderNumber);
						$message = 'Order processed';
        }
        echo json_encode(['message' => $message, 'status' => true, 'data' => []]);
        die;
    }

    public function sendPickupRequest()
    {
        $total_package = 0;
        $orderTable = TableRegistry::get('Orders');
        $orderData = $orderTable->find('all', ['fields' => ['id'], 'conditions' => ['status' => 'accepted', 'is_pickup_request_sent' => '0'], 'order' => ['id' => 'DESC']])->toArray();
        $total_package = count($orderData);

        if ($total_package > 0) {
            $result = $this->Delhivery->sendPickupRequest($total_package, date('Ymd'), '23:00:00');
            if (isset($result['pickup_id']) && $result['pickup_id'] != '') {
                foreach ($orderData as $temp_id) {
                    $temp_array = (array) json_decode($temp_id);
                    $order = $orderTable->get($temp_array['id']);
                    $order->is_pickup_request_sent = 1;
                    $order->delhivery_pickup_id = $result['pickup_id'];
                    $orderTable->save($order);
                }
            }
        }
        die;
    }

    public function changeOrderStatusDelhivery()
    {
        $getRequest = $this->request->input('json_decode');
        file_put_contents('/var/www/html/pb/src/Files/log_file/delhivery.txt', PHP_EOL . json_encode($getRequest), FILE_APPEND);
        file_put_contents('/var/www/html/pb/src/Files/log_file/delhivery.txt', PHP_EOL . json_encode($_POST), FILE_APPEND);
        if (isset($getRequest->Shipment->AWB)) {
            $waybill = $getRequest->Shipment->AWB;
            $statusType = $getRequest->Shipment->Status->StatusType;
            $status = $getRequest->Shipment->Status->Status;
            if ($waybill != '') {
                $orderTable = TableRegistry::get('Orders');
                $orderData = $orderTable->find('all', ['fields' => ['id'], 'conditions' => ['tracking_code' => $waybill], 'order' => ['id' => 'DESC']])->toArray();
                $total_package = count($orderData);

                if ($total_package > 0) {
                    foreach ($orderData as $temp_id) {
                        $newStatus = '';
                        $temp_array = (array) json_decode($temp_id);
                        $order = $orderTable->get($temp_array['id']);
                        if ($status == 'Delivered') {
                            $newStatus = 'delivered';
                            $order->status = 'delivered';

                            $orderTable->save($order);

                            $log = json_decode($order->order_log, true);
                            $voucher = isset($log['credits']['voucher']) ? $log['credits']['voucher'] : 0;
                            if ($voucher && ($voucher % VOUCHER_501)) {
                                $customerTable = TableRegistry::get('Customers');
                                $customer = $customerTable->get($order->customer_id);
                                $customer->scentshot_active = 1;
                                $customerTable->save($customer);
                            }
                            //for membership
                            $prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
                            if ($prive && ($order->payment_mode == 'postpaid')) {
                                $memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
                                $this->Membership->add($memberData);
                            }

                            $this->Store->updateWalletAfterDelivery($order->id);
                            $this->Store->orderDelivered($order->id);
                        }

                        if ($status == 'In Transit') {
                            $currentOrderStatus = $order->status;
                            $newStatus = 'intransit';
                            $order->status = 'intransit';
                            $orderTable->save($order);
                            if (($currentOrderStatus != 'intransit') && ($statusType != 'RT')) {
                                $this->Store->orderIntransit($order->id);
                            }
                        }

                        if ($status == 'Manifested') {
                            // $newStatus        = 'proccessing';
                            // $order->status    = 'proccessing';
                            // $orderTable->save($order);
                        }

                        if ($status == 'Dispatched') {
                            $newStatus = 'dispatched';
                            $order->status = 'dispatched';
                            $orderTable->save($order);
                        }

                        if ($status == 'Cancelled') {
                            $newStatus = 'cancelled';
                            $order->status = 'cancelled';
                            $orderTable->save($order);
                        }

                        if ($status == 'RTO') {
                            $newStatus = 'rto';
                            $order->status = 'rto';
                            $orderTable->save($order);
                        }

                        if ($status == 'DTO') {
                            $newStatus = 'dto';
                            $order->status = 'dto';
                            $orderTable->save($order);
                        }

                        if ($newStatus != '') {
                            $this->Store->changeInvoiceStatus($order->id, $newStatus);
                        }

                        $shippingLogTable = TableRegistry::get('OrderShippingLogs');
                        $shipping_log = $shippingLogTable->newEntity();
                        $shipping_log->order_id = $order->id;
                        $shipping_log->response_data = json_encode($getRequest);
                        $shippingLogTable->save($shipping_log);
                    }
                }
            }
        }
        echo true;
        die;
    }

    public function awb($id_order)
    {
        if ($id_order > 0) {
            $this->Store->sendOrderToDelhivery($id_order);
        }
        die;
    }

    public function getPkg($awb = 0)
    {
        if ($awb > 0) {
            $this->Delhivery->getPackingSlipTest($awb);
        }
        die;
    }

    public function updatePkg($orderId = 0, $awb = 0)
    {
        if (($orderId > 0) && ($awb > 0)) {
            $status = $this->Store->sendOrderToDelhiveryTest($orderId, $awb);
            echo ($status == 1) ? 'Package updated.' : 'Sorry, record not update!';
        }
        die;
    }

    public function pgResponse()
    {
        $status = 0;
        if ($this->request->is('post')) {
            $orderId = $this->request->getData('orderId');
            $pgName = $this->request->getData('pgName');
            $pgData = $this->request->getData('pgResponse');
            if (!empty($orderId) && !empty($pgName)) {
                $status = $this->Store->pgResposes($orderId, $pgName, $pgData);
            }
        }
        echo $status;
        die;
    }

}
