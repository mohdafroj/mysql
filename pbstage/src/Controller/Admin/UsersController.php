<?php
namespace App\Controller\Admin;

use App\Controller\Admin\AppController;
use App\Model\Entity\Admin;
use Cake\Mailer\MailerAwareTrait;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\I18n\Time;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;

use ReflectionClass;
use ReflectionMethod;

class UsersController extends AppController
{
	use MailerAwareTrait;
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent('CommonLogic');
		$this->loadComponent('Admin');
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

		$cond = '';
		$username = $this->request->getQuery('username', '');
		$this->set('username', $username);
		if (!empty($username)) {
			$filterData['username'] = $username;
			$cond .= " and username ='$username'";
		}

		$firstname = $this->request->getQuery('firstname', '');
		$this->set('firstname', $firstname);
		if (!empty($firstname)) {
			$filterData['firstname'] = $firstname;
			$cond .= " and firstname ='$firstname'";
		}

		$lastname = $this->request->getQuery('lastname', '');
		$this->set('lastname', $lastname);
		if (!empty($lastname)) {
			$filterData['lastname'] = $lastname;
			$cond .= " and lastname ='$lastname'";
		}

		$email = $this->request->getQuery('email', '');
		$this->set('email', $email);
		if (!empty($email)) {
			$filterData['email'] = $email;
			$cond .= " and email = '$email'";
		}

		$created = $this->request->getQuery('created', '');
		$this->set('created', $created);
		if (!empty($created)) {
			$date = new Date($created);
			$created = $date->format('Y-m-d');
			$filterData['created LIKE'] = "$created%";
			$cond .= " and created LIKE '$created%'";
		}

		$modified = $this->request->getQuery('modified', '');
		$this->set('modified', $modified);
		if (!empty($modified)) {
			$date = new Date($modified);
			$modified = $date->format('Y-m-d');
			$filterData['modified LIKE'] = "$modified%";
			$cond .= " and modified LIKE '$modified'%";
		}

		$filterData['is_active'] = "active";
		$status = $this->request->getQuery('status', '');
		$this->set('status', $status);
		if ($status !== '') {
			$filterData['is_active'] = "$status";
			$cond .= " and is_active='$status'";
		}

		$sess = $this->request->session()->read('Auth');
		$UserId = $sess['User']['id'];

		$querys = "select  * from (select * from users where 1=1 $cond order by id) products_sorted, (select @pv := $UserId) initialisation
		 where find_in_set(parent_id, @pv) > 0 and @pv := concat(@pv, ',', id)";
		$conn = ConnectionManager::get('default');

		$queryer = $conn->execute($querys);
		$users = $queryer->fetchAll('assoc');


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
		$parentDetails = [];
		if(!empty($user->parent_id)){
			$parentDetails = $this->Users->get($user->parent_id);
		}

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
		$this->set(compact('parentDetails', 'error'));
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

		//to show parent user in dropdown 
		$moduleName = $this->Admin->getControllers();
		$topuser = $this->Admin->getParentChildName();
		$topuser = array_combine(array_column($topuser, 'id'), array_column($topuser, 'username'));

		$this->set(compact('user', 'topuser', 'error'));
		$this->set(compact('user', 'moduleName', 'error'));
		$this->set('_serialize', ['user', 'moduleName', 'topuser', 'error']);
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
				//	pr($user);
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

		$topuser = $this->Admin->getParentChildName();

		$topuser = array_combine(array_column($topuser, 'id'), array_column($topuser, 'username'));

		$this->set(compact('topuser', 'error'));
		$this->set(compact('user', 'error'));
		$this->set('_serialize', ['user', 'topuser', 'error']);
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

