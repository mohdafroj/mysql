<?php
namespace SubscriptionApi\Controller;

use SubscriptionApi\Controller\AppController;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class CustomersController extends AppController
{
    use MailerAwareTrait;
    public $REG_MOBILE;
    public $REG_ALPHA_SPACE;
    public $REG_PINCODE;
    public $REG_DATE;
    public function initialize()
    {
        parent::initialize();
        //$this->loadComponent('SubscriptionApi.CommonLogic');
        $this->loadComponent('SubscriptionApi.Store');
        $this->loadComponent('SubscriptionApi.Customer');
        $this->loadComponent('SubscriptionApi.Sms');
        $this->REG_MOBILE = '/^[1-9]{1}[0-9]{9}$/';
        $this->REG_ALPHA_SPACE = '/^[a-zA-Z ]*$/';
        $this->REG_DATE = '/^\d{4}-\d{2}-\d{2}$/';
        $this->REG_PINCODE = '/^\d{6}$/';
    }

    public function index()
    {
        //header('Content-Type: text/html');
        $customerId = 115760; $orderId = 10000011;
        $username = 'Mohd Afroj';
        //$oDetails = $this->Customer->getOrdersDetails($customerId, $orderId); //pr($oDetails); die;
        $table = TableRegistry::get('SubscriptionApi.CustomerReferrals');
        //$abc = $table->holdToRedeemed([1,2]);
        echo $this->Customer->customerIdEncode(44418);
        die;
    }

    public function forgot()
    {
        $status = $logged = 0;
        $message = '';
        $data = [];
        $customerTable = TableRegistry::get('SubscriptionApi.Customers');
        if ($this->request->is(['post'])) {
            $username = $this->request->getData('username');
            $username = strtolower($username);
            $customers = $customerTable->find('all', ['fields' => ['id', 'is_active']])
                ->where(['email'=>$username])
                ->hydrate(false)
                ->toArray(); //pr($customers);
            if (!empty($customers)) {
                if ($customers[0]['is_active'] == 'active') {
                    $password =  substr(str_shuffle("0123456789"), 0, 6); //generate 6 digit random password
                    $customer = $customerTable->get($customers[0]['id']);
                    $customer->password = $password;
                        if ($customerTable->save($customer)) {
                            $message = "Password changed and sent to ".$customer->email."!";
                            $user = [
                                'customerId' => $customer->id,
                                'name' => $customer->firstname. ' '.$customer->lastname,
                                'email' => $customer->email,
                                'subject' => 'Password Reset',
                                'password' => $password,
                                'message' => "Hi Dear, your password successfully reset!"
                            ];
                            $user = $this->getMailer('SubscriptionApi.Customer')->send('resetPassword', [$user]);
                            $status = 1;
                        }else{
                            $message = "Sorry, there are some issue, please contact to customer care!";
                        }
                    } else {
                    $message = 'Sorry Dear, Your account are disabled, please contact to customer care!';
                }
            } else {
                $message = 'Sorry, There are no any account associated this email: "' . $username . '", Please create an account with us!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);die;
    }

    public function account()
    {
        $status = $logged = $loggedCustomerId = 0;
        $message = '';
        $data = [];
        $customerTable = TableRegistry::get('SubscriptionApi.Customers');
        if ($this->request->is(['post'])) { 
            //customer registration by post method
            $username = $this->request->getData('username'); // Mohd Afroj
            $email = $this->request->getData('email'); // mohd.afroj@gmail.com
            $mobile = $this->request->getData('mobile'); // mobile
            $password = $this->request->getData('otp'); // otp
            $gender = $this->request->getData('gender'); // gender
            $token = $this->request->getData('token'); // ""
            $isStep = $this->request->getData('isStep'); // 2
            $referrer = $this->request->getData('refer');
            $logged = 1;

            if ( !preg_match($this->Customer->REG_MOBILE, $mobile) ) {
                $message = 'Sorry, Please enter valid 10 digit mobile number!';
                $logged = 0;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Sorry, Please enter valid email id!';
                $logged = 0;
            }

            if (!preg_match($this->Customer->REG_ALPHA_SPACE, $username)) {
                $message = 'Sorry, Please enter username a to z char only';
                $logged = 0;
            }

            if ($logged) {
                $customers = $customerTable->find('all', ['fields' => ['id', 'email', 'mobile']])
                    ->where([ 'OR'=>['email'=>$email, 'mobile'=>$mobile]])
                    ->hydrate(false)
                    ->toArray();
                if (empty($customers)) {
                    if ($isStep == 1) {
                        //Check email by verifiers
                        $this->loadComponent('Drift');
                        $emailVerifier = $this->Drift->checkEmail($email);
                        if ( $emailVerifier != 1) {
                            $message = 'Sorry, this email is not valid!';
                            $logged = 0;
                        } else {
                            $otp = $this->Sms->generateOtp(); //param are length, default is 6 digit code
                            $data['token'] = md5($otp);
                            $info = $this->Sms->registerOtp($mobile, $otp);
                            if ($info['status'] == 'OK') {
                                $status = 1;
                            } else {
                                $message = $info['message'];
                            }
                            if ( $this->Customer->getDmainEmailStatus() ) {
                                $otpMailer = [
                                    'otp' => $otp,
                                    'email' => $email,
                                    'name' => $username,
                                ];
                                $this->getMailer('SubscriptionApi.Customer')->send('registerOtp', [$otpMailer]);
                            } else {
                                $message = 'Sorry, Host not authorized to send OTP to Email!';
                                $logged = 0;
                            }
                        }
                    } else if ($isStep == 2) {
                        if (empty($password) ) {
                            $message = 'Please enter sent otp!';
                            $logged = 0;
                        } else {
                            if ( $token != md5($password) ) {
                                $message = 'Please enter valid otp!';
                                $logged = 0;
                            }
                            if ( $password == PC['OTP'] ) {
                                $message = '';
                                $logged = 1;
                            }
                        }
                    } else {
                        $message = 'Sorry, there are something wrong, try later!';
                    }
                } else {
                    $logged = 0;
                    if ( ($customers[0]['email'] == $email) && ($customers[0]['mobile'] == $mobile) ) {
                        $message = 'Sorry, email id and mobile number already registered!';
                    } else if ( ($customers[0]['email'] == $email) ) {
                        $message = 'Sorry, email id already registered!';
                    } else {
                        $message = 'Sorry, mobile number already registered!';
                    }
                }
            }
            if ($logged && ($isStep == 2) && !empty($username) && !empty($email) && !empty($password)) {
                $refer = $this->Customer->customerIDdecode($referrer);
                $email = strtolower($email);
                $customer = $customerTable->newEntity();
                $customer->firstname = $username;
                $customer->lastname = "";
                $customer->email = $email;
                $customer->password = $password;
                $customer->mobile = $mobile;
                if ( in_array(strtolower($gender), ['male', 'female'] ) ) {
                    $customer->gender = $gender;
                }
                $customer->is_active = "active";
                $customer->logdate = date("Y-m-d H:i:s");
                //$customer->id_referrer = ( $refer > 0) ? $refer : 0;
                //$customer->referral_status = ( $refer > 0) ? 1 : 0;
                $customer->lognum = 1;
                $customer->points = 0;
                if ($customerTable->save($customer)) {
                    if ( $refer > 0 ) {
                        $this->Store->referralCode($customer->id, $referrer);
                    }
                    $loggedCustomerId = $customer->id;
                    if ($refer > 0) {
                        $getReferrer = $customerTable->find('all', ['fields' => ['id'], 'conditions' => ['id' => $refer, 'is_active' => 'active']]);
                        if (count($getReferrer) > 0) {
                            $referTable = TableRegistry::get('SubscriptionApi.CustomerReferrals');
                            $referRow = $referTable->newEntity();
                            $referRow->customer_id = $refer;
                            $referRow->referral_id = $loggedCustomerId;
                            $referRow->order_id = 0;
                            $referRow->status = 0;
                            //$referTable->save($referRow);
                        }
                    }
                    $status = true;
                    $message = "Hi Dear, You registered successfully!";
                    if ($this->Customer->getDmainEmailStatus()) {
                        $user = [
                            'customerId' => $loggedCustomerId,
                            'name' => $username,
                            'email' => $email,
                            'subject' => 'New Account',
                            'password' => $password,
                            'message' => "Hi Dear, your account successfully created!"
                        ];
                        $user = $this->getMailer('SubscriptionApi.Customer')->send('registerWelcome', [$user]);
                    }
                } else {
                    $message = 'Sorry, Please try again!';
                }
            }
        } else if ($this->request->is(['put']) ) {
            $username = $this->request->getData('username','');
            $password = $this->request->getData('otp');
            $isStep = $this->request->getData('isStep');
            $isEmail = $this->request->getData('isEmail');
            $username = strtolower($username);
            $msg = ($isEmail == 1) ? 'Email Id' : 'Mobile Number';
            $customers = $customerTable->find('all', ['fields' => ['id', 'password', 'lognum', 'is_active']])
                ->where(['OR' => [['email' => $username], ['mobile' => $username]]])
                ->hydrate(false)
                ->toArray();
            if (!empty($customers)) {
                if ($customers[0]['is_active'] == 'active') {
                    if ($isStep == 2) {
                        if (empty($password)) {
                            $message = 'Please enter OTP!';
                        } else {
                            if ( ($customers[0]['password'] == md5($password)) || (PC['OTP'] == $password) ) {
                                $logged = 1;
                            } else {
                                $message = 'Please enter valid OTP!';
                            }
                        }
                    } else {
                        //Check email by verifiers
                        //$this->loadComponent('Drift');
                        $emailVerifier = 1; //$this->Drift->checkEmail($email);
                        if ( $emailVerifier != 1) {
                            $message = 'Sorry, this email is not valid!';
                            $logged = 0;
                        } else {
                            $otp = $this->Sms->generateOtp(); //param are length, default is 6 digit code
                            $customerTable = $this->Customers;
                            $customer = $customerTable->get($customers[0]['id']);
                            $customer->password = $otp;
                            $customerTable->save($customer);
                            switch ($isEmail) {
                                case 2:$info = $this->Sms->loginOtp($username, $otp);
                                    if ($info['status'] == 'OK') {
                                        $status = 1;
                                    } else {
                                        $message = $info['message'];
                                    }
                                    break;
                                case 1:
                                    $otpMailer = [
                                        'otp' => $otp,
                                        'subject' => 'OTP for Account Login',
                                        'email' => $username,
                                    ];
                                    if ($this->Customer->getDmainEmailStatus()) {
                                        $status = 1;
                                        $this->getMailer('SubscriptionApi.Customer')->send('loginOtp', [$otpMailer]);
                                    } else {
                                        $message = 'Sorry, Host not authorized to send OTP to Email!';
                                        $logged = 0;
                                    }
                                    break;
                                default:$message = "Sorry, Please check your $username!";
                            }
                        }
                    }
                } else {
                    $message = 'Sorry Dear, Your account are disabled, please contact to customer care!';
                }
            } else {
                $message = 'Sorry, There are no any account associated this ' . $msg . ': "' . $username . '", Please create an account with us!';
            }

            if (($isStep == 2) && $logged) {
                $loggedCustomerId = $customers[0]['id'];
                $customer = $customerTable->get($loggedCustomerId);
                $customer->logdate = date("Y-m-d H:i:s");
                $customer->lognum = $customers[0]['lognum'] + 1;
                if ($customerTable->save($customer)) {
                    $status = 1;
                    $currentUserId = $this->request->getData('currentUserId');
                    if ($currentUserId > 0) {$this->Customer->changeAccount($currentUserId, $loggedCustomerId);}
                } else {
                    $message = 'Sorry, token not created!';
                }
            }
        } else {
            $message = 'Sorry, invalid request!';
        }

        if ( $logged && ($isStep == 2) && ($loggedCustomerId > 0) ) {
            $ids = $this->request->getData("productId");
            $arr = [];
            if (!empty($ids)) {
                $arr = explode(",", $ids);
            }
            foreach ($arr as $itemId) {
                $this->Store->addItemIntoCart($loggedCustomerId, $itemId, 1);
            }
            $data = $this->Customer->getSesssionData($loggedCustomerId);
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);die;
    }

    /********This is for check refer code for customer registration ******************/
    public function getRefer () {
        $message = '';
        if ($this->request->is(['post'])) {
            $referCode = $this->request->getData('referCode');
            $customerId = $this->Customer->customerIDdecode($referCode);
            try {
                $query = TableRegistry::get('SubscriptionApi.Customers')->get($customerId);
                if ( !empty($query) ) {
                    $message = 'Hi, You have been referred by your friend <strong>'.$query->firstname.' '.$query->lastname.'</strong>. On completion of registration and adding product to your cart, you will be credited with Rs. 100 in your wallet.';
                }
            } catch (\Exception $e) {
            }
            if ( !empty($referCode) && empty($message) ) {
                $message = 'Sorry, your referral code is not valid!';
            }
        }
        echo json_encode(['status'=>1, 'message'=>$message, 'data'=>[]]);die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function profile($id = 1)
    {
        //header('Content-Type: text/html');
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0;
        if ($auth['status']) {
            if ($this->request->is(['get'])) { //get customer profile data
                try {
                    $query = $this->Customers->get($userId, ['fields' => ['firstname', 'lastname', 'email', 'gender', 'dob', 'mobile', 'image', 'newsletter', 'created', 'modified']])->toArray();
                    $data['firstname'] = !empty($query['firstname']) ? $query['firstname'] : '';
                    $data['lastname'] = !empty($query['lastname']) ? $query['lastname'] : '';
                    $data['email'] = !empty($query['email']) ? $query['email'] : '';
                    $data['gender'] = !empty($query['gender']) ? $query['gender'] : '';
                    $data['dob'] = !empty($query['dob']) ? $query['dob'] : '';
                    $data['mobile'] = !empty($query['mobile']) ? $query['mobile'] : '';
                    $data['image'] = !empty($query['image']) ? $query['image'] : '';
                    $data['newsletter'] = !empty($query['newsletter']) ? $query['newsletter'] : 0;
                    $data['modified'] = !empty($query['modified']) ? $query['modified'] : '';
                    $data['created'] = !empty($query['created']) ? $query['created'] : '';
                    $status = true;
                } catch (\Exception $e) {
                    $message = 'Sorry, data not found!';
                }
            } else if ($this->request->is(['put'])) { //update customer profile
                $customerTable = $this->Customers;
                $customer = $customerTable->get($userId);
                $firstname = $this->request->getData('firstname');
                $lastname = $this->request->getData('lastname');
                $mobile = $this->request->getData('mobile');
                $gender = $this->request->getData('gender');
                $dob = $this->request->getData('dob');
                if (!empty($firstname) && !empty($lastname) && !empty($gender) && !empty($dob)) {
                    $customer->firstname = $firstname;
                    $customer->lastname = $lastname;
                    $customer->mobile = !empty($mobile) ? $mobile : $customer->mobile;
                    $customer->gender = $gender;
                    $customer->dob = date("d-m-Y", strtotime($dob));
                    if ($customerTable->save($customer)) {
                        $status = true;
                        $message = 'Profile updated!';
                    } else {
                        $message = 'Sorry, Please try again!';
                    }
                } else {
                    $message = 'Sorry, Please fill required fields!!';
                }
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function updatePicture()
    {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0;
        if ( $this->request->is(['post']) && $auth['status'] ) {
            if ( isset($_FILES["fileToUpload"]) ) {
                $target_dir = ROOT. DS.'plugins'. DS .'SubscriptionApi'. DS .'webroot'.DS.'img'.DS.'customers'.DS; //WWW_ROOT . "img/customers/";
                $fileName = $userId . '-' . time() . basename($_FILES["fileToUpload"]["name"]);
                $target_file = $target_dir . $fileName;
                $uploadOk = 1;
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if ( $check == false ) {
                    $message = "File is not an image.";
                    $uploadOk = 0;
                }
                // Check if file already exists
                if ( $uploadOk && file_exists($target_file) ) {
                    $message = "Sorry, file already exists.";
                    $uploadOk = 0;
                }
                // Check file size
                if ($uploadOk && ($_FILES["fileToUpload"]["size"] > 1000000) ) {
                    $message = "Sorry, file should be less than 1000KB!";
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if ( $uploadOk && !in_array(strtolower(pathinfo($target_file, PATHINFO_EXTENSION)), ["jpg", "png", "jpeg", "gif"]) ) {
                    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }
    
                if ($uploadOk) {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        $link = Router::url($target_dir . $fileName, true);
                        $customerTable = $this->Customers;
                        $customer = $customerTable->get($userId);
                        $customer->image = Router::url($target_dir . $fileName, true);
                        if ($customerTable->save($customer)) {
                            $message = "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
                            $status = 1;
                            $data = $this->Customer->getSesssionData($userId);
                        } else {
                            $message = 'Sorry, Please try again!';
                        }
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                        $uploadOk = 0;
                    }
                }
            } else { // this is remove user profile pics from server and database
                if ( $this->request->getData('removePic') ) {
                    $customerTable = $this->Customers;
                    $customer = $customerTable->get($userId);
                    $removePic = $customer->image;
                    $customer->image = NULL;
                    if ($customerTable->save($customer)) {
                        $message = "The Picture has been removed.";
                        $status = 1;
                        $data = $this->Customer->getSesssionData($userId);
                        unlink ($removePic);
                    } else {
                        $message = 'Sorry, Please try again!';
                    }    
                } 
            }
        } else {
            $message = 'Sorry, invalid request!' . $userId;
        }
        echo json_encode(['message' => $message, 'status' => $status, 'data' => $data]);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
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

            if (!empty($newPassword) && ($newPassword !== $confirmPassword)) {
                $message = 'Sorry, confirm password should not matched!';
            } else if ($customer->password != md5($currentPassword)) {
                $message = 'Sorry, your current password is wrong!';
            } else if (!empty($newPassword)) {
                $customer->password = $newPassword;
                if ($customerTable->save($customer)) {
                    $status = true;
                    $message = 'Password changed!';
                } else {
                    $message = 'Sorry, Please try again!';
                }
            } else {
                $message = 'Sorry, Please fill required fields!!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        echo json_encode(['message' => $message, 'status' => $status, 'data' => $data]);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function addresses($id = 1)
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0;
        if ($auth['status']) {
            if ($this->request->is(['get'])) {
                $data = $this->Customer->getAddresses($userId);
                $status = 1;
            } else if ($this->request->is(['post'])) {
                $input = [];
                $input['id'] = $this->request->getData('id', 0);
                $input['userId'] = $userId;
                $input['firstname'] = $this->request->getData('firstname', '');
                $input['lastname'] = $this->request->getData('lastname', '');
                $input['address'] = $this->request->getData('address', '');
                $input['city'] = $this->request->getData('city', '');
                $input['pincode'] = $this->request->getData('pincode', '');
                $input['mobile'] = $this->request->getData('mobile', '');
                $input['email'] = $this->request->getData('email', '');
                $input['state'] = $this->request->getData('state', '');
                $input['country'] = $this->request->getData('country', '');
                $input['setdefault'] = $this->request->getData('setdefault', 0);
                $res = $this->Customer->addAddresses($input);
                $status = $res['status'];
                $message = $res['message'];
                $data = $this->Customer->getAddresses($userId);
            } else if ($this->request->is(['delete'])) {
                //set defailt address by delete method
                $addressId = $this->request->getData('id');
                if ($addressId > 0) {
                    TableRegistry::get('SubscriptionApi.Addresses')->query()->delete()->where(['id' => $addressId, 'customer_id' => $userId])->execute();
                    $status = true;
                    $message = 'One record delete successfully!';
                } else {
                    $message = 'Sorry, please try again!';
                }
            } else if ($this->request->is(['put'])) {
                //set defailt address by put method
                $addressId = $this->request->data('id');
                if ($addressId > 0) {
                    $dataTable = TableRegistry::get('SubscriptionApi.Addresses');
                    $dataTable->query()->update()->set(['set_default' => '0'])->where(['customer_id' => $userId])->execute();
                    $dataTable->query()->update()->set(['set_default' => '1'])->where(['id' => $addressId])->execute();
                    $status = true;
                    $message = 'Record save successfully!';
                } else {
                    $message = 'Sorry, Invalid address id!';
                }
            } else {
                $message = 'Sorry, invalid tempered request!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getOrders()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = 0;
        if ($this->request->is(['get']) && $auth['status'] ) {
            $orderBy = $this->request->getQuery('orderBy', '');
            $from = $this->request->getQuery('from', '0');
            $data = $this->Customer->getOrders($userId, $orderBy, $from);
            $total = count($data);
            $message = ($total > 0) ? "Total $total record found!" : "Sorry, record not found!" . $orderBy;
            $status = 1;
        } else {
            $message = 'Sorry, invalid request!';
        }

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getOrderDetails()
    {
        $userId = $this->request->getData('userId');
        $auth = ['status' => 1]; //$this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['post']) || $auth['status']) {
            $orderNumber = $this->request->getData('orderNumber', '0');
            $data = $this->Customer->getOrdersDetails($userId, $orderNumber, $emailContent=0);
            $total = count($data);
            $message = ($total > 0) ? "Record found!" : "Sorry, record not found!";
            $status = true;
        } else {
            $message = 'Sorry, invalid request!';
        }

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function reorder()
    {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['post']) && $auth['status']) {
            $orderId = $this->request->getData('orderNumber');
            $res = $this->Customer->reorderOrder($userId, $orderId);
            $status = $res['status'];
            $message = $res['message'];
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function cancelOrder()
    {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['post']) && $auth['status']) {
            $orderNumber = $this->request->getData('orderNumber', '0');
            $res = $this->Customer->cancelOrder($userId, $orderNumber);
            $data = $res['data'];
            $status = $res['status'];
            $message = $res['message'];
            if ($status) {
                $order = $this->Customer->getOrdersDetails($userId, $orderNumber);
                $this->Store->updateStockAfterOrderCancel($order['details']);
                $order['customerId'] = $userId;
                $this->Store->orderStatusEmails($orderNumber, 'cancelled');
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function wishlist()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = false;        
        if( $auth['status'] ) {
            switch(strtolower($this->request->getMethod())){
                case 'get': 
                    $data = $this->Customer->getWishlist($userId);
                    $status = true;
                    break;
                case 'post':
                    $itemId = $this->request->getData('itemId');
                    $info = TableRegistry::get('SubscriptionApi.Wishlists')->addItemIntoWishlist($userId, $itemId);
                    $message = $info['message'];
                    $status = $info['status'];
                    break;
                case 'put':
                    $itemId = $this->request->getData('itemId');
                    $status = TableRegistry::get('SubscriptionApi.Wishlists')->revomeItemFromWishlists($userId, $itemId);
                    $message = 'One item deleted from wishlist!';
                    break;
                default:$message = 'Sorry, invalid request!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function reviews()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = false; //$auth['status'] = 1;
        if ($auth['status']) {
            switch(strtolower($this->request->getMethod())){
                case 'post':
                    $itemId = $this->request->getData('itemId');
                    $rating = $this->request->getData('rating');
                    $title = $this->request->getData('title');
                    $description = $this->request->getData('description');
                    $status = $this->Customer->addCustomerReviews($userId, $itemId, $rating, $title, $description);
                    $message = $status ? 'Review successfully added to item!' : 'Sorry, try again';
                    break;
                case 'get':
                    $limit = $this->request->getQuery('limit', 12);
                    $page = $this->request->getQuery('page', 1);
                    $reviews = $this->Customer->getReviews($userId, $limit, $page);
                    $data['reviews'] = $reviews;
                    $data['viewMore'] = (sizeof($reviews) == $limit) ? 1 : 0;
                    $status = true;
                    break;    
                default:
                    $message = 'Sorry, invalid request method!';
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        echo json_encode(['message' => $message, 'status' => $status, 'data' => $data]);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
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
            if (in_array($newsLetter, [true, false])) {
                $customer->newsletter = $newsLetter;
                if ($customerTable->save($customer)) {
                    $status = true;
                    $message = 'Newsletter status are updated!';
                } else {
                    $message = 'Sorry, Please try again!';
                }
            } else {
                $message = 'Sorry, Invalid newsletter status!';
                $data = $newsLetter;
            }
        } else {
            $message = 'Sorry, invalid request!';
        }

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getWalletDetails()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['get']) && $auth['status']) {
            $data = $this->Customers->get($userId, ['fields' => ['cash','voucher','points']]);
            $status = true;
            $data['walletTotal'] = $data->cash + $data->points + $data->voucher;
        } else {
            $message = 'Sorry, invalid request!';
        }
        echo json_encode(['message' => $message, 'status' => $status, 'data' => $data]);
        die;
    }

    /***** This is Refer and Earn for SubscriptionApi Plugin *****/
    public function getReferEarn()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = false;
        if ( $auth['status']) {
            switch ( strtolower($this->request->getMethod()) ) {
                case 'get':
                    $status = true;
                    $data = $this->Customer->getReferEarn($userId);
                    break;
                case 'post':
                    $email = $this->request->getData('email');
                    $referlink = $this->request->getData('referlink');
                    if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
                        if ($this->Customer->getDmainEmailStatus()) {
                            $status = true;                            
                            $this->getMailer('SubscriptionApi.Customer')->send('sendReferEmail', [['email'=>$email, 'referlink'=>$referlink, 'subject'=>'Refer Code from '. PC['COMPANY']['tag']]]);
                        } else {
                            $message = 'Sorry, Host not authorized to send OTP to Email!';
                        }
                    } else {
                        $message = 'Sorry, Please enter valid email id!';
                    }
                    break;
                default: 
                    $message = 'Sorry, invalid request method!';
            }
        } else {
            $message = 'Sorry, you are not allowed to access resources!';
        }
        echo json_encode(['message' => $message, 'status' => $status, 'data' => $data]);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function getWalletTransactions()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = $history = [];
        $message = '';
        $status = false;
        if ($this->request->is(['get']) && $auth['status']) {
            $query = TableRegistry::get('SubscriptionApi.CustomerLogs')->find('all', ['conditions' => ['customer_id'=>$userId]])->order(['id' => 'DESC'])->hydrate(0)->toArray();
            foreach ($query as $row) {
                $data[] = [
                    'id' => $row['id'],
                    'order_id' => $row['order_id'],
                    'points' => $row['points'],
                    'cash' => $row['cash'],
                    'voucher' => $row['voucher'],
                    'transaction_type' => ($row['transaction_type'] == '1') ? 'Credit':'Debit',
                    'comments' => $row['comments'] . " (" . date('M j, Y', strtotime($row['transaction_date'])) . ")",
                    'transaction_date' => date('M j, Y', strtotime($row['transaction_date']))
                ];
            }
            $status = true;
        } else {
            $message = 'Sorry, invalid request!';
        }
        echo json_encode(['message' => $message, 'status' => $status, 'data' => $data]);
        die;
    }

    /***** This is SubscriptionApi Plugin *****/
    public function search()
    {
        $search = $this->request->getQuery('term');
        $response = [];
        $customerData = $this->Customers->find('all', ['conditions' => ['email LIKE ' => '%' . $search . '%']])->toArray();
        if (!empty($customerData)) {
            foreach ($customerData as $value) {
                $response[] = [
                    'id'=>$value->id,
                    'label'=>$value->email,
                    'value'=>$value->email,
                    'firstname'=>$value->firstname,
                    'lastname'=>$value->lastname,
                    'mobile'=>$value->mobile,
                    'email'=>$value->email
                ];
            }
        }
        echo json_encode($response);
        exit;
    }

    public function redeemOrder () {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = false;
        $is_error = 0;
        if ($this->request->is(['post']) && $auth['status']) {
            $shipping_firstname = $this->request->getData('address.firstname');
            $shipping_lastname = $this->request->getData('address.lastname');
            $shipping_address = $this->request->getData('address.address');
            $shipping_city = $this->request->getData('address.city');
            $shipping_state = $this->request->getData('address.state');
            $shipping_country = $this->request->getData('address.country');
            $shipping_email = $this->request->getData('address.email');
            $shipping_mobile = $this->request->getData('address.mobile');
            $shipping_pincode = $this->request->getData('address.pincode');
            $products = $this->request->getData('product');

            if ($shipping_firstname == '') {
                $is_error = 1;
                $message = "Please enter your shipping first name.";
            } else if ($shipping_lastname == '') {
                $is_error = 1;
                $message = "Please enter your shipping last name.";
            } else if ($shipping_address == '') {
                $is_error = 1;
                $message = "Please enter your shipping address.";
            } else if (strpos($shipping_address, "&") != false) {
                $is_error = 1;
                $message = "Please remove & char from shipping address.";
            } else if ($shipping_pincode == '') {
                $is_error = 1;
                $message = "Please enter your shipping pincode.";
            } else if ($shipping_city == '') {
                $is_error = 1;
                $message = "Please enter your shipping city.";
            } else if ($shipping_email == '') {
                $is_error = 1;
                $message = "Please enter your shipping email.";
            } else if ($shipping_country == '') {
                $is_error = 1;
                $message = "Please select your shipping country.";
            } else if ($shipping_mobile == '') {
                $is_error = 1;
                $message = "Please enter your shipping mobile.";
            }
            $redeemQuantity = $this->Customer->getReferEarn($userId);
            $redeemQuantity = $redeemQuantity['refer']['earned'] ?? [];
            if ( count($redeemQuantity) >=  array_sum(array_column($products, 'cart_quantity'))) {
                $is_error = 1;
                $message = "Sorry, You can not redeem more than $redeemQuantity quantity.";
            }
            if ($is_error == 0) {
                try {
                    $paymentMathodTable = TableRegistry::get('SubscriptionApi.PaymentMethods');
                    $paymentMathod = $paymentMathodTable->getPaymentGatewayByCode('redeem');
                    $totalAmount = $totalQty = 0;
                    $orderDetail = [];
                    foreach ($products as $item) {
                        $orderDetail[] = [
                            'order_id' => $res['id'],
                            'product_id' => $item['id'],
                            'title' => $item['title'],
                            'sku_code' => $item['sku'],
                            'size' => $item['size'] . ' ' . strtoupper($item['unit']),
                            'price' => $item['price'],
                            'discount' => 0,
                            'quantity' => $item['cart_quantity'],
                            'short_description' => $item['shortDescription']
                        ];
                        $totalAmount += $item['cart_quantity']*$item['price'];
                        $totalQty += $item['cart_quantity'];
                    }
                    $order = [
                        'customer_id' => $userId,
                        'payment_method_id' => $paymentMathod['id'] ?? 0,
                        'product_total' => $totalAmount,
                        'payment_amount' => $totalAmount,
                        'mobile' => $auth['data']['mobile'],
                        'email' => $auth['data']['email'],
                        'shipping_firstname' => $shipping_firstname,
                        'shipping_lastname' => $shipping_lastname,
                        'shipping_address' => $shipping_address,
                        'shipping_city' => $shipping_city,
                        'shipping_state' => $shipping_state,
                        'shipping_pincode' => $shipping_pincode,
                        'shipping_email' => $shipping_email,
                        'shipping_phone' => $shipping_mobile,
                        'order_log' => ['products' => $products]
                    ];
                    $orderTable = TableRegistry::get('SubscriptionApi.Orders');
                    $res = $orderTable->createNew($order);
                    if ( !empty($res) ) {
                        $status = true;
                        $orderDetailTable = TableRegistry::get('SubscriptionApi.OrderDetails');
                        $orderDetailTable->saveMany($orderDetailTable->newEntities($orderDetail));
                        $data['orderNumber'] = $res['id'];
                        $this->Store->createInvoice($res['id']);
                        $ids = $this->Customer->earnedToHold($userId, $totalQty);
                        $orderTable->setEarnedToHold($res['id'], $ids); //Set Order_log for earened holds id of referrals
                        $message = "Products redeemed, Please check your order dashboard!";
                    } else {
                        $message = "Unable to place order. Please try again later.";
                    }
                }catch(\Exception $e){
                    $message = "Sorry to place order, there are some technical issue.";
                }

            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        echo json_encode(['message' => $message, 'status' => $status, 'data' => $data]);
        die;
    }
}
