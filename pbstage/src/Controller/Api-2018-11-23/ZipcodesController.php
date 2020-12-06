<?php
namespace App\Controller\Api;
use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\Http\ServerRequest;
use Cake\Http\ServerResponse;
//use Cake\Datasource\Exception;
use Cake\Core\Exception\Exception;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\InvalidCsrfTokenException;
class ZipcodesController extends AppController
{
	public $paginate = [
        'page' => 1,
        'limit' => 50,
        'maxLimit' => 2000,
        'fields' => [
            'id', 'zipcode', 'prepaid', 'cod','city','state'
        ],
        'sortWhitelist' => [
            'id', 'zipcode', 'prepaid', 'cod','city','state'
        ]
    ];
	
	public function beforeFilter(Event $event)
	{
		parent::beforeFilter($event);
		//$this->Security->csrfCheck = false; http://localhost:80/blog/api/zipcodes.json
		$this->eventManager()->off($this->Csrf);
		
		//echo $this->request->getParam('controller');
		/*
		$this->response->cors($this->request)
			->allowOrigin('*')
			->allowMethods(['GET','PUT','POST', 'OPTIONS'])
			->allowHeaders(['X-CSRF-Token'])
			->allowCredentials()
			//->exposeHeaders(['Link'])
			->maxAge(300)
			->build();
		*/
		//$this->response->withHeader('Access-Control-Allow-Origin', 'http://localhost:4200');			
		header('Access-Control-Allow-Origin: http://localhost:4200');
		//return 1;//$this->response;
	}
	
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
		//echo json_encode($zipcodes); die;
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
	public function getCode($code=null)
    {
		$zipcodes = [];
		$response = ['message'=>'Sorry, you don`t have permission!', 'success'=>false, 'data'=>$zipcodes];
		try{
			$filter = [];
			$filter['zipcode'] = $code;
			$zipcodes = $this->Zipcodes->find('all', ['conditions'=>$filter]);
			if( empty($zipcodes) ){
				throw new Exception(__('Sorry, Record not found!'));
			}
			$response['data'] = $this->paginate($zipcodes);
			$response['message'] = 'Record found!';	
			$response['success'] = true;	
		}catch( Exception $e ){
			$response['message'] = $e->getMessage();
		}
		echo json_encode($response); die;
    }
	

}

