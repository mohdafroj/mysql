<?php

namespace SubscriptionApi\Controller;
use Cake\Core\Configure;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{
    public $countryApiId = 1; //use $this->getController()->countryApiId in component
    public function initialize()
    {
        parent::initialize();
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Paginator');
        $this->response->type('application/json');
        Configure::load('SubscriptionApi.config', 'default', false);

        $allowedOrigins = ['localhost', 'https://www.perfumeoffer.com', 'https://www.perfumebooth.com'];
        $currentOrigin = $_SERVER['HTTP_HOST'];
        //pr($currentOrigin);
        if (in_array($currentOrigin, $allowedOrigins)) {
            $currentOrigin = ($currentOrigin != 'localhost') ? $currentOrigin : 'http://localhost:4200';
            header('Access-Control-Allow-Origin: ' . $currentOrigin);
        }
        //header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Accept, Content-Type, Authorization, Enctype');
        header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE');
        header('Content-Type: application/json');
        header('Accept: application/json');
    }

}
