<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Datasource\ConnectionManager;
use ReflectionClass;
use ReflectionMethod;


/**
 * Admin component
 */
class AdminComponent extends Component
{
	//protected $selectOptions = ['50'=>'50','100'=>'100','200'=>'200','500'=>'500','7000'=>'7000','1000'=>'1000','1500'=>'1500','2000'=>'2000'];

	//protected $adminAuth = ['authenticate' =>[ 'Form' => [ 'fields' => ['username' => 'username', 'password' => 'password'] ] ], 'loginAction' => ['controller' => 'Users',	'action' => 'login'], 'unauthorizedRedirect' => $this->referer() ];

	public function getUser($userId) {
		$user = [];
		if( $userId ) {
			$user = TableRegistry::get('Users')->get($userId)->toArray();
		}
		return $user;
	}
	
	public function getControllers()
	{
		$folder = new Folder(ROOT);
		$controllerFiles = $folder->cd('src' . DS . 'Controller' . DS . 'Admin');
		$controllerFiles = $folder->find('.*Controller.php', true);
		$skipControllers = ['AppController.php', 'UsersController.php', 'AttributesController.php'];
		$cleanControllers = [];
		foreach ($controllerFiles as $key => $controller) {
			if (!in_array($controller, $skipControllers)) {
				$cleanControllers[] = str_replace('Controller.php', '', $controller);
			}
		}
		return $cleanControllers;
	}
	public function getMethodName($moduleName = null)
	{
		foreach ($moduleName as $controllerName) {
			$className = 'App\\Controller\\Admin\\' . $controllerName . 'Controller';
			$class = new ReflectionClass($className);
			$actions = $class->getMethods(ReflectionMethod::IS_PUBLIC);
			$results[$controllerName] = [];
			$ignoreList = ['beforeFilter', 'afterFilter', 'initialize'];
			foreach ($actions as $action) {
				if ($action->class == $className && !in_array($action->name, $ignoreList)) {
					array_push($results[$controllerName], $action->name);
				}
			}
		}
		return $results;
	}

	

	public function getParentChildName()
	{
		$Table = TableRegistry::get('users');
		$sess = $this->request->session()->read('Auth');
		$UserId = $sess['User']['id'];
		$querys = "select  * from (select * from users order by id) products_sorted, (select @pv := $UserId) initialisation
		where (find_in_set(parent_id, @pv) > 0 or find_in_set(id, @pv) > 0 ) and @pv := concat(@pv, ',', id)";
		$conn = ConnectionManager::get('default');
		$queryer = $conn->execute($querys);
		$topuser = $queryer->fetchAll('assoc');
		return $topuser;
	}
}
