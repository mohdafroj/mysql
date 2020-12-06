<?php
namespace App\Mailer;
use Cake\Mailer\Mailer;

class CustomerMailer extends Mailer
{
	private $SendGridTest = 'SendGridTest';
	private $transport = 'SendGrid';
    private $from = 'privemember@perfumeoffer.com'; 
    private $connect = 'connect@perfumebooth.com';
	private $testmail = 'mohd.afroj@perfumebooth.com';

    public function registerWelcomeTest($user)
    {	

        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject($user['subject'])
			->emailFormat('html')
			->template('Api/Customer/register')
            ->set($user);
	
    }

    public function welcome($user)
    {
        $this
            ->to($user->email)
            ->subject(sprintf('Welcome %s', $user->name))
            ->template('welcome_mail', 'custom'); // By default template with same name as method name is used.
    }

    public function contactUs($user)
    {	
        $this
			->profile(['from'=>$user['email'], 'transport'=>$this->transport])
            ->to('customerservice@perfumebooth.com')
            ->subject($user['subject'])
			->emailFormat('html')
			->template('Api/Customer/contact_us')
            ->set($user);
	
    }

    public function registerWelcome($user)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject($user['subject'])
			->emailFormat('html')
			->template('Api/Customer/register')
            ->set($user);
	
    }

    public function resetPassword($user)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject('Forgot Password at PerfumeBooth')
			->emailFormat('html')
			->template('Api/Customer/reset_password')
            ->set($user);
	
    }

    public function otpSend($user)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject("PerfumeBooth: OTP for COD")
			->emailFormat('html')
			->template('Api/Customer/otp')
            ->set($user);
	
    }

    public function registerOtp($user)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject("PerfumeBooth: OTP for Account Registration")
			->emailFormat('html')
			->template('Api/Customer/registerOtp')
            ->set($user);	
    }

    public function loginOtp($user)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject("PerfumeBooth: OTP for Account Login")
			->emailFormat('html')
			->template('Api/Customer/loginOtp')
            ->set($user);	
    }

    public function orderConfirmed($order)
    {	
        $this
			->profile(['from'=>$this->connect, 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Order Confirmation from PerfumeBooth")
			->emailFormat('html')
			->template('Api/Customer/order_confirm')
            ->set($order);
	
    }
	
    public function orderDelivered($order)
    {	
        $this
			->profile(['from'=>$this->connect, 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Order Delivered by PerfumeBooth")
			->emailFormat('html')
			->template('Api/Customer/order_delivered')
            ->set($order);
	
    }
	
    public function orderDispatched($order)
    {	
        $this
			->profile(['from'=>$this->connect, 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Order Dispatched by PerfumeBooth")
			->emailFormat('html')
			->template('Api/Customer/order_dispatched')
            ->set($order);
	
    }
	
    public function orderIntransit($order)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Order Dispatched by PerfumeBooth")
			->emailFormat('html')
			->template('Api/Customer/order_intransit')
            ->set($order);
	
    }
	
    public function accountCredit($order)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Account Credit Notification at PerfumeBooth")
			->emailFormat('html')
			->template('Api/Customer/order_account_credit')
            ->set($order);
	
    }
	
    public function orderCancelled($order)
    {	
        $this
			->profile(['from'=>$this->connect, 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Order Cancelled at PerfumeBooth")
			->emailFormat('html')
			->template('Api/Customer/order_cancelled')
            ->set($order);
	
    }
    
    public function orderReview($order)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Order Cancelled at PerfumeBooth")
			->emailFormat('html')
			->template('Api/Customer/order_cancelled')
            ->set($order);
	
    }
    
	public function abendedCart($data)
    {	
        $subject  = isset($data['subject']) ? $data['subject']:'Abended Cart at PerfumeBooth';
        $template = isset($data['template']) ? $data['template']:'abended_cart';
        $this
            ->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($data['customer']['email'])
            ->subject($subject)
			->emailFormat('html')
			->template('Drift/'.$template)
            ->set($data);
	
    }
}