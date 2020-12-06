<?php
namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\Routing\Router;
use Razorpay\Api\Api;

class RazorpayComponent extends Component
{
    private $keyId = 'rzp_test_1YO867E049mBKw';
    private $secretKey = 'q0jN2hjQQ092lPMi71WOwTIE';
    private $live = 1;
    private $api = null;
    public function __construct(){
        // Change for live account
        if( $this->live ) {
            $this->keyId = 'rzp_live_OTH4ywBaNUA8wv';
            $this->secretKey = 'KRKcmLpCi61PGVxM71jLX1Jd';
        }
        $this->api = new Api($this->keyId, $this->secretKey);
    }

    public function paymentRequest($order) {
        $data = ['url'=>'', 'message'=>''];
        // create order at razorpay gateway
        $currency = $order['location']['currency'] ?? 'INR';
        $currency = strtoupper($currency);
        $amount = $order['payment_amount'];
        $amount = 100 * $amount;
        $amount =  (int)$amount; 
        $razorpayOrder = $this->api->order->create(
            [
                'receipt' => $order['id'],
                'amount' => $amount,
                'currency' => $currency,
                'payment_capture' => 1
            ]
        );
        $data['razorpay'] = [
            'order_id' => $razorpayOrder->id ?? '',
            'key_id' => $this->keyId,
            'currency' => $currency,
            'amount' => $amount,
            'name' => $order['customer']['firstname'].' '.$order['customer']['lastname'],
            'email' => $order['customer']['email'],
            'mobile' => $order['customer']['mobile'],
        ];
        return $data;
    }

    public function paymentResponse($param) {
        $data = ['message'=>'', 'result'=>'', 'status'=>0];
        try {
            $response = $this->api->payment->fetch($param['payment_id']); 
            $response->response = ['signature' => $param['signature'] ?? ''];
            $data['result'] = $response;
            $razorpayOrderId = $response->order_id ?? '';
            $status = $response->status ?? '';            
            if( ($status == 'captured') && !empty($param['order_id']) && ( $param['order_id'] == $razorpayOrderId ) ) {
                $data['status'] = 1;
            } else {
                $data['status'] = 2;
            }
        } catch (\Exception $ex) {
            $data['message'] = $ex;
        }
        return $data;
    }

    /// Here are about subscription code
    public function createPlan($param) {
        $response = [ 'data'=>[], 'message'=>'', 'status' => 0 ];
        $planDocs = [
            'period' => $param['period'] ?? 'monthly', // yearly, monthly, weekly, daily
            'interval' => $param['interval'],
            'item' => [
                'id' => $param['id'],
                'name' => $param['name'],
                'description' => $param['description'],
                'amount' => $param['amount'],
                'currency' => $param['currency'],
            ],
            'notes' => []
        ];

        return $response;
    }
}
