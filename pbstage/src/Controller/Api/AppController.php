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
namespace App\Controller\Api;

use Cake\Controller\Controller;

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
    use \Crud\Controller\ControllerTrait;
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $allowedOrigins = ['localhost', 'https://www.perfumeoffer.com', 'http://www.perfumebooth.com'];
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
