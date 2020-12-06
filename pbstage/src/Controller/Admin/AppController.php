<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller\Admin;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Collection\Collection;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\Date;
use Cake\Validation\Validator;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
	public function initialize()
    {
        parent::initialize();
		$this->viewBuilder()->setLayout('Admin/default');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Csrf');
        $this->loadComponent('Security');
        $this->loadComponent('Admin');
		$this->loadComponent('Auth',[
        	'authenticate' => [
        		'Form' => [
        			'fields' => ['username' => 'username', 'password' => 'password']
        		]
            ],
        	'loginAction' => ['plugin'=>null, 'prefix'=>'admin', 'controller' => 'Users','action'=>'login'],
        	'unauthorizedRedirect' => $this->referer() // If unauthorized, return them to page they were just on
        ]);
        $this->Auth->allow(['forgot','logout']);
        
        // Role Management
		$userId = $this->request->session()->read('Auth.User.id');
		$userInfo = $this->Admin->getUser($userId); 
        $permission = $userInfo['permission'] ?? [];
        if( !empty($permission) ){
            $permission = json_decode($permission);
        }        
        $controllerName = $this->request->params['controller']; 
        $actionName = $this->request->params['action'];
        $accessAuth = false;
        foreach ($permission as $contrl => $method) {    
            foreach ($method as $key => $methodName) {               
                if( ( $controllerName == $contrl) && ($key == $actionName) && $methodName )  {
                    $accessAuth = true;
                    break;
                }
            }
            if( $accessAuth ) {
                break;
            }
        }
        $skippedControllers = ['Users', 'Attributes'];
        if( in_array($controllerName, $skippedControllers) || $this->request->isAjax() )  {            
        } else {
            if ( !$accessAuth ) {
                $this->Flash->error(__('Sorry, You dot not have permission to access these resources!'), ['key' => 'adminError']); 
                $this->redirect($this->referer());
            }
        }
       // End 
       
    }

    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
}
