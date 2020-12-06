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

class CustomersController extends AppController
{
	use MailerAwareTrait;
	public $REG_MOBILE;
	public $REG_ALPHA_SPACE;
	public $REG_PINCODE;
	public $REG_DATE;
	public function initialize(){
		parent::initialize();
		$this->loadComponent('CommonLogic');
		$this->loadComponent('Sms');
		$this->loadComponent('Store');
		$this->loadComponent('Customer');
		$this->loadComponent('Delhivery');
		$this->REG_MOBILE = '/^[1-9]{1}[0-9]{9}$/';
		$this->REG_ALPHA_SPACE = '/^[a-zA-Z ]*$/';
		$this->REG_DATE = '/^\d{4}-\d{2}-\d{2}$/';
		$this->REG_PINCODE = '/^\d{6}$/';
	}
	
	public function index()
	{

		$limit 		= $this->request->getQuery('limit', 10);
		$page	   	= $this->request->getQuery('page', 1);

		$c = $this->Customer->getSesssionData($userId=16597);
		pr($c);
		//echo md5('pass@015'); die;
		$user = [
			'customerId'=>123456,
			'email'=>'mohd.afroj@perfumebooth.com',
			'subject'=>'New Account',
			'password'=>'786afroj',
			'message'=>"Hi Dear Mohd Afroj, your account successfully created at https://www.perfumebooth.com"
		];
		//$user = $this->getMailer('Customer')->send('registerWelcome', [$user]);
		die;
	}
	
