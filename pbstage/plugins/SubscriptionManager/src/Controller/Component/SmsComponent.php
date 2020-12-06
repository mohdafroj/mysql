<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;

/**
 * Admin component
 */
class SmsComponent extends Component
{
	// support number - 080 4027 5566
	private $base_url;
	private $key;
	private $sender_id;
	private $orderPrefix = PC['ORDER_PREFIX'];
    public function __construct () {
		$this->base_url = PC['SMS']['base_url'];
		$this->key = PC['SMS']['key'];
        $this->sender_id = PC['SMS']['sender_id'];
	}
	
	public function generateOtp($length=6) {
		$chars = "0123456789";
		return substr(str_shuffle($chars), 0, $length);
    }
	
	public function registerOtp($mobile, $otp){
		$text = $otp.' is the OTP for registration at '.PC['COMPANY']['tag'].' with your mobile number ending '.substr($mobile, -4, 4).'. OTP is valid for 3 minutes only.';
		return $this->send($mobile, $text);
	}
	
	public function loginOtp($mobile, $otp){
		$text = $otp.' is the OTP for account login at '.PC['COMPANY']['tag'].' with your mobile number ending '.substr($mobile, -4, 4).'. OTP is valid for 3 minutes only.';
		return $this->send($mobile, $text);
	}
	
	public function otpSend($mobile, $otp, $amount){
		$text = $otp.' is the OTP for order of Rs. '.$amount.' at '.PC['COMPANY']['tag'].' with your mobile number ending '.substr($mobile, -4, 4).'. OTP is valid for 5 minutes only.';
		return $this->send($mobile, $text);
	}
	
	public function orderSend($mobile, $orderId, $amount, $method, $text='') {
		$orderId = $this->orderPrefix.$orderId;
		$content = 'Your order has been successfully placed with '.PC['COMPANY']['tag'].' with Order ID : '.$orderId.', Product Name: '.$text.' Paid as :  '.$method.'. You will receive the shipment details shortly.';
		return $this->send($mobile, $content);
	}
	
	public function orderDelivered($mobile, $orderId, $date) {
		$orderId = $this->orderPrefix.$orderId;
		$content = 'Delivered: Your package with '.$orderId.' has been delivered on '.$date.'. Thank you for shopping on '.PC['COMPANY']['tag'].'. Please visit again!';
		return $this->send($mobile, $content);
	}
	
	public function orderIntransit($mobile, $orderId, $date){
		$orderId = $this->orderPrefix.$orderId;
		$content = 'Dispatched: Your package with '.$orderId.' has been dispatched on '.$date.'. Thank you for shopping on '.PC['COMPANY']['tag'].'. Please visit again!';
		return $this->send($mobile, $content);
	}
	
	public function orderDispatched($mobile, $orderId, $date){
		$orderId = $this->orderPrefix.$orderId;
		$content = 'Dispatched: Your package with '.$orderId.' has been dispatched on '.$date.'. Thank you for shopping on '.PC['COMPANY']['tag'].'. Please visit again!';
		return $this->send($mobile, $content);
	}
	
	public function orderAccountCredit($mobile, $credited) {
		$more = 0;
		$content = 'Account Credited: Your account has been credited with ';
		if ( $credited['voucher'] > 0 ) {
			$more = 1;
			$content .= 'Rs. '.$credited['voucher'].' voucher';
		}
		if ( $credited['points'] > 0 ) {
			if ( $more ) {
				$content .= ', Points Rs. '.$credited['points'];
			} else {
				$more = 1;
				$content .= 'Points Rs. '.$credited['points'];
			}
		}
		if ( $credited['cash'] > 0 ) {
			if ( $more ) {
				$content .= ' & Cash Rs. '.$credited['cash'];
			} else {
				$content .= 'Cash Rs. '.$credited['cash'];
			}
		}
		return $this->send($mobile, $content);
	}
	
	public function referralCode($mobile, $amount, $code){
		$content = 'Account Credited: Your account has been credited with Rs. '.$amount.' as referral code '.$code;
		return $this->send($mobile, $content);
	}
	
	public function orderReferralTo($mobile, $amount, $toName){
		$content = 'Account Credited: Your account has been credited with Rs. '.$amount.' as you refer to '.$toName;
		return $this->send($mobile, $content);
	}
	
	public function orderCancellled($mobile, $orderId){
		$orderId = $this->orderPrefix.$orderId;
		$text = 'Hi Dear, your order at '.PC['COMPANY']['tag'].' are cancel. Your order number: '.$orderId;
		return $this->send($mobile, $text);
	}
	
	public function customerRegister($mobile, $email, $pass){
		$text = 'Welcome to '.PC['COMPANY']['tag'].', you have been successfully registered with us as username: '.$email.' and password: '.$pass;
		return $this->send($mobile, $text);
	}
	
	public function shipmentSend($mobile, $orderId, $trackerId){
		$text = 'Order Id: '.$orderId.' has been shipped with AWB No. '.$trackerId.'. https://track.delhivery.com/p/'.$trackerId.' to track. You will receive another sms when courier executive is out for delivery.';
		return $this->send($mobile, $text);
	}	
	
	public function send($mobile, $text) {
		$status = true;
		$requestUrl = $this->base_url.'?method=sms&api_key='.$this->key.'&to='.$mobile.'&sender='.$this->sender_id.'&message='.urlencode($text);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $requestUrl );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
		$response = curl_exec($ch);
		if(curl_errno($ch)){
			$status = false;
		}else{
			$status = json_decode($response, TRUE);
		}
		curl_close($ch);
		return $status;
    }
}
