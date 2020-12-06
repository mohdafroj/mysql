<?php
namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\Routing\Router;
use Twocheckout;
use Twocheckout_Charge;

class TwocheckoutComponent extends Component
{
    private $username = 'rohit@perfumebooth.com';
    private $password = 'Perfume@987';
    private $sellerId  = '250098578136';
    private $privateKey = '8DC75A4D-10CD-4507-BB43-7AFEBBE56EDF';
    private $secretKey = 'Fp8ty(Gg0LNU?A~!ws%[';
    private $sandbox = true;
    public function __construct(){
        // Your sellerId(account number) and privateKey are required to make the Payment API Authorization call.
        Twocheckout::privateKey($this->privateKey);
        Twocheckout::sellerId($this->sellerId);

        // Your username and password are required to make any Admin API call.
        Twocheckout::username($this->username);
        Twocheckout::password($this->password);

        // If you want to turn off SSL verification (Please don't do this in your production environment)
        if( $this->sandbox ){
            Twocheckout::verifySSL(false);  // this is set to true by default
        }

        // To use your sandbox account set sandbox to true
        Twocheckout::sandbox($this->sandbox);

        // All methods return an Array by default or you can set the format to 'json' to get a JSON response.
        Twocheckout::format('json');
    }
    public function createTokenForm(){
        $formData = '';

        return $formData;
    }
    public function paymentRequest($order) {
        $data = ['url'=>'','message'=>''];
        // $order is for order details array
        try {
            $currency = $order['location']['currency'] ?? 'INR';
            $currency = strtoupper($currency);
            $parametersData = [
                "sellerId" => $this->sellerId,
                "merchantOrderId" => $order['id'],
                "token" => 'MjFiYzIzYjAtYjE4YS00ZmI0LTg4YzYtNDIzMTBlMjc0MDlk',
                "currency" => $currency,
                "total" => $order['payment_amount'],
                "billingAddr" => array(
                    "name" => $order['shipping_firstname'] . ' ' . $order['shipping_lastname'],
                    "addrLine1" => $order['shipping_address'],
                    "city" => $order['shipping_city'],
                    "state" => 'OH',
                    "zipCode" => $order['shipping_pincode'],
                    "country" => $order['shipping_country'],
                    "email" => $order['shipping_email'],
                    "phoneNumber" => $order['shipping_phone']
                ),
                "shippingAddr" => array(
                    "name" => $order['shipping_firstname'] . ' ' . $order['shipping_lastname'],
                    "addrLine1" => $order['shipping_address'],
                    "city" => $order['shipping_city'],
                    "state" => 'OH',
                    "zipCode" => $order['shipping_pincode'],
                    "country" => $order['shipping_country'],
                    "email" => $order['shipping_email'],
                    "phoneNumber" => $order['shipping_phone']
                )
            ];
            $charge = Twocheckout_Charge::auth($parametersData);

        }catch (\Twocheckout_Error $ex) {
            $data['message'] = $ex->getMessage();
        }
        return $data;
    }

    public function paymentResponse($param){
        $data = ['message'=>'','result'=>'','status'=>0];
        try {
            $amount = $param['amount'] ?? 0;
            $key = $param['key'] ?? '';
            $orderNumber = $param['orderNumber'] ?? 0;
            if ( strtoupper(md5( $this->secretKey . $this->sellerId . $orderNumber . $amount)) == $key ) {
                $data['status'] = 1;
            }
        } catch (\Exception $ex) {
            $data['message'] = $ex;
        }
        return $data;
    }
}