	public function permission($id = null)
	{
		//error_reporting(0);
		$error = [];
		$moduleName = $this->Admin->getControllers();
		$contrlMthod = $this->Admin->getMethodName($moduleName);
		$userData = $this->Users->get($id);
		$topuser = $this->Admin->getParentChildName();
		if (!empty($userData->parent_id)) {
			if (!in_array($id, array_column($topuser, 'id'))) {
				die('Sorry!!! Unable to access this section!...  ');
			}
		}

		if ($this->request->is(['patch', 'post', 'put'])) {
			$userpermission = json_encode($this->request->getData());
			$query = $this->Users->query();
			$query->update()->set(['permission' => $userpermission])->where(['id' => $id])->execute();
		}

		$user = $this->Users->get($id);

		if (!empty($userData->parent_id)) {
			if (!empty($this->Users->get($user->parent_id))) {

				$parentuserAccess = $this->Users->get($user->parent_id);
				$parentuserAccess = $parentuserAccess->permission ?? '';
			}
			$Prjdata =   json_decode($parentuserAccess);
		}

		$jdata   =   json_decode($user->permission);

		$finalPermission = [];
		if (!empty($Prjdata)) {
			foreach ($contrlMthod as $contrl => $method) {

				foreach ($method as $key => $methodName) {
					if ($Prjdata->$contrl->$methodName)
						$finalPermission[$contrl][$methodName] = (isset($jdata->$contrl->$methodName) && $jdata->$contrl->$methodName) ? 1 : 0;
				}
			}
		}

		$this->set(compact('user', 'error'));
		$this->set(compact('finalPermission', 'error'));
		$this->set('_serialize', ['finalPermission', 'user', 'error']);
	}

	public function manage($id = null)
	{
		$error = [];
		$moduleName = $this->Admin->getControllers();
		$contrlMthod = $this->Admin->getMethodName($moduleName);
		$userId = $this->request->session()->read('Auth.User.id');
		$userInfo = $this->Admin->getUser($userId); 
		$parentId = $userInfo['parent_id'];
		$loggedPermission = json_decode($userInfo['permission']);

		if ($this->request->is(['post', 'put'])) {
			$userpermission = json_encode($this->request->getData());
			$this->Users->query()->update()->set(['permission' => $userpermission])->where(['id' => $userId])->execute();
			$this->Flash->success(__('Permission updated!'), ['key' => 'adminSuccess']);
			$this->redirect(['action' => 'manage']);
		}

 		$finalPermission = [];
		if (empty($parentId)) {
			foreach ($contrlMthod as $contrl => $method) {
				foreach ($method as $key => $methodName) {					
					$finalPermission[$contrl][$methodName] = $loggedPermission->$contrl->$methodName ?? 0;
				}
			}
		} else {
			foreach ($contrlMthod as $contrl => $method) {
				foreach ($method as $key => $methodName) {
					if ($loggedPermission->$contrl->$methodName)
						$finalPermission[$contrl][$methodName] = 1;
					}
			}
		}
		$this->set(compact('parentId', 'error'));
		$this->set(compact('finalPermission', 'error'));
		$this->set('_serialize', ['parentId', 'finalPermission', 'error']);
	}


	public function syslog()
	{
		$filterData = [];
		$moduleName = $this->Admin->getControllers();
		$this->set('queryString', $this->request->getQueryParams());
		$limit = $this->request->getQuery('limit', 15);
		
		$this->paginate = [
			'limit' => $limit, // limits the rows per page
			'maxLimit' => 2000,
		];

		$username = $this->request->getQuery('username', '');
		$this->set('username',$username);
		if (!empty($username)) {
			$filterData['users.username'] = $username;
		}
		
		$entity = $this->request->getQuery('entity_name', '');
		$this->set('entity_name',$entity);
		if (!empty($entity)) {
			$filterData['SysLogs.entity_name'] = $entity;
		}

		$created = $this->request->getQuery('created', '');		
		
		if (!empty($created)) {
			$date = new \DateTime($created);
			$created = $date->format('Y-m-d');
			$filterData['SysLogs.created LIKE'] = "$created%";
			}

		$query=TableRegistry::get('SysLogs')->find('all', ['contain'=>['Users'], 'conditions'=>$filterData, 'order'=>['SysLogs.id' => 'DESC']]);
		
		$userNameRes=TableRegistry::get('Users')->find('all',['order'=>['username' => 'ASC']])->hydrate(0)->toArray();
		$entityRes = $this->Admin->getMethodName($moduleName);
		
		$userList    =	array_combine(array_column($userNameRes,'username'),array_column($userNameRes,'username'));
		$entityName  =	array_combine(array_keys($entityRes),array_keys($entityRes));

		$sysLogs = $this->paginate($query);

		$this->set(compact('userList'));
		$this->set(compact('entityName'));
		$this->set(compact('sysLogs'));
		$this->set('_serialize', ['sysLogs','userList','entityName']);
	}

	public function beforeFilter(Event $event)
	{
		$fields = ['spend_date'];
		$this->Security->config('unlockedFields', $fields);
	}

}
