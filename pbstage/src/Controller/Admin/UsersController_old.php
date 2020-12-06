<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use App\Model\Entity\Admin;
use Cake\Mailer\MailerAwareTrait;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\I18n\Time;

class UsersController extends AppController
{
	use MailerAwareTrait;
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('CommonLogic');
		$this->loadComponent('Store');
		$this->loadComponent('Sms');
		ini_set('max_execution_time', 0);
	}

	public function index()
	{
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
			'limit' => $limit, // limits the rows per page
			'maxLimit' => 2000,
		];
		$filterData = [];
		$username = $this->request->getQuery('username', '');
		$this->set('username', $username);
		if (!empty($username)) {
			$filterData['username'] = $username;
		}

		$firstname = $this->request->getQuery('firstname', '');
		$this->set('firstname', $firstname);
		if (!empty($firstname)) {
			$filterData['firstname'] = $firstname;
		}

		$lastname = $this->request->getQuery('lastname', '');
		$this->set('lastname', $lastname);
		if (!empty($lastname)) {
			$filterData['lastname'] = $lastname;
		}

		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if (!empty($email)) {
			$filterData['email'] = $email;
		}

		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if (!empty($created)) {
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$created%";
		}

		$modified = $this->request->getQuery('modified', '');
		$this->set('modified', $modified);
		if (!empty($modified)) {
			$date = new Date($modified);
			$modified = $date->format('Y-m-d');
			$filterData['modified LIKE'] = "$modified%";
		}

		$status = $this->request->getQuery('status', '');
		$this->set('status', $status);
		if ($status !== '') {
			$filterData['is_active'] = "$status";
		}
		$filterData['username != '] = "developer";
		$query = $this->Users->find('all', ['conditions' => $filterData])->order(['created' => 'DESC']);
		$users = $this->paginate($query);
		$this->set(compact('users'));
		$this->set('_serialize', ['users']);
	}

	public function login()
	{

		$now           = Time::now();
		//$now->timezone = 'Asia/Kolkata';
		$now = $now->format('l jS \of F Y h:i:s A');
		//INSERT INTO t1 (a,b) SELECT 1, MAX(b)+1 FROM t1;
		$this->viewBuilder()->setLayout('Admin/login');
		$this->set('title', 'Admin Panel');
		if ($this->request->is('post')) {
			$user = $this->Auth->identify();
			if ($user) {
				$this->Auth->setUser($user);
				$this->request->session()->write('userName', $this->Auth->user('firstname') . ' ' . $this->Auth->user('lastname'));
				$query = $this->Users->query();
				$query->update()->set(["lognum = lognum + 1, logdate = '" . date('Y-m-d h:m:s') . "'"])->where(['id' => $this->Auth->user('id')])->execute();
				$this->redirect(['controller' => '/dashboard/']);
			}
			$this->Flash->error(__('Your username or password is incorrect!'));
		}
		/*
		$date = date('Y-m-d', strtotime('-5 day'));
		$dataTable 	= TableRegistry::get('Orders');
		$order		= $dataTable->find('all', ['fields'=>['order_number'], 'conditions'=>['Orders.credit_mailer'=>1,'Orders.status'=>'delivered']])
					->where(function ($exp, $q) use($date) {
						return $exp->between('Orders.modified', "$date 00:00:01", "$date 23:59:59");
					})
					->toArray();
		if( !empty($order) ){
			foreach( $order as $value ){
				$this->Store->orderReview($value->order_number);
				$updateOrder = $dataTable->get($value->order_number);
				$updateOrder->credit_mailer	= 2;
				$dataTable->save($updateOrder);
			}
		}
		*/
		$this->set(compact('now'));
    	$this->set('_serialize', ['now']);
	}

	public function logout()
	{
		$this->Flash->success('You are now logged out!');
		return $this->redirect($this->Auth->logout());
	}

	public function forgot()
	{
		$this->viewBuilder()->setLayout('Admin/login');
		$this->set('title', 'Admin Panel');
		if ($this->request->is(['post'])) {
			$email = $this->request->getData('email');
			$user = $this->Users->findByEmail($email)->toArray();
			if (empty($user)) {
				$this->Flash->error(__('Sorry, This email id not registered!'));
			} else {
				$newPass = $this->CommonLogic->generatePassword(10); //Pass length of password
				$obj = new DefaultPasswordHasher();
				$dbPass = $obj->hash($newPass);
				$query = $this->Users->query()->update()->set(["password = '" . $dbPass . "', modified = '" . date('Y-m-d h:m:s') . "'"])->where(['email' => $email])->execute();
				if ($query) {
					$user = ['email' => $email, 'password' => $newPass, 'subject' => 'PerfumeBooth: Recover Password', 'message' => ''];
					$user = $this->getMailer('Client')->send('resetPassword', [$user]);
					//pr($user);
					if (!empty($user)) {
						$this->Flash->success(__('Password sent to your email!'));
						$this->request->getData('email', '');
					}
				} else {
					$this->Flash->error(__('Sorry, try again!'));
				}
			}
		}
	}

	public function profile()
	{
		$id = $this->Auth->user('id');
		if (empty($id)) {
			throw new NotFoundException;
		}
		$error = [];
		$user = $this->Users->get($id);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'adminProfile']);
			$error = $user->errors();
			if (empty($error)) {
				if ($this->Users->save($user)) {
					$data = $this->request->getData();
					$this->Flash->success(__('The user has been saved!'), ['key' => 'adminSuccess']);
					$this->request->session()->write('userName', $data['Users']['firstname'] . ' ' . $data['Users']['lastname']);
					return $this->redirect(['controller' => 'Users', 'action' => 'index']);
				}
			}
		}
		if (!empty($error)) {
			$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
		}

		$this->set(compact('user', 'error'));
		$this->set('_serialize', ['user', 'error']);
	}

	public function exports($ids = null)
	{
		$this->response->withDownload('exports.csv');
		$limit = $this->request->getQuery('limit', 50);
		$this->paginate = [
			'limit' => $limit, // limits the rows per page
			'maxLimit' => 2000,
		];

		$filterData = [];
		$username = $this->request->getQuery('username', '');
		$this->set('username', $username);
		if (!empty($username)) {
			$filterData['username'] = $username;
		}

		$firstname = $this->request->getQuery('firstname', '');
		$this->set('firstname', $firstname);
		if (!empty($firstname)) {
			$filterData['firstname'] = $firstname;
		}

		$lastname = $this->request->getQuery('lastname', '');
		$this->set('lastname', $lastname);
		if (!empty($lastname)) {
			$filterData['lastname'] = $lastname;
		}

		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if (!empty($email)) {
			$filterData['email'] = $email;
		}

		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if (!empty($created)) {
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$created%";
		}

		$modified = $this->request->getQuery('modified', '');
		$this->set('modified', $modified);
		if (!empty($modified)) {
			$date = new Date($modified);
			$modified = $date->format('Y-m-d');
			$filterData['modified LIKE'] = "$modified%";
		}

		$status = $this->request->getQuery('status', '');
		$this->set('status', $status);
		if ($status !== '') {
			$filterData['is_active'] = "$status";
		}
		$data = $this->Users->find('all', ['conditions' => $filterData])->order(['created' => 'DESC'])->toArray();
		$_serialize = 'data';
		$_header = ['ID', 'First Name', 'Last Name', 'User Name', 'Email', 'Created Date', 'Modified Date', 'Logdate', 'Logums'];
		$_extract = ['id', 'firstname', 'lastname', 'username', 'email', 'created', 'modified', 'logdate', 'lognum'];
		$this->set(compact('data', '_serialize', '_header', '_extract'));
		$this->viewBuilder()->setClassName('CsvView.Csv');
		return;
	}

	public function dashboard()
	{
		$date = date('Y-m-d', strtotime('-1 day'));
		$dataTable 	= TableRegistry::get('Orders');
		// $order		= $dataTable->find('all', ['fields'=>['order_number'], 'conditions'=>['Orders.credit_mailer'=>0,'Orders.status'=>'delivered']])
		// 			->where(function ($exp, $q) use($date) {
		// 				return $exp->between('Orders.modified', "$date 00:00:01", "$date 23:59:59");
		// 			})
		// 			->toArray(); //$order = [1];
		// if( !empty($order) ){
		// 	foreach( $order as $value ){
		// 		//$this->Store->orderAccountCredit(100066425);
		// 		//$updateOrder = $dataTable->get(100066425);
		// 		//$updateOrder->credit_mailer	= 1;
		// 		//$dataTable->save($updateOrder);
		// 	}
		// }
		// Profit calculate

		if ($this->request->is(['post'])) {
			$data = $this->request->getData();
			//			 print_r($data);
			//	 die;

			$validator = new Validator();
			$validator
				->add('ad_fb', 'money', ['rule' => ['money', 'left'], 'message' => 'Please enter a valid  amount.'])
				->add('ad_google', 'money', ['rule' => ['money', 'left'], 'message' => 'Please enter a valid  amount.'])

			
				->add('pro_rto', 'customrto', [
					'rule' => function ($value, $context) {

						if (!is_numeric($value))
							return "Sorry, Provisional RTO value  should numeric !";

						return ($value < 100 ? true : false);
					},
					'message' => 'Sorry, Provisional RTO  should not greater than 100! '
				])

				->add('pro_ship', 'custom', [
					'rule' => function ($value, $context) {

						if (!is_numeric($value))
							return "Sorry, Provisional Shipping value  should numeric !";

						return ($value < 100 ? true : false);
					},
					'message' => 'Sorry, Provisional Shipping  should not greater than 100! '
				]);

			$error = $validator->errors($this->request->getData());
             //print_r($error);
			if (empty($error)) {
				$frm_date = $data['frm_date'] . ' 00:00:01';
				$to_date = $data['to_date'] .  ' 23:59:59';

				//$frm_date = "2019-05-20 09:43:15";
				//$to_date = "2019-05-21 11:06:43";
				//$resultsIteratorObject = $dataTable->find()->where(['created >=' => $frm_date,'created <='=>$to_date,'credit_mailer'=>0,'status'=>'delivered'])->all();
				$profitObject = $dataTable->find()->select(['payment_amount', 'prdcts.price' ,'od.price', 'od.qty', 'od.sku_code', 'prdcts.cost_price', 'created', 'Orders.id'])
				->where(function ($exp, $q) use($frm_date,$to_date) {
									return $exp->between('Orders.created', "$frm_date 00:00:01", "$to_date 23:59:59");
								})

				->where(['status IN' => ['delivered','accepted','dispatched','intransit','rto']])
					->join([
						'u' => [
							'table' => 'order_details',
							'alias' => 'od',
							'type' => 'inner',
							'conditions' => 'od.order_id = Orders.id',
						],
						'c' => [
							'table' => 'products',
							'alias' => 'prdcts',
							'type' => 'inner',
							'conditions' => 'od.sku_code = prdcts.sku_code',
						]
					])->hydrate(false)->all();



				if ($profitObject->isEmpty()) {
					$ProfitValue = "There is no record exists here";
					$ProfitValue = <<<EOT
			<div class="alert alert-warning fade in alert-dismissible show">
 <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true" style="font-size:20px">X</span>
  </button>  $ProfitValue.
</div>
EOT;
				} else {
					$ProfitValue = 0;
					$cost_price = array();
					$actualPrice = array();

					foreach ($profitObject as $profitObjectval) {
						$arrSal[$profitObjectval['id']] = $profitObjectval['payment_amount'];
						$actualPrice[]=$profitObjectval['prdcts']['price']*$profitObjectval['od']['qty'];
						$cost_price[] = $profitObjectval['prdcts']['cost_price'] * $profitObjectval['od']['qty'];
					}
					$saleAmt         =   array_sum($arrSal);
					$cost_price_val      =  array_sum($cost_price);
					$actualPrice_val    =   array_sum($actualPrice);
					
					$pro_ship_data = (($saleAmt * $data['pro_ship']) / 100);
					$pro_rto_data = (($saleAmt * $data['pro_rto']) / 100);
					$ad_spand_fb_data = (($data['ad_fb']*106)/100);
					$ad_spand_gogl_data = $data['ad_google'];
					$gst_data = (($actualPrice_val * $data['gst']) / 100);

					$gst_rto_data = (($gst_data * $data['pro_rto']) / 100);
					$GSTNew=($saleAmt-($saleAmt/118)*100)*((100-$data['pro_rto'])/100);


				//	$ProfitValue = $saleAmt - $pro_ship_data - $pro_rto_data - $gst_data - $cost_price - $ad_spand_data;
//$ProfitValue = $saleAmt - $pro_ship_data - $pro_rto_data - $ad_spand_fb_data-$ad_spand_gogl_data-$gst_rto_data;    
                    $ProfitValue = $saleAmt-$GSTNew-$pro_rto_data-$ad_spand_fb_data-$ad_spand_gogl_data-$pro_ship_data-$cost_price_val;  
					$ProfitValue=number_format($ProfitValue,2); 
					$GSTNew=number_format($GSTNew,2);

					$ProfitValue = <<<EOT
					<div class="alert alert-success fade in alert-dismissible show" style="margin-top:18px;font-size:16px">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					   <span aria-hidden="true" style="font-size:20px">X</span>
					 </button>    <strong>Success!</strong> You have Earned Sales Profit Rs. $ProfitValue <br/>and Sales Amount:$saleAmt and GST:$GSTNew Actual cost price:$cost_price_val and  Facebook Spent:$ad_spand_fb_data and Google Spend:$ad_spand_gogl_data and RTO:$pro_rto_data and Shipping:$pro_ship_data
				   </div>
EOT;
				}
			} else {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
			//print_r($ResultprftArr);
			$this->set(compact('ProfitValue', 'error'));
			//$this->set('_serialize', ['ProfitValue', 'error']);
		} 
		//End Profit
	}
	
	public function dashboard1()
	{
		//$this->Store->orderCancelled(100076848); die; //mohd.afroj@perfumebooth.com
		$user = [
			'customerId' => 123456,
			'email' => 'mohd.afroj@perfumebooth.com',
			'subject' => 'New Account',
			'password' => '786afroj',
			'message' => "Hi Dear Mohd Afroj, your account successfully created at https://www.perfumebooth.com"
		];
		$user = $this->getMailer('Customer')->send('registerWelcome', [$user]);
		die;
	}
	public function add()
	{
		$error = [];
		$user = $this->Users->newEntity();
		if ($this->request->is('post')) {
			$user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'adminUserAdd']);
			$error = $user->getErrors();
			if (empty($error)) {
				if ($this->Users->save($user)) {
					$this->Flash->success(__('The user has been saved!'), ['key' => 'adminSuccess']);
					return $this->redirect(['controller' => 'Users', 'action' => 'index']);
				}
			}
		}
		if (!empty($error)) {
			$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
		}
		$this->set(compact('user', 'error'));
		$this->set('_serialize', ['user', 'error']);
	}

	public function edit($id = null)
	{
		try {
			if (empty($id)) {
				throw new NotFoundException("Invalid request!");
			}
			$error = [];
			$user = $this->Users->get($id);
			if ($this->request->is(['patch', 'post', 'put'])) {
				$user = $this->Users->patchEntity($user, $this->request->getData(), ['validate' => 'adminUserUpdate']);
				pr($user);
				$error = $user->errors();
				if (empty($error)) {
					if ($this->Users->save($user)) {
						$this->Flash->success(__('The user has been saved!'), ['key' => 'adminSuccess']);
						return $this->redirect(['controller' => 'Users', 'action' => 'index']);
					}
				}
			}
			if (!empty($error)) {
				$this->Flash->error(__('Sorry, there are something wrong!'), ['key' => 'adminError']);
			}
		} catch (Exception $e) {
			$this->Flash->error(__($e->getMessage()), ['key' => 'adminError']);
		}
		$this->set(compact('user', 'error'));
		$this->set('_serialize', ['user', 'error']);
	}

	public function delete($id = null)
	{
		$this->request->allowMethod(['post', 'delete']);
		$user = $this->Users->get($id);
		if ($this->Users->delete($user)) {
			$this->Flash->success(__('The user has been deleted!'), ['key' => 'adminSuccess']);
			return $this->redirect(['controller' => 'Users', 'action' => 'index']);
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again!'), ['key' => 'adminSuccess']);
		}
		return true;
	}
}
