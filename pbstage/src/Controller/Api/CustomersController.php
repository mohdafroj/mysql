<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Controller\ComponentRegistry;

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
        $this->loadComponent('CommonLogic');
        $this->loadComponent('Sms');
        $this->loadComponent('Store');
        $this->loadComponent('Customer');
        $this->loadComponent('Delhivery');
        $this->REG_MOBILE = '/^[1-9]{1}[0-9]{9}$/';
        $this->REG_ALPHA_SPACE = '/^[a-zA-Z ]*$/';
        $this->REG_DATE = '/^\d{4}-\d{2}-\d{2}$/';
        $this->REG_PINCODE = '/^\d{6}$/';
        //header('Content-Type: text/html');
    }

    public function index()
    {
        $dataTable = TableRegistry::get('Orders');
        $query = $dataTable->find('all', ['fields' => ['id', 'delhivery_pickup_id', 'payment_method_id', 'payment_mode', 'product_total', 'payment_amount', 'discount', 'ship_method', 'ship_amount', 'mode_amount', 'coupon_code', 'tracking_code', 'mobile', 'email', 'status', 'created', 'shipping_firstname', 'shipping_lastname', 'shipping_address', 'shipping_city', 'shipping_state', 'shipping_pincode', 'shipping_country', 'shipping_email', 'shipping_email', 'shipping_phone', 'gift_voucher_amount', 'pb_points_amount', 'pb_cash_amount', 'credit_gift_amount', 'credit_points_amount', 'credit_cash_amount', 'transaction_ip'], 'contain' => ['OrderDetails', 'OrderComments'], 'conditions' => ['Orders.customer_id' => 98954, 'Orders.id' => 100127614]])
        ->contain(['PaymentMethods'=>[
            'queryBuilder'=>function($q){
                return $q->select(['id','title']);
            }]
        ])->hydrate(0)->toArray();
        pr($query);
        die;
    }

    public function account()
    {
        $status = $logged = 0;
        $message = '';
        $data = [];
        if ($this->request->is(['post'])) //customer registration by post method
        {
            $username = $this->request->getData('username');
            $mobile = $this->request->getData('mobile');
            $email = $this->request->getData('email');
            $password = $this->request->getData('password');
            $token = $this->request->getData('token');
            $isStep = $this->request->getData('isStep');
            $referrer = (int) $this->request->getData('ref');
            $logged = 1;

            if (!preg_match($this->Customer->REG_MOBILE, $mobile)) {
                $message = 'Sorry, Please enter 10 digit valid mobile number!';
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
                $customers = $this->Customers->find('all', ['fields' => ['id', 'email', 'mobile']])
                    ->where(['OR' => [['email' => $email], ['mobile' => $mobile]]])
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
                            if ($this->CommonLogic->getDmainEmailStatus()) {
                                $otpMailer = [
                                    'otp' => $otp,
                                    'email' => $email,
                                    'name' => $username,
                                ];
                                $this->getMailer('Customer')->send('registerOtp', [$otpMailer]);
                            } else {
                                $message = 'Sorry, Host not authorized to send OTP to Email!';
                                $logged = 0;
                            }
                        }    
                    } else if ($isStep == 2) {
                        if (empty($password)) {
                            $message = 'Please enter sent otp!';
                            $logged = 0;
                        } else {
                            if ($token != md5($password)) {
                                $message = 'Please enter valid otp!';
                                $logged = 0;
                            }
                        }
                    } else {
                        $message = 'Sorry, there are something wrong, try later!';
                    }
                } else {
                    $logged = 0;
                    if (($customers[0]['email'] == $email) && ($customers[0]['mobile'] == $mobile)) {
                        $message = 'Sorry, both email id and mobile number already registered!';
                    } else if ($customers[0]['email'] == $email) {
                        $message = 'Sorry, email id already registered!';
                    } else if ($customers[0]['mobile'] == $mobile) {
                        $message = 'Sorry, mobile number already registered!';
                    } else {
                        $message = 'Sorry, both email id and mobile number already registered!';
                    }
                }
            }
            if ($logged && ($isStep == 2) && !empty($username) && !empty($email) && !empty($mobile) && !empty($password)) {
                $id_referrer = 0;
                if ($referrer > 0) {
                    $getReferrer = $this->Customers->find('all', ['fields' => ['id'], 'conditions' => ['id' => $referrer, 'is_active' => 'active']]);
                    if (count($getReferrer) > 0) {
                        $id_referrer = $referrer;
                    }
                }
                $email = strtolower($email);
                $customerTable = $this->Customers;
                $customer = $customerTable->newEntity();
                $customer->firstname = $username;
                $customer->lastname = "";
                $customer->email = $email;
                $customer->password = $password;
                $customer->mobile = $mobile;
                $customer->location_id = 33;
                $customer->is_active = "active";
                $customer->logdate = date("Y-m-d H:i:s");
                $customer->lognum = 1;
                $customer->api_token_created_at = date("Y-m-d H:i:s");
                $customer->voucher_amount = 0;
                $customer->pb_points = 0;
                $customer->pb_cash = 0;
                $customer->track_page = "register";
                $customer->id_referrer = $id_referrer;

                if ($customerTable->save($customer)) {
                    $status = true;
                    $data = $this->Customer->getSesssionData($customer->id);
                    $message = "Hi Dear, You registered successfully!";

                    if ($this->CommonLogic->getDmainEmailStatus()) {
                        $user = [
                            'customerId' => $customer->id,
                            'name' => $username,
                            'email' => $email,
                            'subject' => 'New Account',
                            'password' => '',
                            'message' => "Hi Dear, your account successfully created at https://www.perfumebooth.com",
                        ];
                        $user = $this->getMailer('Customer')->send('registerWelcome', [$user]);
                    }

                    $ids = $this->request->getData("productId");
                    $arr = [];
                    if (!empty($ids)) {
                        $arr = explode(",", $ids);
                    }
                    foreach ($arr as $itemId) {
                        $this->Store->addItemIntoCart($customer->id, $itemId, 1);
                    }
                } else {
                    $message = 'Sorry, Please try again!';
                }
            }
        } else if ($this->request->is(['put'])) {
            $username = $this->request->getData('username'); //'mohd.afroj@perfumebooth.com';
            $password = $this->request->getData('password');
            $isStep = $this->request->getData('isStep');
            $isEmail = $this->request->getData('isEmail');
            $username = strtolower($username);
            $msg = ($isEmail == 1) ? 'Email Id' : 'Mobile Number';
            $customers = $this->Customers->find('all', ['fields' => ['id', 'password', 'lognum', 'is_active']])
                ->where(['OR' => [['email' => $username], ['mobile' => $username]]])
                ->hydrate(false)
                ->toArray();
            if (!empty($customers)) {
                if ($customers[0]['is_active'] == 'active') {
                    if ($isStep == 2) {
                        if (empty($password)) {
                            $message = 'Please enter sent otp!';
                        } else {
                            if ($customers[0]['password'] == md5($password)) {
                                $logged = 1;
                            } else {
                                $message = 'Sorry, Please enter valid otp!';
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
                                    if ($this->CommonLogic->getDmainEmailStatus()) {
                                        $status = 1;
                                        $this->getMailer('Customer')->send('loginOtp', [$otpMailer]);
                                    } else {
                                        $message = 'Sorry, Host not authorized to send OTP to Email!';
                                        $logged = 0;
                                    }
                                    break;
                                default:$message = 'Sorry, Please check your username!';
                            }
                        }
                    }
                } else {
                    $message = 'Sorry Dear, Your account are disabled, Please contact to customer care!';
                }
            } else {
                $message = 'Sorry, There are no any account associated this ' . $msg . ': "' . $username . '", Please create an account with us!';
            }

            if (($isStep == 2) && $logged) {
                $id = $customers[0]['id'];
                $customerTable = $this->Customers;
                $customer = $customerTable->get($id);
                $customer->logdate = date("Y-m-d H:i:s");
                $customer->lognum = $customers[0]['lognum'] + 1;
                $customer->api_token_created_at = date("Y-m-d H:i:s");
                $customer->track_page = 'login';
                if ($customerTable->save($customer)) {
                    $status = 1;
                    $currentUserId = $this->request->getData('currentUserId');
                    if ($currentUserId > 0) {$this->Customer->changeAccount($currentUserId, $id);}
                    $data = $this->Customer->getSesssionData($id);
                } else {
                    $message = 'Sorry, token not created!';
                }

                $ids = $this->request->getData("productId");
                $arr = [];
                if (!empty($ids)) {
                    $arr = explode(",", $ids);
                }
                foreach ($arr as $itemId) {
                    $this->Store->addItemIntoCart($id, $itemId, 1);
                }
            }
        } else {
            $message = 'Sorry, invalid request!';
        }

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);die;
    }

    public function profile($id = 1)
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0;
        if ($auth['status']) {
            if ($this->request->is(['get'])) { //get customer profile data
                try {
                    $query = $this->Customers->get($userId, ['fields' => ['firstname', 'lastname', 'email', 'gender', 'dob', 'profession', 'address', 'city', 'pincode', 'mobile', 'image', 'location_id', 'newsletter', 'created', 'modified']])->toArray();
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
                    $data['modified'] = !empty($query['modified']) ? $query['modified'] : '';
                    $data['created'] = !empty($query['created']) ? $query['created'] : '';
                    $data['locations'] = TableRegistry::get('Locations')->find('all', ['fields' => ['id', 'title', 'code'], 'conditions' => ['parent_id' => 2, 'is_active' => 'active'], 'order' => ['title']]);
                    $status = true;
                } catch (\Exception $e) {
                    $message = 'Sorry, data not found!';
                }
            } else if ($this->request->is(['put'])) { //update customer profile
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
                if (!empty($firstname) && !empty($lastname) && !empty($gender) && !empty($dob)) {
                    $customer->firstname = $firstname;
                    $customer->lastname = $lastname;
                    $customer->address = $address;
                    $customer->city = $city;
                    $customer->pincode = $pincode;
                    $customer->location_id = $location_id;
                    $customer->gender = $gender;
                    $customer->dob = date("d-m-Y", strtotime($dob));
                    $customer->profession = $profession;
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

    public function updatePicture()
    {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);
        $data = [];
        $message = '';
        $status = 0;
        if ($this->request->is(['post']) && $auth['status'] && isset($_FILES["fileToUpload"])) {
            $target_dir = WWW_ROOT . "img/customers/";
            $fileName = $userId . '-' . time() . basename($_FILES["fileToUpload"]["name"]);
            $target_file = $target_dir . $fileName;
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
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
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $message = "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $message = "Sorry, there was an error uploading your file.";
                }
            }

            if ($uploadOk) {
                $link = Router::url('/img/customers/' . $fileName, true);
                $customerTable = $this->Customers;
                $customer = $customerTable->get($userId);
                $customer->image = Router::url('/img/customers/' . $fileName, true);
                if ($customerTable->save($customer)) {
                    $message = "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
                    $status = 1;
                    $data = $this->Customer->getSesssionData($userId);
                } else {
                    $message = 'Sorry, Please try again!';
                }
            }
        } else {
            $message = 'Sorry, invalid request!' . $userId;
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

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
                $input['country'] = $this->request->getData('country', 'India');
                $input['setdefault'] = $this->request->getData('setdefault', 0);
                $res = $this->Customer->addAddresses($input);
                $status = $res['status'];
                $message = $res['message'];
                $data = $this->Customer->getAddresses($userId);
            } else if ($this->request->is(['delete'])) {
                //set defailt address by delete method
                $addressId = $this->request->getData('id');
                if ($addressId > 0) {
                    TableRegistry::get('Addresses')->query()->delete()->where(['id' => $addressId, 'customer_id' => $userId])->execute();
                    $status = true;
                    $message = 'One record delete successfully!';
                } else {
                    $message = 'Sorry, please try again!';
                }
            } else if ($this->request->is(['put'])) {
                //set defailt address by put method
                $addressId = $this->request->data('id');
                if ($addressId > 0) {
                    $dataTable = TableRegistry::get('Addresses');
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

    public function getOrders()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = 0;
        if ($this->request->is(['get']) && $auth['status']) {
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

    public function getOrderDetails()
    {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['post']) || $auth['status']) {
            $orderNumber = $this->request->getData('orderNumber', '0');
            $data = $this->Customer->getOrdersDetails($userId, $orderNumber);
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

    public function reorderOrder()
    {
        $userId = $this->request->getData('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['post']) && $auth['status']) {
            $orderNumber = $this->request->getData('orderNumber', '0');
            $res = $this->Customer->reorderOrder($userId, $orderNumber);
            $status = $res['status'];
            $message = $res['message'];
        } else {
            $message = 'Sorry, invalid request!' . $userId;
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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
                $this->getMailer('Customer')->send('orderCancelled', [$order]);
            }
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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
            $data = $dataTable->find('all', ['conditions' => ['customer_id' => $userId]])
                ->contain("ProductsImages", function ($qr) {
                    return $qr->select(['title', 'alt_text', 'img_thumbnail'])->where(['ProductsImages.product_id' => 'Reviews.product_id', 'ProductsImages.is_thumbnail' => 1, 'ProductsImages.is_active' => 'active']);
                })
                ->order(['created' => 'DESC'])
                ->toArray();
            $status = true;
        } else {
            $message = 'Sorry, invalid request!';
        }

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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
        if ($this->request->is(['post']) && $auth['status']) {
            $itemId = $this->request->getData('itemId');
            $rating = $this->request->getData('rating');
            $title = $this->request->getData('title');
            $description = $this->request->getData('description');
            $status = $this->Customer->addCustomerReviews($userId, $itemId, $rating, $title, $description);
            $message = $status ? 'Review successfully added to item!' : 'Sorry, try again';
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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
        if ($this->request->is(['post']) && $auth['status'] && ($itemId > 0)) {
            $info = $this->Customer->addItemIntoWishlist($userId, $itemId);
            $message = $info['message'];
            $status = $info['status'];
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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
        if ($this->request->is(['put']) && $auth['status'] && ($itemId > 0)) {
            $status = $this->Customer->revomeItemFromWishlists($userId, $itemId);
            $message = 'One item deleted from wishlist!';
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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
            $limit = $this->request->getQuery('limit', 12);
            $page = $this->request->getQuery('page', 1);

            $reviews = $this->Customer->getReviews($userId, $limit, $page);
            $data['reviews'] = $reviews;
            $data['viewMore'] = (sizeof($reviews) == $limit) ? 1 : 0;
            $status = true;
        } else {
            $message = 'Sorry, invalid request!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
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

    //###########Please write code for all customer api after this ///////////////############################################
    public function getWalletDetails()
    {
        $userId = $this->request->getQuery('userId');
        $giftVoucherStatus = $this->request->getQuery('giftVoucherStatus');
        $pbPointsStatus = $this->request->getQuery('pbPointsStatus');
        $pbCashStatus = $this->request->getQuery('pbCashStatus');
        if ($giftVoucherStatus == '') {
            $giftVoucherStatus = true;
        }

        if ($pbPointsStatus == '') {
            $pbPointsStatus = true;
        }

        if ($pbCashStatus == '') {
            $pbCashStatus = true;
        }

        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['get']) && $auth['status']) {
            $wallet_data = $this->Customers->get($userId, ['fields' => ['voucher_amount', 'pb_points', 'pb_cash']]);
            $data['v5'] = $wallet_data['voucher_amount'] % 100;
            $data['v3'] = ($wallet_data['voucher_amount'] - 501 * $data['v5']) / 100;
            $data['gift_voucher_amount'] = $wallet_data['voucher_amount'];
            $data['pb_points_amount'] = $wallet_data['pb_points'];
            $data['pb_cash_amount'] = $wallet_data['pb_cash'];
            $data['grand_total'] = $wallet_data['voucher_amount'] + $wallet_data['pb_points'] + $wallet_data['pb_cash'];
            $status = true;
        } else {
            $message = 'Sorry, invalid request!';
        }

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    public function getWalletTransactions()
    {
        $userId = $this->request->getQuery('userId');
        $auth = $this->Customer->isAuth($userId);

        $data = [];
        $message = '';
        $status = false;
        if ($this->request->is(['get']) && $auth['status']) {
            $filterData = [];
            $filterData['id_customer'] = $userId;

            $pbPointsTable = TableRegistry::get('PbCashPoints');
            $history = $pbPointsTable->find('all', ['conditions' => $filterData])->order(['id' => 'DESC'])->toArray();
            $counter = 0;
            foreach ($history as $temp_row) {
                if ($temp_row->transaction_type == '1') {
                    $transaction_type = 'Credit';
                } else {
                    $transaction_type = 'Debit';
                }
                $data[$counter]['id'] = $temp_row->id;
                $data[$counter]['id_order'] = $temp_row->id_order;
                $data[$counter]['pb_cash'] = number_format($temp_row->pb_cash, 2);
                $data[$counter]['pb_points'] = number_format($temp_row->pb_points, 2);
                $data[$counter]['voucher_amount'] = number_format($temp_row->voucher_amount, 2);
                $data[$counter]['transaction_type'] = $transaction_type;
                $data[$counter]['comments'] = $temp_row->comments . " (" . date('M j, Y', strtotime($temp_row->transaction_date)) . ")";
                $data[$counter]['transaction_date'] = date('M j, Y', strtotime($temp_row->transaction_date));
                $counter++;
            }
            $status = true;
        } else {
            $message = 'Sorry, invalid request!';
        }

        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);
        die;
    }

    public function search()
    {
        $search = $this->request->getQuery('term');
        $responseArray = array();
        $customerTable = $this->Customers;
        $customerData = $customerTable->find('all', ['conditions' => ['email LIKE ' => '%' . $search . '%']])->toArray();
        if (!empty($customerData)) {
            $counter = 0;
            foreach ($customerData as $temp_customer) {
                $responseArray[$counter]['id'] = $temp_customer->id;
                $responseArray[$counter]['label'] = $temp_customer->email;
                $responseArray[$counter]['value'] = $temp_customer->email;
                $responseArray[$counter]['firstname'] = $temp_customer->firstname;
                $responseArray[$counter]['lastname'] = $temp_customer->lastname;
                $responseArray[$counter]['address'] = $temp_customer->address;
                $responseArray[$counter]['city'] = $temp_customer->city;
                $responseArray[$counter]['pincode'] = $temp_customer->pincode;
                $responseArray[$counter]['mobile'] = $temp_customer->mobile;
                $responseArray[$counter]['email'] = $temp_customer->email;
                $responseArray[$counter]['location_id'] = $temp_customer->location_id;
                $counter++;
            }
        }
        echo json_encode($responseArray);
        exit;
    }
}
