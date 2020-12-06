<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\Collection\Collection;
use Cake\Mailer\MailerAwareTrait;
use Cake\Routing\Router;

class PagesController extends AppController
{

	use MailerAwareTrait;
	public function initialize(){
		parent::initialize();
		$this->loadComponent('Customer');
	}
	
    public function contactUs()
    {	
		$data = [];
		$message = '';
		$status = false;		
    	if ( $this->request->is(['post']) ) {
			$firstname	 = $this->request->getData('firstname');
			$lastname	 = $this->request->getData('lastname');
			$mobile		 = $this->request->getData('mobile');
			$email 		 = $this->request->getData('email');
			$comment	 = $this->request->getData('comment');
			$email		 = strtolower($email);
			if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
				echo json_encode(['message'=>'Sorry, Please enter valid email id!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			if ( !preg_match($this->Customer->REG_MOBILE, $mobile) ){
				echo json_encode(['message'=>'Sorry, Please enter 10 digit valid mobile number!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			if( !empty($firstname) && !empty($lastname) && !empty($email) && !empty($mobile) ){
				$user = [
						'name'=>$firstname.' '.$lastname,
						'email'=>$email,
						'subject'=>'Thank you for contacting Perfume Booth',
						'mobile'=>$mobile,
						'comment'=>$comment
				];
				$user = $this->getMailer('Customer')->send('contactUs', [$user]);
				if( $user ){
					$status = true;
					$message = "Thank You For Submitting your query. Our ecxcutive will contact you at the earliest.";
				}else{
					$message = 'Sorry, Please try again!';
				}
			}else{
				$message = 'Sorry, Please fill required fields!!';
			}
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response); die;
    }
	
	public function newsletterSubscribe($name = null)
	{
		$data = [];
		$message = '';
		$status = false;		
    	if ( $this->request->is(['post']) ) {
			$email 		= $this->request->getData('email');
			$email		= strtolower($email);
			if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
				echo json_encode(['message'=>'Sorry, Please enter valid email id!', 'status'=>$status, 'data'=>$data]); die;
			}			
			if( !empty($email) ){		
				$dataTable = TableRegistry::get('Subscriptions');
				$result = $dataTable->find('all',['conditions'=>['email'=>$email]])->toArray();
				if( empty($result) ){
					$mydata = $dataTable->newEntity();
					$mydata->email = $email;
					if( $dataTable->save($mydata) ){
						$status = true;
						$message = "Hi dear, your email successfully saved for subscription!";
					}else{
						$message = 'Sorry, Please try again!';
					}
				}else{
					$message = 'Sorry, your email already registered!';
				}
			}else{
				$message = 'Sorry, Please fill required fields!!';
			}
    	}else{
			$message = 'Sorry, invalid request!';
		}		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response); die;
	}
}