    public function register()
    {	
		$data = [];
		$message = '';
		$status = false;		
    	if ( $this->request->is(['post']) )
		{
			$mobile 			= $this->request->getData('mobile');
			$email 				= $this->request->getData('email');
			$password 			= $this->request->getData('password');
			$gender 			= $this->request->getData('gender');
			$referrer 			= (int)$this->request->getData('ref');
			
			if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
				echo json_encode(['message'=>'Sorry, Please enter valid email id!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			if ( !preg_match($this->Customer->REG_MOBILE, $mobile) ){
				echo json_encode(['message'=>'Sorry, Please enter 10 digit valid mobile number!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			if ( empty($password) ){
				echo json_encode(['message'=>'Please enter password!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			
			if( !in_array($gender, ['male','female']) ){
				echo json_encode(['message'=>'Please select your gender!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			$q = $this->Customers->findAllByEmail($email)->toArray();
			$added = !empty($q) ? true : false;
			if ( $added ){
				echo json_encode(['message'=>'Sorry, email already registered!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			$q = $this->Customers->findAllByMobile($mobile)->toArray();
			$added = !empty($q) ? true : false;
			if ( $added ){
				echo json_encode(['message'=>'Sorry, mobile number already registered!', 'status'=>$status, 'data'=>$data]); die;
			}
			
			$id_referrer	= 0;
			if($referrer > 0)
			{
				$getReferrer	= $this->Customers->get($referrer);
				if(count($getReferrer) > 0)
				{
					$id_referrer	= $getReferrer->id;
				}
			}
			
			if( !empty($email) && !empty($mobile) && !empty($password) && !empty($gender) ){
				$email							= strtolower($email);
				$validDate 						= date("Y-m-d H:i:s", strtotime("+1 hour"));
				$customerTable 					= $this->Customers;
				$customer 						= $customerTable->newEntity();
				$customer->firstname 			= "";
				$customer->lastname 			= "";
				$customer->email 				= $email;
				$customer->password 			= $password;
				$customer->mobile 				= $mobile;
				$customer->gender 				= $gender;
				$customer->location_id 			= 33;
				$customer->is_active 			= 'active';
				$customer->api_token 			= md5($password);
				$customer->api_token_created_at = $validDate;
				$customer->voucher_amount		= 0;
				$customer->pb_points 			= 0;
				$customer->pb_cash 				= 0;
				$customer->track_page 			= 'register';
				$customer->id_referrer 			= $id_referrer;
				
				if( $customerTable->save($customer) )
				{
					$id_customer			= $customer->id;
					$transaction_type		= 1;
					$id_referrered_customer	= 0;
					$id_order				= 0;
					$pb_cash				= 0; //REGISTER_PB_CASH;
					$pb_points				= REGISTER_PB_POINTS;
					$voucher_amount			= REGISTER_VOUCHER_AMOUNT;
					$comments				= "Wallet credited for registration on PerfumeBooth";
					$transaction_ip			= $_SERVER['REMOTE_ADDR'];
					//$this->Store->logPBWallet($id_customer, $transaction_type, $id_referrered_customer, $id_order, $pb_cash, $pb_points, $voucher_amount, $comments, $transaction_ip);
					
					$status = true;
					$data = $this->Customer->getSesssionData($customer->id);
					$message = "Hi Dear, you registered successfully!";
					$user = [
						'customerId'=>$customer->id,
						'email'=>$email,
						'subject'=>'New Account',
						'password'=>$password,
						'message'=>"Hi Dear, your account successfully created at https://www.perfumebooth.com"
					];
					$user = $this->getMailer('Customer')->send('registerWelcome', [$user]);

					$ids = $this->request->getData("productId");
					$arr = [];
					if(!empty($ids)){
						$arr = explode(",",$ids);
					}
					foreach($arr as $itemId){
						$this->Store->addItemIntoCart($customer->id, $itemId, 1);
					}


				}
				else
				{
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
	
    public function forgotPassword()
    {	
		$data = [];
		$message = '';
		$status = false;		
    	if ( $this->request->is(['put']) ) {
			$email = $this->request->getData('username');
			$email = strtolower($email);
			$q = $this->Customers->findAllByEmailOrMobile($email, $email)->toArray();
			if( !empty($q) && !empty($email) ){
				$password = $this->Customer->generatePassword(5);
				$customerTable = $this->Customers;
				$customer = $customerTable->get($q[0]['id']);				
				$customer->password = $password;
				if( $customerTable->save($customer) ){
					$status = true;
					$user = [
						'customerId'=>$q[0]['id'],
						'name'=>$q[0]['firstname'].' '.$q[0]['lastname'],
						'email'=>$q[0]['email'],
						'gender'=>$q[0]['gender'],
						'dob'=>$q[0]['dob'],
						'profession'=>$q[0]['profession'],
						'address'=>$q[0]['address'],
						'city'=>$q[0]['city'],
						'pincode'=>$q[0]['pincode'],
						'mobile'=>$q[0]['mobile'],
						'image'=>$q[0]['image'],
						'password'=>$password,
						'message'=>'Your password changed, https://www.perfumebooth.com'
					];
					$this->getMailer('Customer')->send('resetPassword', [$user]);
					$message = "Hi Dear, your password sent to your registered email ".$user['email'];
				}else{
					$message = 'Sorry, Please try again!';
				}
			}else{
				$message = 'Sorry, there are no any account associated to given username!';
			}
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
	
	public function login()
    {   
		$status = $looged = false;
		$message = '';
		$data = [];
		if ($this->request->is(['post'])) {
			$email = $this->request->getData('username'); //'mohd.afroj@perfumebooth.com';
			$pass = $this->request->getData('password'); //'786afroj';
			//$obj = new DefaultPasswordHasher();
			//$pass = $obj->hash($pass);
			$pass = md5($pass);
			$email = strtolower($email);
			$customers = $this->Customers->find('all',['fields'=>['id','lognum'],'conditions'=>['email'=>$email,'password'=>$pass, 'is_active'=>'active']])->toArray();
			if(!empty($customers)){
				$looged = true;
			}else{
				$customers = $this->Customers->find('all',['fields'=>['id','lognum'],'conditions'=>['mobile'=>$email,'password'=>$pass, 'is_active'=>'active']])->toArray();
				if(!empty($customers)){
					$looged = true;
				}
			}
			if( $looged ){
				$validDate = date("Y-m-d H:i:s", strtotime("+1 hour"));
				$id = $customers[0]['id'];
				$customerTable 					= $this->Customers;
				$customer 						= $customerTable->get($id);
				$customer->logdate 				= date("Y-m-d H:i:s");
				$customer->lognum 				= $customers[0]['lognum'] + 1;
				$customer->api_token 			= $pass; //md5($validDate);
				$customer->api_token_created_at = $validDate;
				$customer->track_page 			= 'login';
				if ($customerTable->save($customer)) {
					$status = true;
					$data = $this->Customer->getSesssionData($id);
				}else{
					$message = 'Sorry, token not created!';
				}

				$ids = $this->request->getData("productId");
				$arr = [];
				if(!empty($ids)){
					$arr = explode(",",$ids);
				}
				foreach($arr as $itemId){
					$this->Store->addItemIntoCart($id, $itemId, 1);
				}
				

			}else{
				$message = 'Sorry, username and password did not match!';
			}
		}else{
			$message = 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		echo json_encode($response, true);
    	die;
    }

    public function logout($id = 1)
    {
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);		
		$data = [];
		$message = '';
		$status = false; 
    	if ( $this->request->is(['post']) ) {
			$customer = $this->Customers->get($userId);
			$customer->api_token = NULL;
			$this->Customers->save($customer);
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }

	public function changeAccount()
    {   
		$status = $looged = false;
		$message = '';
		$data = [];
		if ($this->request->is(['post'])) {
			$email = $this->request->getData('username'); //'mohd.afroj@perfumebooth.com';
			$pass = $this->request->getData('password'); //'786afroj';
			$currentUserId = $this->request->getData('currentUserId');
			//$obj = new DefaultPasswordHasher();
			//$pass = $obj->hash($pass);
			$pass = md5($pass);
			$customers = $this->Customers->find('all',['fields'=>['id','lognum'],'conditions'=>['email'=>$email,'password'=>$pass, 'is_active'=>'active']])->toArray();
			if(!empty($customers)){
				$looged = true;
			}else{
				$customers = $this->Customers->find('all',['fields'=>['id','lognum'],'conditions'=>['mobile'=>$email,'password'=>$pass, 'is_active'=>'active']])->toArray();
				if(!empty($customers)){
					$looged = true;
				}
			}
			if( $looged ){
				$validDate = date("Y-m-d H:i:s", strtotime("+1 hour"));
				$id = $customers[0]['id'];
				$customerTable = $this->Customers;
				$customer = $customerTable->get($id);
				$customer->logdate = date("Y-m-d H:i:s");
				$customer->lognum = $customers[0]['lognum'] + 1;
				$customer->api_token = $pass;
				$customer->api_token_created_at = $validDate;
				if ($customerTable->save($customer)) {
					$status = true;
					$this->Customer->changeAccount($currentUserId, $id);
					$data = $this->Customer->getSesssionData($id);
				}else{
					$message = 'Sorry, token not created!';
				}
			}else{
				$message = 'Sorry, username and password should not matched!';
			}
		}else{
			$message = 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
		echo json_encode($response, true);
    	die;
    }

    public function getProfile($id = 1)
    {
		$userId = $this->request->getQuery('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ( $this->request->is(['get']) && $auth['status'] ) {
			$query = $this->Customers->get($userId, ['fields'=>['firstname', 'lastname','email','gender','dob','profession','address','city','pincode','mobile','image','location_id','newsletter','created','modified'],'contain'=>'Addresses'])->toArray();


			if( !empty($query) ){
				$data['firstname'] = !empty($query['firstname']) ? $query['firstname'] : '';
				$data['lastname'] = !empty($query['lastname']) ? $query['lastname'] : '';
				$data['email'] = !empty($query['email']) ? $query['email'] : '';
				$data['gender'] = !empty($query['gender']) ? $query['gender'] : '';
				$data['dob'] = !empty($query['dob']) ? $query['dob'] : '';
				$data['profession'] = !empty($query['profession']) ? $query['profession'] : '';
				$data['address'] = !empty($query['address']) ? $query['address'] : '';
				$data['city'] = !empty($query['city']) ? $query['city'] : '';
				$data['pincode'] = !empty($query['pincode']) ? $query['pincode'] : '';
				$data['mobile'] = !empty($query['mobile']) ? $query['mobile'] : '';
				$data['image'] = !empty($query['image']) ? $query['image'] : '';
				$data['location_id'] = !empty($query['location_id']) ? $query['location_id'] : 0;
				$data['newsletter'] = !empty($query['newsletter']) ? $query['newsletter'] : 0;
				$data['addresses'] = !empty($query['addresses']) ? $query['addresses'] : [];
				$data['modified'] = !empty($query['modified']) ? $query['modified'] : '';
				$data['created'] = !empty($query['created']) ? $query['created'] : '';
			}else{
				$data = [
					'firstname'=>'',
					'lastname'=>'',
					'email'=>'',
					'gender'=>'',
					'dob'=>'',
					'profession'=>'',
					'address'=>'',
					'city'=>'',
					'pincode'=>'',
					'mobile'=>'',
					'image'=>'',
					'location_id'=>0,
					'newsletter'=>0,
					'addresses'=>[],
					'modified'=>'',
					'created'=>''
				];
			}						

			$data['locations'] = TableRegistry::get('Locations')->find('all',['conditions'=>['parent_id'=>2],'order'=>['title']]);
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }

	public function updatePicture()
	{
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		$data = [];
		$message = '';
		$status = false;
    	if ( $this->request->is(['post']) && $auth['status'] && isset($_FILES["fileToUpload"])) {			
			$target_dir = WWW_ROOT."img/customers/";
			$fileName = $userId.'-'.time().basename($_FILES["fileToUpload"]["name"]);
			$target_file = $target_dir . $fileName;
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			// Check if image file is a actual image or fake image
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$message = "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			} else {
				$message = "File is not an image.";
				$uploadOk = 0;
			}
			// Check if file already exists
			if (file_exists($target_file)) {
				$message = "Sorry, file already exists.";
				$uploadOk = 0;
			}
			// Check file size
			if ($_FILES["fileToUpload"]["size"] > 500000) {
				$message = "Sorry, file should be less than 500KB!";
				$uploadOk = 0;
			}
			// Allow certain file formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
				$message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
				$uploadOk = 0;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				$message = "Sorry, your file was not uploaded.";
			// if everything is ok, try to upload file
			} else {
				if ( !move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file) ) {
					$message = "Sorry, there was an error uploading your file.";
				}
			}

			if( $uploadOk ){
				$link = Router::url('/img/customers/'.$fileName, true);
				$customerTable = $this->Customers;
				$customer = $customerTable->get($userId);
				$customer->image = Router::url('/img/customers/'.$fileName, true);
				if( $customerTable->save($customer) ){
					$message = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
					$status = true;			
					$data = $this->Customer->getSesssionData($userId);
				}else{
					$message = 'Sorry, Please try again!';
				}
			}
    	}else{
			$message = 'Sorry, invalid request!'.$userId;
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
	}
	
    public function updateProfile($id = 1){
    
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;		
    	if ( $this->request->is(['put']) && $auth['status'] ) {
			$customerTable = $this->Customers;
			$customer = $customerTable->get($userId);
			$firstname = $this->request->getData('firstname');
			$lastname = $this->request->getData('lastname');
			$address = $this->request->getData('address');
			$city = $this->request->getData('city');
			$pincode = $this->request->getData('pincode');
			$location_id = $this->request->getData('location_id');
			$gender = $this->request->getData('gender');
			$dob = $this->request->getData('dob');
			$profession = $this->request->getData('profession');
			if( !empty($firstname) && !empty($lastname) && !empty($gender) && !empty($dob) ){
				$customer->firstname = $firstname;
				$customer->lastname = $lastname;
				$customer->address = $address;
				$customer->city = $city;
				$customer->pincode = $pincode;
				$customer->location_id = $location_id;
				$customer->gender = $gender;
				$customer->dob = date("d-m-Y", strtotime($dob));
				$customer->profession = $profession;
				if( $customerTable->save($customer) ){
					$status = true;
					$message = 'Profile updated!';
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
    	echo json_encode($response);
		die;
    }

    public function updateSecurity($id = 1)
    {	
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;		
    	if ($this->request->is(['put']) && $auth['status']) {
			$customerTable = $this->Customers;
			$customer = $customerTable->get($userId);
			$currentPassword = $this->request->getData('currentPassword');
			$newPassword = $this->request->getData('newPassword');
			$confirmPassword = $this->request->getData('confirmPassword');
			
			if( !empty($newPassword) && ( $newPassword !== $confirmPassword ) ){
				$message = 'Sorry, confirm password should not matched!';
			}else if( $customer->password != md5($currentPassword) ){
				$message = 'Sorry, your current password is wrong!';
			}else if( !empty($newPassword) ){
				$customer->password = $newPassword;
				if( $customerTable->save($customer) ){
					$status = true;
					$message = 'Password changed!';
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
    	echo json_encode($response);
		die;
    }

    public function getAddresses($id = 1)
    {	
		$userId = $this->request->getQuery('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ( $this->request->is(['get']) && $auth['status'] ) {
			$addressTable = TableRegistry::get('Addresses');
			$q = $addressTable->findByCustomerId($userId)->toArray();
			$addressId = isset($q[0]['id']) ? $q[0]['id'] : 0;
			if($addressId > 0){$data = $addressTable->get($addressId);}			
			$data['locations'] = TableRegistry::get('Locations')->find('all',['conditions'=>['parent_id'=>2],'order'=>['title']]);
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }

    public function updateAddresses($id = 1)
    {	
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;		
    	if ($this->request->is(['put']) && $auth['status']) {
			$addressTable = TableRegistry::get('Addresses');
			$q = $addressTable->findByCustomerId($userId)->toArray();
			$addressId = isset($q[0]['id']) ? $q[0]['id'] : 0;
			if( $addressId > 0 ){
				$address = $addressTable->get($addressId);
			}else{
				$address = $addressTable->newEntity();
			}
			$shipFirstname = $this->request->getData('shipping_firstname');
			$shipLastname = $this->request->getData('shipping_lastname');
			$shipAddress = $this->request->getData('shipping_address');
			$shipCity = $this->request->getData('shipping_city');
			$shipPincode = $this->request->getData('shipping_pincode');
			$shipMobile = $this->request->getData('shipping_mobile');
			$shipEmail = $this->request->getData('shipping_email');
			$shipLocationId = $this->request->getData('shipping_location_id');
			
			$same = $this->request->getData('same_address');
			if( ($same == 1) || ($same == "1") || ($same == "true") || ($same == true) ){
				$billFirstname = $shipFirstname;
				$billLastname = $shipLastname;
				$billAddress = $shipAddress;
				$billCity = $shipCity;
				$billPincode = $shipPincode;
				$billMobile = $shipMobile;
				$billEmail = $shipEmail;
				$billLocationId = $shipLocationId;
			}else{
				$billFirstname = $this->request->getData('billing_firstname');
				$billLastname = $this->request->getData('billing_lastname');
				$billAddress = $this->request->getData('billing_address');
				$billCity = $this->request->getData('billing_city');
				$billPincode = $this->request->getData('billing_pincode');
				$billMobile = $this->request->getData('billing_mobile');
				$billEmail = $this->request->getData('billing_email');
				$billLocationId = $this->request->getData('billing_location_id');
			}
			
			if( !empty($shipFirstname) &&  !empty($shipLastname) && !empty($shipAddress) && !empty($shipCity) && !empty($shipPincode) && preg_match($this->Customer->REG_MOBILE, $shipMobile) && filter_var($shipEmail, FILTER_VALIDATE_EMAIL) && ($shipLocationId > 0) && !empty($billFirstname) && !empty($billLastname) && !empty($billAddress) && !empty($billCity) && !empty($billPincode) && preg_match($this->Customer->REG_MOBILE, $billMobile) && filter_var($billEmail, FILTER_VALIDATE_EMAIL) && ($billLocationId > 0) ){
				$address->customer_id = $userId;
				$address->shipping_firstname = $shipFirstname;
				$address->shipping_lastname = $shipLastname;
				$address->shipping_address = $shipAddress;
				$address->shipping_city = $shipCity;
				$address->shipping_pincode = $shipPincode;
				$address->shipping_mobile = $shipMobile;
				$address->shipping_email = $shipEmail;
				$address->shipping_location_id = $shipLocationId;
				
				$address->billing_firstname = $billFirstname;
				$address->billing_lastname = $billLastname;
				$address->billing_address = $billAddress;
				$address->billing_city = $billCity;
				$address->billing_pincode = $billPincode;
				$address->billing_mobile = $billMobile;
				$address->billing_email = $billEmail;
				$address->billing_location_id = $billLocationId;
				if( $addressTable->save($address) ){
					$status = true;
					$message = 'Hi Dear, addresses updated successfully!';
				}else{
					$message = 'Sorry, Please try again!';
				}
			}else{
				$message = 'Sorry, Please fill required valid fields!';
			}
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }

    public function getOrders()
    {	
		$userId = $this->request->getQuery('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['get']) && $auth['status']) {
			$orderBy = $this->request->getQuery('orderBy','');
			$from = $this->request->getQuery('from','0');
			$data = $this->Customer->getOrders($userId, $orderBy, $from);
			$total = count($data);
			$message = ( $total > 0 ) ? "Total $total record found!":"Sorry, record not found!";
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
    
    public function getOrderDetails()
    {	
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['post']) && $auth['status']) {
			$orderNumber = $this->request->getData('orderNumber','0');
			$data = $this->Customer->getOrdersDetails($userId, $orderNumber);
			$total = count($data);
			$message = ( $total > 0 ) ? "Record found!":"Sorry, record not found!";
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
    
    public function reorderOrder()
    {	
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['post']) && $auth['status']) {
			$orderNumber = $this->request->getData('orderNumber','0');
			$res = $this->Customer->reorderOrder($userId, $orderNumber);
			$status = $res['status'];
			$message = $res['message'];
    	}else{
			$message = 'Sorry, invalid request!'.$userId;
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
    
    public function cancelOrder()
    {	
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['post']) && $auth['status'])
		{
			$orderNumber 	= $this->request->getData('orderNumber', '0');
			$res 			= $this->Customer->cancelOrder($userId, $orderNumber);
			$data 			= $res['data'];
			$status 		= $res['status'];
			$message 		= $res['message'];
			if( $status )
			{
				$order 		= $this->Customer->getOrdersDetails($userId, $orderNumber);
				$this->Store->updateStockAfterOrderCancel($order['details']);
				$order['customerId'] = $userId;
				$this->getMailer('Customer')->send('orderCancelled', [$order]);
			}
    	}
		else
		{
			$message		= 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
        
    public function getReviews()
    {
		$userId = $this->request->getQuery('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['get']) && $auth['status']) {
			$dataTable = TableRegistry::get('Reviews');
			$data = $dataTable->find('all', ['conditions'=>['customer_id'=>$userId]])
					->contain("ProductsImages", function($qr){
						return $qr->select(['title','alt_text','img_thumbnail'])->where(['ProductsImages.product_id'=>'Reviews.product_id', 'ProductsImages.is_thumbnail'=>1, 'ProductsImages.is_active'=>'active']);
					})
					->order(['created' => 'DESC'])
					->toArray();
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
	
    public function addReviews()
    {
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);		
		$data = [];
		$message = '';
		$status = false;
    	if ( $this->request->is(['post']) && $auth['status'] ) {
			$itemId = $this->request->getData('itemId');
			$rating = $this->request->getData('rating');
			$title = $this->request->getData('title');
			$description = $this->request->getData('description');
			$status = $this->Customer->addCustomerReviews($userId, $itemId, $rating, $title, $description);			
			$message = $status ? 'Review successfully added to item!':'Sorry, try again';
    	}else{
			$message = 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
	
    public function getWishlist()
    {
		$userId = $this->request->getQuery('userId');
		$auth = $this->Customer->isAuth($userId);		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['get']) && $auth['status']) {
			$data = $this->Customer->getWishlist($userId);
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
	
    public function addToWishlist()
    {
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);		
		$data = [];
		$message = '';
		$status = false;
		$itemId = $this->request->getData('itemId');
    	if ( $this->request->is(['post']) && $auth['status'] && ($itemId > 0) ) {
			$info = $this->Customer->addItemIntoWishlist($userId, $itemId);
			$message = $info['message'];
			$status = $info['status'];
    	}else{
			$message = 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
	
    public function updateWishlist()
    {
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);		
		$data = [];
		$message = '';
		$status = false;
		$itemId = $this->request->getData('itemId');
    	if ( $this->request->is(['put']) && $auth['status'] && ($itemId > 0) ) {
			$status = $this->Customer->revomeItemFromWishlists($userId, $itemId);
			$message = 'One item deleted from wishlist!';
    	}else{
			$message = 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
	
    public function getCustomerReviews()
    {
		$userId = $this->request->getQuery('userId');
		$auth = $this->Customer->isAuth($userId);		
		$data = [];
		$message = '';
		$status = false;
    	if ($this->request->is(['get']) && $auth['status']) {
			$limit 		= $this->request->getQuery('limit', 12);
			$page	   	= $this->request->getQuery('page', 1);

			$reviews = $this->Customer->getReviews($userId, $limit, $page);
			$data['reviews'] = $reviews;
			$data['viewMore'] = (sizeof($reviews) == $limit) ? 1:0;
			$status = true;
    	}else{
			$message = 'Sorry, invalid request!';
		}
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
	
    public function updateNewsletterStatus($id = 1)
    {	
		$userId = $this->request->getData('userId');
		$auth = $this->Customer->isAuth($userId);
		
		$data = [];
		$message = '';
		$status = false;		
		$newsLetter = $this->request->getData('newsletter');			
    	if ($this->request->is(['put']) && $auth['status']) {
			$customerTable = $this->Customers;
			$customer = $customerTable->get($userId);
			if( in_array($newsLetter,[true,false]) ){
				$customer->newsletter = $newsLetter;
				if( $customerTable->save($customer) ){
					$status = true;
					$message = 'Newsletter status are updated!';
				}else{
					$message = 'Sorry, Please try again!';
				}
			}else{
				$message = 'Sorry, Invalid newsletter status!';
				$data = $newsLetter;
			}
    	}else{
			$message = 'Sorry, invalid request!';
		}
		
		$response = ['message'=>$message, 'status'=>$status, 'data'=>$data];
    	echo json_encode($response);
		die;
    }
    	
	
	//###########Please write code for all customer api after this ///////////////############################################	
    public function getWalletDetails()
    {
		$userId 			= $this->request->getQuery('userId');
		$giftVoucherStatus 	= $this->request->getQuery('giftVoucherStatus');
		$pbPointsStatus 	= $this->request->getQuery('pbPointsStatus');
		$pbCashStatus 		= $this->request->getQuery('pbCashStatus');
		if($giftVoucherStatus == '')
			$giftVoucherStatus	= true;
		if($pbPointsStatus == '')
			$pbPointsStatus		= true;
		if($pbCashStatus == '')
			$pbCashStatus		= true;
		
		$auth 				= $this->Store->isAuth($userId);
		
		$data 		= [];
		$message 	= '';
		$status 	= false;
    	if ( $this->request->is(['get']) && $auth['status'] )
		{
			$wallet_data					= $this->Customers->get($userId, ['fields'=>['voucher_amount',  'pb_points', 'pb_cash']]);
			$data['v5']						= $wallet_data['voucher_amount']%100;
			$data['v3']						= ($wallet_data['voucher_amount']-501*$data['v5'])/100;
			$data['gift_voucher_amount']	= $wallet_data['voucher_amount'];
			$data['pb_points_amount']		= $wallet_data['pb_points'];
			$data['pb_cash_amount']			= $wallet_data['pb_cash'];
			$data['grand_total']			= $wallet_data['voucher_amount'] + $wallet_data['pb_points'] + $wallet_data['pb_cash'];
			$status 						= true;
    	}
		else
		{
			$message			= 'Sorry, invalid request!';
		}
		
		$response	= ['message' => $message, 'status' => $status, 'data' => $data];
    	echo json_encode($response);
		die;
    }
	
	public function getWalletTransactions()
    {
		$userId 			= $this->request->getQuery('userId');
		$auth 				= $this->Store->isAuth($userId);
		
		$data 		= [];
		$message 	= '';
		$status 	= false;
    	if ($this->request->is(['get']) && $auth['status'] )
		{
			$filterData 				= [];
			$filterData['id_customer']	= $userId;
			
			$pbPointsTable	= TableRegistry::get('PbCashPoints');
			$history		= $pbPointsTable->find('all', ['conditions'=>$filterData])->order(['id' => 'DESC'])->toArray();
			$counter		= 0;
			foreach($history as $temp_row)
			{
				if($temp_row->transaction_type == '1')
				{
					$transaction_type	= 'Credit';
				}
				else
				{
					$transaction_type	= 'Debit';
				}
				$data[$counter]['id']				= $temp_row->id;
				$data[$counter]['id_order']			= $temp_row->id_order;
				$data[$counter]['pb_cash']			= number_format($temp_row->pb_cash, 2);
				$data[$counter]['pb_points']		= number_format($temp_row->pb_points, 2);
				$data[$counter]['voucher_amount']	= number_format($temp_row->voucher_amount, 2);
				$data[$counter]['transaction_type']	= $transaction_type;
				$data[$counter]['comments']			= $temp_row->comments." (".date('M j, Y', strtotime($temp_row->transaction_date)).")";
				$data[$counter]['transaction_date']	= date('M j, Y', strtotime($temp_row->transaction_date));
				$counter++;
			}
			$status 		= true;
    	}
		else
		{
			$message		= 'Sorry, invalid request!';
		}
		
		$response	= ['message' => $message, 'status' => $status, 'data' => $data];
    	echo json_encode($response);
		die;
    }
	
	public function search()
	{
		$search 			= $this->request->getQuery('term');
		$responseArray		= array();
		$customerTable		= $this->Customers;
		$customerData		= $customerTable->find('all', ['conditions' => ['email LIKE ' => '%'.$search.'%']])->toArray();
		if(!empty($customerData))
		{
			$counter		= 0;
			foreach($customerData as $temp_customer)
			{
				$responseArray[$counter]['id']			= $temp_customer->id;
				$responseArray[$counter]['label']		= $temp_customer->email;
				$responseArray[$counter]['value']		= $temp_customer->email;
				$responseArray[$counter]['firstname']	= $temp_customer->firstname;
				$responseArray[$counter]['lastname']	= $temp_customer->lastname;
				$responseArray[$counter]['address']		= $temp_customer->address;
				$responseArray[$counter]['city']		= $temp_customer->city;
				$responseArray[$counter]['pincode']		= $temp_customer->pincode;
				$responseArray[$counter]['mobile']		= $temp_customer->mobile;
				$responseArray[$counter]['email']		= $temp_customer->email;
				$responseArray[$counter]['location_id']	= $temp_customer->location_id;
				$counter++;
			}
		}
		echo json_encode($responseArray);
		exit;
	}
}