<?php
namespace SubscriptionApi\Controller\Component;
use Cake\Controller\Component;
use Cake\Routing\Router;
use PayPal\Api\Amount;
use PayPal\Api\Authorization;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\ShippingAddress;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

class PaypalComponent extends Component
{
    private $clientId  = 'AYo3XBxKvsnrw4aE-a2g7k90LVDubKamsoagXE6bhZACNfaX5seLjWX0wFwX2GK5AkXI6tNgjeLAedYq';
    private $secretKey = 'ENN9XC3qbUAwtnL3Djku2h9BnffzK9pMR7lqEtVVjRx53ippXufdzVVNdxXhuKHQ6Cx7VMR7DMjj-s01';
    private $liveMode = 1;
    public function __construct(){
        if($this->liveMode){
            $this->clientId  = PC['PAYPAL']['clientId'];
            $this->secretKey = PC['PAYPAL']['secretKey'];
        }
    }

    public function payPalContext() {
        $apiContext = new \PayPal\Rest\ApiContext( new \PayPal\Auth\OAuthTokenCredential($this->clientId, $this->secretKey) );
        if( $this->liveMode ){
            $apiContext->setConfig(['mode'=>'live']);
        }
        return $apiContext;
    }

    public function paymentRequest($order, $param){
        $data = ['url'=>'','message'=>''];
        // $order is for order details array
        if(count($order) && count($param)){
            // Create payer info
            $payerInfo = new PayerInfo();
            $payerInfo ->setFirstName($order['customer']['firstname']);
            $payerInfo ->setLastName($order['customer']['lastname']);
            //$payerInfo ->setPhone("+91".$order['customer']['mobile']); //echo $order['customer']['mobile']; die;           
            $payerInfo ->setEmail($order['customer']['email']);
            // Create new payer and method
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");
            $payer ->setPayerInfo($payerInfo);

            $currency = $order['location']['currency'] ?? 'INR';
            $currency = strtoupper($currency);
            // Set redirect URLs
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(Router::url([
                'plugin' =>'SubscriptionApi',
                'controller' =>'Stores',
                'action' => 'paymentResponse',
                '?'=>['order-id'=>$order['id'], 'customer-id'=>$order['customer_id'],'pb-token'=>$param['token']]
            ], true))
            ->setCancelUrl(Router::url([
                'plugin' =>'SubscriptionApi',
                'controller' =>'Stores',
                'action' => 'paymentCancel'                
            ], true));
    
            //pr($order);
            $shipping_address = new ShippingAddress();
            $shipping_address->setCity($order['shipping_city']);
            $shipping_address->setPostalCode($order['shipping_pincode']);
            $shipping_address->setLine1($order['shipping_address']);
            $shipping_address->setState($order['shipping_state']);
            $shipping_address->setCountryCode(strtoupper($order['location']['code2']));
            //$shipping_address->setCountryCode('IN');
            $shipping_address->setRecipientName($order['shipping_firstname'] . ' ' . $order['shipping_lastname']);
            // Set item list
            $itemsObj = [];
            foreach($order['order_details'] as $value){
                $item1 = new Item();
                $item1->setName($value['title'])->setCurrency($currency)->setQuantity($value['quantity'])->setPrice($value['price']);
                $itemsObj[] = $item1;
            }
            if( $order['discount'] > 0 ){
                $item1 = new Item();
                $item1->setName('Discount')->setCurrency($currency)->setQuantity(1)->setPrice(-$order['discount']);
                $itemsObj[] = $item1;
            }
            $itemList = new ItemList();
            $itemList->setItems($itemsObj);
            $itemList->setShippingAddress($shipping_address);
            $itemList->setShippingPhoneNumber($order['shipping_phone']);
            // Set payment details
            $details = new Details();
            $details->setShipping($order['ship_amount']+$order['mode_amount'])->setTax(0)->setSubtotal($order['product_total']);
    
            // Set payment amount
            $amount = new Amount();
            $amount->setCurrency($currency)->setTotal($order['payment_amount'])->setDetails($details);
            
            // Set transaction object
            $transaction = new Transaction();
            $transaction->setAmount($amount)->setItemList($itemList)->setDescription("Payment Description")->setInvoiceNumber($order['id']);
            
            // Create the full payment object
            $payment = new Payment();
            $payment->setIntent('order')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));
            try{ //var_dump($payment);die;
                $apiContext = $this->payPalContext();
                $payment->create($apiContext);  
                // Get PayPal redirect URL and redirect the customer
                //$queryString = '?country.x='. strtoupper($order['location']['code2']) .'&locale.x=' . $order['location']['locale'];
                $data['url'] = $payment->getApprovalLink();
            }catch(\PayPal\Exception\PayPalConnectionException $ex){
                $data['message'] = $ex->getCode().$ex->getData();
            }catch (\Exception $ex) {
                $data['message'] = $ex;
            }                
        }else{
            $data['message'] = 'Please set required parameters!';
        }
        return $data;
    }

    public function paymentRequestTest($orderId){
        $data = ['url'=>'','message'=>''];
        // Create new payer and method
        $payer = new Payer();
        // $order is for order details array
        if( $orderId > 0 ){
            $payer->setPaymentMethod("paypal");
            $currency = 'USD';
            $currency = strtoupper($currency);
            // Set redirect URLs
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(Router::url([
                'plugin' =>'SubscriptionApi',
                'controller' =>'Stores',
                'action' => 'paymentResponse',
                '?'=>['order-id'=>$orderId, 'customer-id'=>12345,'pb-token'=>'qwertyuiop']
            ], true))
            ->setCancelUrl(Router::url([
                'plugin' =>'SubscriptionApi',
                'controller' =>'Stores',
                'action' => 'paymentCancel'                
            ], true));
    
            //pr($order);
            // Set item list
            $itemsObj = [];
            $item1 = new Item();
            $item1->setName('Product 1')->setCurrency($currency)->setQuantity(1)->setPrice(20);
            $itemsObj[] = $item1;

            $item1 = new Item();
            $item1->setName('Discount')->setCurrency($currency)->setQuantity(1)->setPrice(-4);
            $itemsObj[] = $item1;

            $itemList = new ItemList();
            $itemList->setItems($itemsObj);
    
            // Set payment details
            $details = new Details();
            $details->setShipping(0)->setTax(0)->setSubtotal(16);
    
            // Set payment amount
            $amount = new Amount();
            $amount->setCurrency($currency)->setTotal(16)->setDetails($details);
            
            // Set transaction object
            $transaction = new Transaction();
            $transaction->setAmount($amount)->setItemList($itemList)->setDescription("Payment Description")->setInvoiceNumber($orderId);
            
            // Create the full payment object
            $payment = new Payment();
            $payment->setIntent('order')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));
            try{ //var_dump($payment);die;
                $apiContext = $this->payPalContext();
                $payment->create($apiContext);  
                // Get PayPal redirect URL and redirect the customer
                $data['url'] = $payment->getApprovalLink();
            }catch(PayPal\Exception\PayPalConnectionException $ex){
                $data['message'] = $ex->getCode().$ex->getData();
            }catch (Exception $ex) {
                $data['message'] = $ex;
            }                
        }else{
            $data['message'] = 'Please set required parameters!';
        }
        pr($data); die;
        return $data;
    }

    public function paymentResponse($orderObject, $param){
        $data = ['message'=>'','result'=>'','status'=>0];
        try {
            $pbToken    = $param['pbToken'] ?? null;
            $paymentId = $param['paymentId'] ?? 0;
            $apiContext = $this->payPalContext();
            $payment = Payment::get($paymentId, $apiContext);
            $payerId = $param['payerId'] ?? 0;
            // Execute payment with payer ID
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);
            // Execute payment
            $result = $payment->execute($execution, $apiContext);
            //var_dump($result);die;
            $data['result'] = $result;
            if ($paymentId && $payerId && ($result->id == $paymentId ) && ($result->payer->payer_info->payer_id == $payerId ) && ($pbToken == $param['token']) ) {
                $order = $payment->transactions[0]->related_resources[0]->order;
                // Set payment amount
                $currency = $orderObject->location->currency ?? 'INR';
                $currency = strtoupper($currency);
                $amount = new Amount();
                $amount->setCurrency($currency)->setTotal($orderObject->payment_amount);
                // Set capture details
                $captureDetails = new Authorization();
                $captureDetails->setAmount($amount);
                try {
                    $result = $order->capture($captureDetails, $apiContext);
                    $data['status'] = 1;
                    //print_r($result);
                } catch (\PayPal\Exception\PayPalConnectionException $ex) {
                    $data['message'] = $ex->getCode().$ex->getData();
                    $data['status'] = 2;
                } catch (\Exception $ex) {
                    $data['message'] = $ex->getCode().$ex->getData();
                    $data['status'] = 2;
                }
            }
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            $data['message'] = $ex->getCode().$ex->getData();
        } catch (\Exception $ex) {
            $data['message'] = $ex;
        }
        return $data;
    }
}
