<?php
namespace SubscriptionApi\Controller;

use SubscriptionApi\Controller\AppController;
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
		$this->loadComponent('SubscriptionApi.Customer');
	}
	
    public function companyData()
    {   
        $response = ['message' => 'Data info', 'status' => 1, 'data' =>[ 'company' => PC['COMPANY']]];
        echo json_encode($response); die;
    }

    public function zipcodes($code = null)
    {
        $zipcodes = [];
        $response = ['message' => 'Sorry, you don`t have permission!', 'success' => false, 'data' => $zipcodes];
        try {
            $filter = [];
            $filter['zipcode'] = $code;
            $zipcodes = TableRegistry::get('SubscriptionApi.Zipcodes')->find('all', ['conditions' => $filter]);
            if (empty($zipcodes)) {
                throw new Exception(__('Sorry, Record not found!'));
            }
            $response['data'] = $this->paginate($zipcodes);
            $response['message'] = 'Record found!';
            $response['success'] = true;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        echo json_encode($response);die;
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
						'subject'=>'Thank you for contacting '. PC['COMPANY']['tag'],
						'mobile'=>$mobile,
						'comment'=>$comment
				];
				$user = $this->getMailer('SubscriptionApi.Customer')->send('contactUs', [$user]);
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
				$dataTable = TableRegistry::get('SubscriptionApi.Subscriptions');
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
