<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\Core\Exception\Exception;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\InvalidCsrfTokenException;
class ZipcodesController extends AppController
{
	use \Crud\Controller\ControllerTrait;

	public $paginate = [
        'page' => 1,
        'limit' => 10,
        'maxLimit' => 2000,
        'fields' => [
            'id', 'zipcode', 'prepaid', 'cod','city','state','created','modified'
        ],
        'sortWhitelist' => [
            'id', 'zipcode', 'prepaid', 'cod','city','state','created','modified'
        ]
    ];
	
	public function index()
    {
		$zipcodes = [];
		try{
			$zipcodes = $this->Zipcodes->find('all');
			if( empty($zipcodes) ){
				throw new Exception(__('Sorry, Record not found!'));
			}
			$zipcodes= $this->paginate($zipcodes);
		}catch( Exception $e ){
			$this->message = $e->getMessage();
		}
		$this->set(compact('zipcodes'));
		$this->set('_serialize', ['zipcodes']);
    }
	
	public function view($id=null)
    {	
		$zipcode = [];
		try{
				if( empty($id) || !is_numeric($id) ){
					throw new Exception(__('Invalid parameter passed!'));
				}
				$zipcode = $this->Zipcodes->get($id);
				if( empty($zipcode) ){
					throw new RecordNotFoundException();
				}
		}catch( RecordNotFoundException $e ){
			$this->message = $e->getMessage();
		}catch( Exception $e ){
			$this->message = $e->getMessage();
		}
		$this->set(compact('zipcode'));
		$this->set('_serialize', ['zipcode']);
    }	
}

