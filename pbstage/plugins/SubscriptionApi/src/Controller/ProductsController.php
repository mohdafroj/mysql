<?php
namespace SubscriptionApi\Controller;

use SubscriptionApi\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\I18n\Time;

class ProductsController extends AppController
{
    private $pack1Price = 599;
    private $pack2Price = 799;
    private $pack3Price = 1599;
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('SubscriptionApi.Product');
        $this->loadComponent('SubscriptionApi.Store');
        $this->loadComponent('SubscriptionApi.Customer');
    }
    public function index()
    {   
        $this->response->type('text/html');
        $products = TableRegistry::get('SubscriptionApi.Brands')->getWebBrands();
        $this->set('products', $products);
    }

	/***** This is SubscriptionApi Plugin that handle notify me action *****/
    public function notifyMe()
    {
        $response = $data = [];
        $status = 0;
        $message = '';
        $action = 1;
        if ($this->request->is(['post'])) {
            $email = $this->request->getData('email');
            $productId = $this->request->getData('productId');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Sorry, Please enter valid email id!';
                $action = 0;
            }
            if (!is_numeric($productId)) {
                $message = 'Sorry, Product Id should not valid!';
                $action = 0;
            }

            if ($action) {
                $notifyMeTable = TableRegistry::get('SubscriptionApi.NotifyMe');
                $query = $notifyMeTable->find("all", ['conditions' => ['email' => $email, 'product_id' => $productId]])->toArray();
                $message = 'Your request successfully saved!';
                $status = 1;
                if (empty($query)) {
                    $newData = $notifyMeTable->newEntity();
                    $newData->email = $email;
                    $newData->product_id = $productId;
                    if (!$notifyMeTable->save($newData)) {
                        $status = 0;
                        $message = 'Sorry, Please try again!';
                    }
                } else {
                    if (!$query[0]['status']) {
                        $row = $notifyMeTable->get($query[0]['id']);
                        $row->status = 1;
                        $data = $notifyMeTable->save($row);
                    }
                }
                $response = ['message' => $message, 'status' => $status, 'data' => $data];
            } else {
                $this->response->statusCode(400);
                $response['message'] = $message;
            }
        } else {
            if ($this->request->is(['options'])) {
            } else {
                $response['message'] = 'Sorry, request not found!';
                $this->response->statusCode(404);
            }
        }
        $this->response->send();
        echo json_encode($response);
        die;
    }

    /***** This is SubscriptionApi Plugin that get data of seleted item *****/
    public function getDetails()
    {
        $data = $param = [];
        $status = false;
        $message = '';
        if ($this->request->is('get')) {
            $param['urlKey'] = $this->request->getQuery('key', '');
            $param['userId'] = $this->request->getQuery('userId', 0);
            $data = $this->Product->getDetails($param);
            if (isset($data['id'])) {
                $message = 'Record found!';
                $status = true;
            } else {
                $message = 'Sorry, this product not found!';
            }
        } else {
            $message = 'Sorry, requested method not allow!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);die;
    }

		/***** This is SubscriptionApi Plugin that get reviews of a product *****/
    public function getReviews()
    {
        $data = $param = [];
        $status = false;
        $message = '';
        if ($this->request->is('get')) {
            $limit = $this->request->getQuery('limit', 10);
            $productId = $this->request->getQuery('productId', 0);
            $page = $this->request->getQuery('page', 1);

            $reviews = $this->Product->getProductReviews($productId, $limit, $page);
            $total = sizeof($reviews);
            if ($total > 0) {
                $message = "Total $total records found!";
            } else {
                $message = 'Sorry, reviews not found!';
            }
            $status = true;
            $data['reviews'] = $reviews;
            $data['pager'] = $page;
            $data['total'] = $total;
            $data['viewMore'] = ($total == $limit) ? 1 : 0;
        } else {
            $message = 'Sorry, requested method not allow!';
        }
        $response = ['message' => $message, 'status' => $status, 'data' => $data];
        echo json_encode($response);die;
    }

	/***** This is SubscriptionApi Plugin that get filtered products *****/
    public function getProducts()
    {
        $data = $products = [];
        $filter = [];
        $products = $this->Product->getAllProducts($filter);
        $message = count($products) . ', record found!';
        $success = 1;
        $data['products'] = $products;
        $products1 = $products2 = [];
        foreach ( $products as $value ) {
            if ( in_array($value['id'], $this->Product->perfumes1) ) {
                array_push($products1, ['id'=>$value['id'], 'title' =>$value['title'], 'image' => $value['images'][0]['small']]);
            } 
            if ( in_array($value['id'], $this->Product->perfumes2) ) {
                array_push($products2, ['id'=>$value['id'], 'title' =>$value['title'], 'image' => $value['images'][0]['small']]);
            } 
        }
        $data['package'] = $this->Product->getPackages(['pack1'=>$products1, 'pack2'=>$products2]);
        //pr($products); die;
        $response = ['message' => $message, 'status' => $success, 'data' => $data];
        echo json_encode($response);die;
    }

    public function getRedeemProducts()
    {
        $data = $products = [];
        $filter = [];
		$userId = $this->request->getQuery('userId', '0');
        $products = $this->Product->getAllProducts($filter);
        $message = count($products) . ', record found!';
        $success = 1;
        $data['products'] = $products;
        $products1 = $products2 = [];
        foreach ( $products as $value ) {
            if ( in_array($value['id'], $this->Product->perfumes1) ) {
                array_push($products1, ['id'=>$value['id'], 'title' =>$value['title'], 'image' => $value['images'][0]['small']]);
            } 
            if ( in_array($value['id'], $this->Product->perfumes2) ) {
                array_push($products2, ['id'=>$value['id'], 'title' =>$value['title'], 'image' => $value['images'][0]['small']]);
            } 
        }
        $redeemQuantity = $this->Customer->getReferEarn($userId);
        $redeemQuantity = $redeemQuantity['refer']['earned'] ?? [];
        $data['redeemQuantity'] = count($redeemQuantity);
        $response = ['message' => $message, 'status' => $success, 'data' => $data];
        echo json_encode($response);die;
    }

    public function getScentMatch() {
        $this->loadComponent('SubscriptionApi.Algo');
        $data = $products = $filter = [];
        $filter['gender'] = $this->request->getQuery('gender', '');
        $filter['brandId'] = $this->request->getQuery('brand', '');
        $filter['families'] = $this->request->getQuery('families', '');
        $filter['favoritePerfumes'] = $this->request->getQuery('favoritePerfumes', '');
        if ( empty($filter['favoritePerfumes']) ) {
            $products = $this->Product->getScentMatch($filter);
            $data['family'] = TableRegistry::get('SubscriptionApi.Families')->getFamilies();
            $data['brands'] = TableRegistry::get('SubscriptionApi.Brands')->getWebBrands();
        } else {
            $filter['families'] = explode(',', $filter['families']);
            $filter['favoritePerfumes'] = explode(',', $filter['favoritePerfumes']);
            $products = $this->Algo->searchProducts($filter);
        }
        //pr($products);die;
        $message = count($products) . ', record found!';
        $data['products'] = $products;
        $response = ['message' => $message, 'status' => 1, 'data' => $data];
        echo json_encode($response);die;
    }

    public function getAlgoProducts () {
        $limit  = 30;
        $search = $this->request->getQuery('search', '');
        $data   = TableRegistry::get('SubscriptionApi.AlgoProducts')->find('all', ['fields' => ['id', 'name'=>'product_name']])->order(['product_name'=>'asc']);
        if ( !empty($search) ) {
            $data = $data->where(["product_name REGEXP '($search)'"]);
        }
        $data = $data->limit($limit)->hydrate(0)->toArray();
        $response = ['message' => '', 'status' => 1, 'data' => $data];
        echo json_encode($response);die;
    }

    public function getProductNew() {
        $data = $productsPack1 = $productsPack2 = $productsPack3 = $filter = [];
        $totalProducts = $this->Product->getHashProduct($filter);
        $message = count($totalProducts) . ', record found!';
        $success = 1;
        foreach ($totalProducts as $item ) {
            if( in_array(4, array_column($item['categories'], 'id')) ) { //For pack - 1
                $productsPack1[] = $item;
            }
            if ( in_array(8, array_column($item['categories'], 'id')) ) { //For pack - 2
                $productsPack2[] = $item;
            }
            if ( in_array(9, array_column($item['categories'], 'id')) ) { //For pack - 3
                $productsPack3[] = $item;
            }
        }

        $data['pack1Price'] = $this->pack1Price;
        $data['pack2Price'] = $this->pack2Price;
        $data['pack3Price'] = $this->pack3Price;
        $data['productsPack1'] = $productsPack1;
        $data['productsPack2'] = $productsPack2;
        $data['productsPack3'] = $productsPack3;

        $data['meta'] = [
            'title' => 'Online Perfume Store India | Branded Perfumes Online',
            'keywords' => 'Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men , Perfumes, Fragrances',
            'description' =>'View your own choice perfume on online perfume store in India and shop now. We offer doorstep delivery in  India.'
        ];

        $data['marquee'] = '20% Cash Back (All Order) + Free shipping (Prepaid order)';
        $response = ['message' => $message, 'status' => $success, 'data' => $data];
        echo json_encode($response);die;
    }

    public function getProductBuynow() {
        $data = $productsPack1 = $productsPack2 = $productsPack3 = $filter = [];
        $filter['coupon'] = $this->request->getQuery('offerCoupon', ''); //ROSHNI20
        $totalProducts = $this->Product->getHashProduct($filter);
        $message = count($totalProducts) . ', record found!';
        $success = 1;
        foreach ($totalProducts as $item ) {
            $categories = array_column($item['categories'], 'id');
            if( in_array(4, $categories) ) { //For pack - 1                
                $productsPack1[] = $item;
            }
            if ( in_array(8, $categories) ) { //For pack - 2
                $productsPack2[] = $item;
            }
            if ( in_array(9, $categories) ) { //For pack - 3
                $productsPack3[] = $item;
            }
        }
        //die;
        $data['pack1Price'] = $this->pack1Price;
        $data['pack2Price'] = $this->pack2Price;
        $data['pack3Price'] = $this->pack3Price;
        $data['productsPack1'] = $productsPack1;
        $data['productsPack2'] = $productsPack2;
        $data['productsPack3'] = $productsPack3;

        $data['meta'] = [
            'title' => 'Online Perfume Store India | Branded Perfumes Online',
            'keywords' => 'Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men , Perfumes, Fragrances',
            'description' =>'View your own choice perfume on online perfume store in India and shop now. We offer doorstep delivery in  India.'
        ];

        $data['marquee'] = '20% Cash Back (All Order) + Free shipping (Prepaid order)';
        $response = ['message' => $message, 'status' => $success, 'data' => $data];
        echo json_encode($response);die;
    }

    public function getLaunchOffer() {
        $data = [];
        $page = $this->request->getQuery('page', '');
        $offerDuration = 0;
        $setTimeinSeconds = 1500;
        $offerDuration = 3600; //strtotime("+3 days 7 hours 5 seconds") - strtotime("now");
        //die($offerDuration);
        switch ( $page ){
            case 'launchoffer':
                $data['offerCoupon'] = 'GETFOR399'; //'ROSHNI20';
                $data['maxDiscount'] = 'Upto 45% Off';
                $data['offerDuration'] = $offerDuration;
                $data['pack1'] = ['cross'=>599, 'price'=>399, 'discount'=>'33% OFF'];
                $data['pack2'] = ['cross'=>1199, 'price'=>699, 'discount'=>'42% OFF'];
                $data['pack3'] = ['cross'=>1799, 'price'=>999, 'discount'=>'45% OFF'];
                break;
            default:
        }

        $data['meta'] = [
            'title' => 'Online Perfume Store India | Branded Perfumes Online',
            'keywords' => 'Online Perfume Store India, Perfume Online, Branded Perfumes Online, Top Indian Perfume, Premium Perfume, Luxury Perfume, Indian Perfume Brands, Perfume for Girls, Perfume for Boys, Perfume for Women, Perfume for Men , Perfumes, Fragrances',
            'description' =>'View your own choice perfume on online perfume store in India and shop now. We offer doorstep delivery in  India.'
        ];

        $response = ['message' => '', 'status' => 1, 'data' => $data];
        echo json_encode($response);die;
    }


}
