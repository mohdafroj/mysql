<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\I18n\Time;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

class ZipcodesController extends AppController
{
    use MailerAwareTrait;
    public $paginate = [
        'page' => 1,
        'limit' => 50,
        'maxLimit' => 2000,
        'fields' => [
            'id', 'zipcode', 'prepaid', 'cod', 'city', 'state',
        ],
        'sortWhitelist' => [
            'id', 'zipcode', 'prepaid', 'cod', 'city', 'state',
        ],
    ];
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Shipvendor');
        $this->loadComponent('Shiproket');
        $this->loadComponent('Delhivery');
        $this->loadComponent('Sms');
        $this->loadComponent('Drift');
        $this->loadComponent('Customer');
        header('Content-Type: text/html'); //
    }

    public function index()
    {$counter = 1;
        $res = [];
        $code = $this->request->getQuery('param1', 100118926);
        //$res = $this->Shiproket->sendOrderByAdmin($code, 0);
        pr($res);
        die;
    }

    public function pbdelhivery($id = 0)
    {
        $response = [];
        if ($id > 0) {
            $response = $this->Delhivery->sendOrderNew($id);
        }
        //pr($response);
        die;
    }

    public function drift($id = 0)
    {
        $response = [];
        $response = $this->Drift->getBounces($id);
        pr($response);
        die;
    }

    public function test($param = 0)
    {
        $now = Time::now();
        $now->timezone = 'Asia/Kolkata';
        $createdTo = $now->modify('- 50 days')->format('Y-m-d H:m:s');
        $createdFrom = $now->modify('- 1 days')->format('Y-m-d H:m:s');
        //echo $createdFrom .' : '. $createdTo; die;
        $orderTable = TableRegistry::get('Orders');
        $delivered = $orderTable->find('all', ['fields' => ['Orders.id'], 'group' => ['Orders.customer_id'], 'conditions' => ['Orders.status' => 'delivered']])
        //->where(function ($exp, $q) use($createdFrom, $createdTo) {
        //return $exp->between('Orders.modified', $createdFrom, $createdTo);
        //})
            ->contain([
                'Customers' => function ($q) {
                    return $q->select(['firstname', 'lastname', 'email', 'mobile']);
                },
                'OrderDetails' => function ($p) {
                    return $p->select(['order_id', 'product_id', 'price', 'title', 'size', 'qty']);
                },
            ])
            ->limit(12)
            ->hydrate(false)->toArray();
        pr($delivered);
        die;
    }

    public function view($id = null)
    {
        $zipcode = [];
        try {
            if (empty($id) || !is_numeric($id)) {
                throw new Exception(__('Invalid parameter passed!'));
            }
            $zipcode = $this->Zipcodes->get($id);
            if (empty($zipcode)) {
                throw new RecordNotFoundException();
            }
        } catch (RecordNotFoundException $e) {
            $this->message = $e->getMessage();
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
        $this->set(compact('zipcode'));
        $this->set('_serialize', ['zipcode']);
    }
    public function getCode($code = null)
    {
        $zipcodes = [];
        $response = ['message' => 'Sorry, you don`t have permission!', 'success' => false, 'data' => $zipcodes];
        try {
            $filter = [];
            $filter['zipcode'] = $code;
            $zipcodes = $this->Zipcodes->find('all', ['conditions' => $filter]);
            if (empty($zipcodes)) {
                throw new Exception(__('Sorry, Record not found!'));
            }
            $response['data'] = $this->paginate($zipcodes);
            $response['message'] = 'Record found!';
            $response['success'] = true;
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        echo json_encode($response);die;
    }

}
