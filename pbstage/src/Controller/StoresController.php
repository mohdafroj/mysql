<?php
namespace App\Controller;

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
        $this->loadComponent('SubscriptionApi.Coupon');
        $this->loadComponent('SubscriptionApi.Customer');
        $this->loadComponent('SubscriptionApi.Membership');
        $this->loadComponent('SubscriptionApi.Shipvendor');
        $this->loadComponent('Paypal');
        $this->loadComponent('Razorpay');
    }

    public function index()
    {
        die;
    }

    public function paymentRequest(){
        $this->response->type('text/html');
        $this->viewBuilder()->setLayout('SubscriptionApi.payment');
        $waitMessage = 'Do not Refresh or Press Back <br/> Redirecting...';
        $orderId     = $this->request->getData('order_id', $this->request->getQuery('order-id','10000004'));
        $customerId  = $this->request->getData('customer_id', $this->request->getQuery('customer-id','98954'));
        $pbToken     = $this->request->getData('customer_auth', $this->request->getQuery('pb-token','98954'));
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
                case 'cod': // For COD Payment Option
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
        $this->set('paymentMethodCode', $paymentMethodCode);
		$this->set(compact('order', 'waitMessage'));
		$this->set('_serialize', ['order', 'waitMessage']);
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
            case 'razorpay':
                $param  = [
                    'payment_id' => $this->request->getData('razorpay_payment_id'), 
                    'order_id' => $this->request->getData('razorpay_order_id'), 
                    'signature' => $this->request->getData('razorpay_signature')
                ];
                $res   = $this->Razorpay->paymentResponse($param);
                $this->Store->savePaymentResponse($orderId, 'RazorPay', serialize($res['result']));
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
                    //$this->Shipvendor->pushOrder($orderId);
                    if (!empty($order->coupon_code)) {
                        $this->Coupon->inOrderStatus($order->coupon_code, $order->customer->email);
                    }
                    $log = json_decode($order->order_log, true);
                    $prive = isset($log['prive']['apply']) ? $log['prive']['apply'] : 0;
                    if ($prive && ($order->payment_mode != 'postpaid')) {
                        $memberData = $this->Membership->getPlanData($order->customer_id, $order->id);
                        $this->Membership->add($memberData);
                    }
                    $oDetails = $this->Customer->getOrdersDetails($order->customer_id, $orderId);
                    if (!empty($oDetails)) {
                        $this->Store->updateStockAfterOrderPlaced($oDetails['order_details']);
                        $text = '';
                        $total = count($oDetails['order_details']);
                        if (count($oDetails['order_details']) > 1) {
                            $total = $total - 1;
                            $text = $oDetails['order_details'][0]['title'] . " + $total";
                        } else {
                            $text = $oDetails['order_details'][0]['title'];
                        }    
                        $oDetails['customerId'] = $order->customer_id;
                        $this->Store->emptyCart($order->customer_id);
                        //$this->getMailer('SubscriptionApi.Customer')->send('orderConfirmed', [$oDetails]);
                    }
                }
                break;
            case 2: //Payment not capture
                $order->status = 'not_capture';
                $orderTable->save($order);
            default:
        }
        $redirectUrl = str_replace('pb/','checkout/onepage/confirmation', Router::url('/', true));
        return $this->redirect($redirectUrl);
    }

    public function paymentCancel(){
        $redirectUrl = str_replace('pb/','checkout/onepage/confirmation', Router::url('/', true));
        return $this->redirect($redirectUrl);
    }
}
