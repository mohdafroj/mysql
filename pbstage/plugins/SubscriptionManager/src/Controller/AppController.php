<?php

namespace SubscriptionManager\Controller;

use App\Controller\AppController as BaseController;
use Cake\Event\Event;

class AppController extends BaseController
{
    public function initialize()
    {
        parent::initialize();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth');
        $this->loadComponent('Csrf');
        $this->loadComponent('Security');
        $this->loadComponent('Paginator');
        if (!$this->Auth->user()) {
            $this->redirect(['plugin' => null, 'prefix' => 'admin', 'controller' => 'Users', 'action' => 'login']);
        }
    }

    public function beforeRender(Event $event)
    {
        $this->viewBuilder()->setLayout('default');
    }

}
