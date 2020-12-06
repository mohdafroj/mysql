<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * Admin component
 */
class SmsComponent extends Component
{
	public function generateOtp($length=6) {
		$chars = "0123456789";
		return substr(str_shuffle($chars), 0, $length);
    }
	
	public function registerOtp($mobile, $otp){
		$text = $otp.' is the OTP for registration at PerfumeBooth with your mobile number ending '.substr($mobile, -4, 4).'. OTP is valid for 3 minutes only.';
		return $this->send($mobile, $text);
	}
	
	public function loginOtp($mobile, $otp){
		$text = $otp.' is the OTP for account login at PerfumeBooth with your mobile number ending '.substr($mobile, -4, 4).'. OTP is valid for 3 minutes only.';
		return $this->send($mobile, $text);
	}
	
	public function otpSend($mobile, $otp, $amount){
		$text = $otp.' is the OTP for order of Rs. '.$amount.' at PerfumeBooth with your mobile number ending '.substr($mobile, -4, 4).'. OTP is valid for 5 minutes only.';
		return $this->send($mobile, $text);
	}
	
	public function orderSend($mobile, $orderNumber, $amount, $method, $text=''){
		//$text = 'Order ID: '.$orderNumber.' successfully placed with PerfumeBooth. Rs. '.$amount.', Paid as '.$method.'. You will receive shipment details shortly.';
		$content = 'Your order has been successfully placed with PerfumeBooth with Order ID : '.$orderNumber.', Product Name: '.$text.' Paid as :  '.$method.'. You will receive the shipment details shortly.';
		return $this->send($mobile, $content);
	}
	
	public function orderDeliveredSend($mobile, $orderNumber, $date){
		$content = 'Delivered: Your package with '.$orderNumber.' has been delivered on '.$date.'. Thank you for shopping on PerfumeBooth. Please visit again!';
		return $this->send($mobile, $content);
	}
	
	public function orderIntransitSend($mobile, $orderNumber, $date){
		$content = 'Dispatched: Your package with '.$orderNumber.' has been dispatched on '.$date.'. Thank you for shopping on PerfumeBooth. Please visit again!';
		return $this->send($mobile, $content);
	}
	
	public function orderDispatchedSend($mobile, $orderNumber, $date){
		$content = 'Dispatched: Your package with '.$orderNumber.' has been dispatched on '.$date.'. Thank you for shopping on PerfumeBooth. Please visit again!';
		return $this->send($mobile, $content);
	}
	
	public function orderAccountCreditSend($mobile, $creditGiftAmount, $creditPointsAmount, $creditCashAmount){
		$content = 'Account Credited: Your account has been credited with Rs. '.$creditGiftAmount.' gift voucher, PB Points Rs. '.$creditPointsAmount.' & PB Cash Rs. '.$creditCashAmount;
		return $this->send($mobile, $content);
	}
	
	public function cancelSend($mobile, $orderNumber){
		$text = 'Hi Dear, your order at PerfumeBooth are cancel. Your order number: '.$orderNumber;
		return $this->send($mobile, $text);
	}
	
	public function customerRegister($mobile, $email, $pass){
		$text = 'Welcome to perfumebooth.com, you have been successfully registered with us as username: '.$email.' and password: '.$pass;
		return $this->send($mobile, $text);
	}
	
	public function shipmentSend($mobile, $orderNumber, $trackerId){
		$text = 'Order Id: '.$orderNumber.' has been shipped with AWB No. '.$trackerId.'. https://track.delhivery.com/p/'.$trackerId.' to track. You will receive another sms when courier executive is out for delivery.';
		return $this->send($mobile, $text);
	}	
	
	public function send($mobile, $text) {
		$status = true;
		$requestUrl ='http://api-alerts.solutionsinfini.com/v4/?method=sms&api_key=Aa955fed090eb72361610577594051069&to='.$mobile.'&sender=PBOOTH&message='.urlencode($text);
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
