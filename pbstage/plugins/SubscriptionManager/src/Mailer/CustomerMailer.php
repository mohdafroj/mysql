<?php
namespace SubscriptionManager\Mailer;
use Cake\Mailer\Mailer;

class CustomerMailer extends Mailer
{
	private $SendGridTest = 'SendGridTest';
	private $transport = 'SendGrid';
    private $from = PC['COMPANY']['email'];
    private $fromName = PC['COMPANY']['tag'];
	private $testmail = PC['TEST_EMAIL'];
    
    public function registerWelcomeTest($user)
    {	

        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject($user['subject'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/register')
            ->set($user);
	
    }

    public function contactUs($user)
    {	
        $this
			->profile(['from'=>[$user['email']=>'Customer Query'], 'transport'=>$this->transport])
            ->to(PC['COMPANY']['email'])
            ->subject($user['subject'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/contact_us')
            ->set($user);
	
    }

    public function registerWelcome($user)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject($user['subject'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/register')
            ->set($user);
	
    }

    public function otpSend($user)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject(PC['COMPANY']['tag'].": OTP for COD")
			->emailFormat('html')
			->template('SubscriptionManager.Customer/otp')
            ->set($user);
	
    }

    public function sendReferEmail($user)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($user['email'])
            ->subject(PC['COMPANY']['tag'].": Refer Code")
			->emailFormat('html')
			->template('SubscriptionManager.Customer/refer_code')
            ->set($user);	
    }

    public function orderConfirmed($order)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($order['customer']['email'])
            ->subject("Order Confirmation from ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/order_confirm')
            ->set($order);
	
    }
	
    public function orderDelivered($order)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($order['customer']['email'])
            ->subject("Order Delivered by ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/order_delivered')
            ->set($order);
	
    }
	
    public function orderDispatched($order)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($order['customer']['email'])
            ->subject("Order Dispatched by ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/order_dispatched')
            ->set($order);
	
    }
	
    public function orderIntransit($order)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($order['customer']['email'])
            ->subject("Order Dispatched by ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/order_intransit')
            ->set($order);
	
    }
	
    public function orderAccountCredit($order)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($order['customer']['email'])
            ->subject("Account Credit Notification at ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/order_account_credit')
            ->set($order);
	
    }
	
    public function referCredit($userData)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($userData['email'])
            ->subject("Account Credited for referral code by ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionApi.Customer/refer_credit')
            ->set($userData);
	
    }
	
    public function orderReferCredit($userData)
    {	
        $this
			->profile(['from'=>$this->from, 'transport'=>$this->transport])
            ->to($userData['email'])
            ->subject("Account Credited for referral code by ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionApi.Customer/order_refer_credit')
            ->set($userData);
	
    }
	
    public function orderCancelled($order)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($order['customer']['email'])
            ->subject("Order Cancelled at ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/order_cancelled')
            ->set($order);
	
    }
    
    public function orderReview($order)
    {	
        $this
			->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($order['email'])
            ->subject("Order Cancelled at ".PC['COMPANY']['tag'])
			->emailFormat('html')
			->template('SubscriptionManager.Customer/order_cancelled')
            ->set($order);
	
    }
    
	public function abendedCart($data)
    {	
        $subject  = isset($data['subject']) ? $data['subject']:'Abended Cart at '.PC['COMPANY']['tag'];
        $template = isset($data['template']) ? $data['template']:'abended_cart';
        $this
            ->profile(['from'=>[$this->from => $this->fromName], 'transport'=>$this->transport])
            ->to($data['customer']['email'])
            ->subject($subject)
			->emailFormat('html')
			->template('SubscriptionManager.Drift/'.$template)
            ->set($data);
	
    }
}