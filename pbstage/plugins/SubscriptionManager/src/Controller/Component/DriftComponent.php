<?php
namespace SubscriptionManager\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Core\Configure;

class DriftComponent extends Component
{
    private $token;
    private $base_url;
    private $basePath = '';

    public function __construct () {
        $this->basePath = PC['COMPANY']['website'];
		$this->token = PC['SENDGRID']['token'];
		$this->base_url = PC['SENDGRID']['base_url'];
    }

    public function sendToCustomers ($param) {
        $sendStatus = 0;
        try {
            $dataTable 				= TableRegistry::get('SubscriptionManager.DriftMailers');
            $customerTable 			= TableRegistry::get('SubscriptionManager.Customers');
            $orderTable 			= TableRegistry::get('SubscriptionManager.Orders');
            $productCategoryTable   = TableRegistry::get('SubscriptionManager.ProductCategories');
            $mailerId               = $param['mailer_id'] ?? 0;
            $customerId             = $param['customer_id'] ?? 0;
            $sendList				= [];
            $mailer 				= $dataTable->get($mailerId);
            $sendList['mailer_id'] 	= $mailerId;
            $sendList['sender'] 	= !empty($mailer->sender_name) ? $mailer->sender_name :'Connect';
            $sendList['sender_email'] = $mailer->sender_email;
            $sendList['receiver_email']  = $param['receiver_email'] ?? '';
            $sendList['content'] 	= $mailer->content ?? '';
            $sendList['subject'] 	= $mailer->subject ?? '';
            $sendList['utm_source'] = $mailer->utm_source ?? '';
            $sendList['utm_campaign'] = $mailer->utm_campaign ?? '';
            $sendList['utm_term']   = $mailer->utm_term ?? '';
            $sendList['utm_content']= $mailer->utm_content ?? '';
            $sendList['utm_medium'] = $mailer->utm_medium ?? '';
            $conditions 			= empty($mailer->conditions) ? '[]':json_decode($mailer->conditions, true);
            $schedule_type 			= $conditions['schedule_type'] ?? [];
            $start 					= $conditions['start'] ?? [];
            $end 					= $conditions['end'] ?? [];
            
            $schedule_type1 = $start1 = $end1 = [];
            foreach($schedule_type as $key=>$value){
                if( $value != 0 ){ $schedule_type1[$key]=$value; }
            }
            $start1 = array_intersect_key($start, $schedule_type1);
            $end1 = array_intersect_key($end, $schedule_type1);
            $conditions = [];
            foreach($schedule_type1 as $key=>$value){
                $conditions[$key] = [
                    'schedule_type'=>$schedule_type[$key],
                    'start'=>$start[$key],
                    'end'=>$end[$key]
                ];
            }
            //pr($conditions); die;
            $customersIds = [];
            foreach($conditions as $key=>$value){
                $now  			= Time::now();
                $now->timezone  = 'Asia/Kolkata';
                if( $value['schedule_type'] == 2 ){ //calculate according to hours
                    $createdTo   	= $now->modify('- '.$value['start'].' hours')->format('Y-m-d H:m:s');
                    $createdFrom 	= $now->modify('- '.$value['end'].' hours')->format('Y-m-d H:m:s');
                }else{ // calculate according to days
                    $value['end'] 	= $value['end'] - 1;
                    $createdTo   = $now->modify('- '.$value['start'].' days')->format('Y-m-d');
                    $createdFrom = $now->modify('- '.$value['end'].' days')->format('Y-m-d');
                    $createdFrom = $createdFrom.' 00:00:01';
                    $createdTo   = $createdTo.' 23:59:59';						
                }
                //echo $createdFrom.' to '.$createdTo.'<br />';
                $custIds = [];
                switch($key){
                    case 'delivered':
                        $delivered = $orderTable->find('all', ['fields'=>['id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered']]);
                        if ( $customerId > 0 ) {
                            $delivered = $delivered->where(['customer_id'=>$customerId]);
                        } else {
                            $delivered = $delivered->where(function ($exp, $q) use($createdFrom, $createdTo) {
                                return $exp->between('Orders.modified', $createdFrom, $createdTo);
                            });
                        }
                        $delivered = $delivered->contain([
                            'Customers'=>function($q){
                                return $q->select(['firstname','lastname','email','mobile','newsletter', 'valid_email']);
                            },
                            'OrderDetails'=>function($p){
                                return $p->select(['order_id','product_id','price','title','size','quantity']);
                            },
                            'Locations'=>function($q){
                                return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                            }
                        ])
                        ->hydrate(false)->toArray();
                        $status = $this->getDeliveredProducts($delivered, $sendList);
                        if ( $status ) {
                            $sendStatus = $status;
                        }
                        break;
                    case 'repeated':
                        $repeated = $orderTable->find('all',['fields'=>['id'],'group'=>['customer_id'],'conditions'=>['status'=>'delivered'], 'having'=>['count(*) >'=>1]]);
                        if ( $customerId > 0 ) {
                            $repeated = $repeated->where(['customer_id'=>$customerId]);
                        } else {
                            $repeated = $repeated->where(function ($exp, $q) use($createdFrom, $createdTo) {
                                return $exp->between('Orders.modified', $createdFrom, $createdTo);
                            });
                        }
                        $repeated = $repeated->contain([
                            'Customers'=>function($q){
                                return $q->select(['firstname','lastname','email','mobile','newsletter','valid_email']);
                            },
                            'OrderDetails'=>function($p){
                                return $p->select(['order_id','product_id','price','title','size','quantity']);
                            },
                            'Locations'=>function($q){
                                return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                            }
                        ])
                        ->hydrate(false)->toArray();
                        $status = $this->getDeliveredProducts($repeated, $sendList);
                        if ( $status ) {
                            $sendStatus = $status;
                        }
                        break;
                    case 'cart':
                        $cart  = $customerTable->find();
                        $cart  = $cart->select(['id','email', 'name'=>$cart->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
                        ->contain(['Carts'=>[
                                'fields'=>['customer_id','product_id', 'Carts.created'],
                            ]
                        ])
                        ->innerJoinWith('Carts', function($q) use ($createdFrom, $createdTo, $customerId) {
                            if ( $customerId > 0 ) {
                                $q = $q->where(['Carts.customer_id'=>$customerId]);
                            }else {
                                $q = $q->where(['Carts.created >'=>$createdFrom, 'Carts.created <'=>$createdTo]);
                            }
                            return $q;
                        })
                        ->where(['is_active'=>'active','newsletter'=>1,'valid_email'=>'1'])
                        ->group(['Customers.id'])
                        ->order(['Carts.created'=>'DESC'])
                        ->hydrate(false)->toArray();
                        $status = $this->getCartProducts($cart, $sendList);
                        if ( $status ) {
                            $sendStatus = $status;
                        }
                        break;
                    case 'member':
                        $custIds = $customerTable->find('all',['fields'=>['id'],'group'=>['Customers.id'],'conditions'=>['is_active'=>'active','newsletter'=>1,'valid_email'=>'1']]);
                        if ( $customerId > 0 ) {
                            $custIds = $custIds->where(['id'=>$customerId]);
                        } else {
                            $custIds = $custIds->innerJoinWith('Memberships')
                            ->where(function ($exp, $q) use($createdFrom, $createdTo) {
                                return $exp->between('Memberships.created', $createdFrom, $createdTo);
                            });
                        }
                        $custIds = $custIds->hydrate(false)->toArray();
                        $custIds = array_column($custIds, 'id'); //84960
                        break;
                    case 'never':
                        $custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','valid_email'=>'1']]);
                        if ( $customerId > 0 ) {
                            $custIds = $custIds->where(['id'=>$customerId]);
                        } else {
                            $custIds = $custIds->where(['logdate >'=>$createdFrom, 'logdate <'=>$createdTo]);
                        }
                        /*->where(function ($exp, $q) use($createdFrom, $createdTo) {
                            return $exp->between('logdate', $createdFrom, $createdTo);
                        })*/
                        $custIds = $custIds->hydrate(0)->toArray();
                        $custIds = array_column($custIds, 'id');
                        break;
                    case 'pending':
                        $custIds = $orderTable->find('all',['fields'=>['customer_id'],'group'=>['customer_id'], 'conditions'=>['status'=>'pending']]);
                        if ( $customerId > 0 ) {
                            $custIds = $custIds->where(['customer_id'=>$customerId]);
                        } else {
                            $custIds = $custIds->where(function ($exp, $q) use($createdFrom, $createdTo) {
                                return $exp->between('modified', $createdFrom, $createdTo);
                            });
                        } 
                        $custIds = $custIds->hydrate(false)->toArray();
                        $custIds = array_column($custIds, 'customer_id'); //2664
                        break;
                    case 'logout':
                        $custIds = $customerTable->find('all',['fields'=>['id'],'conditions'=>['is_active'=>'active','newsletter'=>1,'valid_email'=>'1']]);
                        if ( $customerId > 0 ) {
                            $custIds = $custIds->where(['id'=>$customerId]);
                        } else {
                            $custIds = $custIds->where(function ($exp, $q) use($createdFrom, $createdTo) {
                                return $exp->between('logdate', $createdFrom, $createdTo);
                            });
                        }
                        $custIds = $custIds->hydrate(false)->toArray();
                        $custIds = array_column($custIds, 'id'); //115391
                        break;
                    default:
                        if ( $key > 0 ) {
                            $pids = $productCategoryTable->find('all',['fields'=>['product_id'],'conditions'=>['category_id'=>$key],'group'=>['product_id']])->hydrate(false)->toArray();
                            $pids = array_column($pids,'product_id');
                            if( count($pids) ){
                                $custIds = $orderTable->find('all',['fields'=>['customer_id'],'conditions'=>['status'=>'delivered'],'group'=>['customer_id']]);
                                if ( $customerId > 0 ) {
                                    $custIds = $custIds->where(['customer_id'=>$customerId]);
                                } else {
                                    $custIds = $custIds->where(function ($exp, $q) use($createdFrom, $createdTo) {
                                        return $exp->between('modified', $createdFrom, $createdTo);
                                    });
                                }  
                                $custIds = $custIds->innerJoinWith('OrderDetails', function($q) use($pids){
                                    return $q->where(['product_id IN'=>$pids]);
                                })
                                ->hydrate(false)->toArray();
                                $custIds = array_column($custIds, 'customer_id');
                            }
                        }
                }
                $customersIds = array_merge($customersIds, $custIds);
                $customersIds = array_unique($customersIds);
            }

            //pr($customersIds);die;
            if( count($customersIds) ){
                $res = $customerTable->find();
                $sendList['dynamic'] = $res->select(['customerEmail'=>'email', 'customerName'=>$res->func()->concat(['firstname'=>'identifier', ' ', 'lastname'=>'identifier'])])
                       ->where(['id IN'=>$customersIds, 'newsletter'=>1, 'valid_email'=>'1'])->hydrate(0)->toArray();
                $status = $this->sendMail($sendList);
                if ( $status ) {
                    $sendStatus = $status;
                }
            }

        } catch (\Exception $e) {}
        return $sendStatus;
    }
    
    public function stats($param = [])
    {
        $response = [];
        try {
            $http = new Client();
            $result = $http->get($this->base_url . 'categories/stats', $param, ['headers' => ['Authorization' => $this->token]]);
            $response = $result->json ?? [];
        } catch (\Exception $e) {}
        return $response;
    }

    //Add customer cart products to mailer templates
    public function getCartProducts($cart, $senderList)
    {
        $totalRecord = count($cart);
        $productTable = TableRegistry::get('SubscriptionManager.Products');
        for ($i = 0; $i < $totalRecord; $i++) {
            if (!empty($cart[$i]['carts'])) {
                $productIds = array_column($cart[$i]['carts'], 'product_id');
                $query = $productTable->find('all', ['fields' => ['id', 'url_key','unit', 'size'], 'conditions' => ['id IN' => $productIds, 'is_active' => 'active']])
                    ->contain([
                        'ProductPrices'=>[
                            'queryBuilder' => function($q){
                                return $q->select(['product_id','title','price'])->where(['ProductPrices.location_id' => 1, 'ProductPrices.is_active' => 'active']);
                            }
                        ],
                        'ProductPrices.Locations' => [
                            'queryBuilder' => function ($q) {
                                return $q->select(['id', 'price_logo'=>'currency_logo'])->where(['Locations.is_active' => 'active']);
                            },
                        ],
                        'ProductImages' => [
                            'queryBuilder' => function ($q) {
                                return $q->select(['product_id', 'img_large'])->where(['ProductImages.is_large' => '1', 'ProductImages.is_active' => 'active']);
                            }
                        ],
                    ])
                    ->hydrate(0)
                    ->toArray(); //pr($query);die;
                //create store sub data
                $text = '';
                foreach ($query as $v) {
                    $image = $v['product_images'][0]['img_large'] ?? '';
                    $urlKey = PC['COMPANY']['website'].'/' . $v['url_key'];
                    $title  = $v['product_prices'][0]['title'] ?? '';
                    $price  = $v['product_prices'][0]['price'] ?? 0;
                    $logo  = $v['product_prices'][0]['price_logo'] ?? '$';
                    $text = $text . '
					<table width="100%" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
									<tr>
										<td>
											<table width="100" border="0" cellspacing="0" cellpadding="0" align="center">
												<tr>
													<td>
														<a href="' . $urlKey . '" target="_blank"><img src="' . $image . '" alt="Dreamz Woman" width="100%" /></a>
													</td>
												</tr>
											</table>
										</td>

										<td width="15">&nbsp;</td>

										<td>
											<table width="250" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td>
														<p style="font-size:14px; margin:0; font-weight:500;">
														<a style="color:#363636; text-decoration:none;" href="' . $urlKey . '" target="_blank">' . $title . '</a>
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
															Size ' . $v['size'] . ' ' . strtoupper($v['unit']) . '
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
														1 Qty
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">' .$logo. $price . '</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="15"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					';
                }
                if (!empty($text)) {
                    $senderList['dynamic'][] = [
                        'customerName' => ucwords($cart[$i]['name']),
                        'customerEmail' => $cart[$i]['email'],
                        'text' => $text,
                    ];
                }

            }
        }
        return $this->sendMail($senderList);
    }

    //Add customer orderes products to mailer templates
    public function getDeliveredProducts($delivered, $senderList)
    {
        $totalRecord = count($delivered);
        $productTable = TableRegistry::get('SubscriptionManager.Products');
        for ($i = 0; $i < $totalRecord; $i++) {
            if ( ($delivered[$i]['customer']['newsletter'] == 1) && ($delivered[$i]['customer']['valid_email'] == '1') ) {
                $text = '';
                foreach ($delivered[$i]['order_details'] as $v) {
                    $image = '';
                    $product = $productTable->get($v['product_id'], ['fields' => ['id', 'url_key'],'contain'=>['ProductImages']])->toArray();
                    foreach ( $product['product_images'] as $p ){
                        if( $p['is_large'] && ($p['is_active'] == 'active') ){
                            $image = $p['img_large']; 
                            break;
                        }
                    }
                    $urlKey = PC['COMPANY']['website'].'/' . $product['url_key'];
                    $logo  = $delivered[$i]['price_logo'] ?? '$';
                    $text = $text . '
					<table width="100%" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
									<tr>
										<td>
											<table width="100" border="0" cellspacing="0" cellpadding="0" align="center">
												<tr>
													<td>
														<a href="' . $urlKey . '" target="_blank"><img src="' . $image . '" alt="Dreamz Woman" width="100%" /></a>
													</td>
												</tr>
											</table>
										</td>

										<td width="15">&nbsp;</td>

										<td>
											<table width="250" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td>
														<p style="font-size:14px; margin:0; font-weight:500;">
															<a style="color:#363636; text-decoration:none;" href="' . $urlKey . '" target="_blank">' . $v['title'] . '</a>
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
															Size ' . $v['size'] . '
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
														' . $v['quantity'] . ' Quantity
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
															' .$logo. $v['price'] . '
															<a href="' . $urlKey . '"  style=" text-decoration:none; font-size:14px; font-weight:500; color:#f03f3f; white-space:nowrap; text-transform:uppercase; display:inline-block; float:right; padding:0 10px; text-decoration:underline;" target="_blank"> Review Now </a>
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="15"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					';
                }
                if (!empty($text)) {
                    $senderList['dynamic'][] = [
                        'customerName' => ucwords($delivered[$i]['customer']['firstname'] . ' ' . $delivered[$i]['customer']['lastname']),
                        'customerEmail' => $delivered[$i]['customer']['email'],
                        'text' => $text,
                    ];
                }
            }
        }
        return $this->sendMail($senderList);
    }

    //send mail to customer via sendgrid mail api setting
    public function sendMail($sendData = [])
    {   //pr($sendData); die;
        $sendStatus = 0;
        $http = new Client();
        $emailType = $sendData['email_type'] ?? '';
        $mailerId = $sendData['mailer_id'] ?? '0';
        $subject = $sendData['subject'] ?? 'Shop Now';
        $content = $sendData['content'] ?? '';
        $receiverEmail = $sendData['receiver_email'] ?? '';
        $senderEmail = $sendData['sender_email'] ?? PC['COMPANY']['email'];
        $senderName = $sendData['sender'] ?? PC['COMPANY']['tag'];
        $utmSource = $sendData['utm_source'] ?? '';
        $utmMedium = $sendData['utm_medium'] ?? '';
        $utmTerm = $sendData['utm_term'] ?? '';
        $utmContent = $sendData['utm_content'] ?? '';
        $utmCampaign = $sendData['utm_campaign'] ?? '';
        $dynamic = $sendData['dynamic'] ?? [];

        if (count($dynamic)) {
            $driftListTable = TableRegistry::get('SubscriptionManager.DriftMailerLists');
            $chunkDynamic = array_chunk($dynamic, PC['SENDGRID']['list']);
            $counter = count($chunkDynamic);
            for ($i = 0; $i < $counter - 1; $i++) {
                //save data into drift mailer list table
                $sendData['dynamic'] = $chunkDynamic[$i];
                $dirftList = $driftListTable->newEntity();
                $dirftList->drift_mailer_id = $mailerId;
                $dirftList->content = json_encode($sendData);
                $driftListTable->save($dirftList);
            }
            $dynamic = $chunkDynamic[$counter - 1]; // last entry of chunk array

            foreach ($dynamic as $param) {
                $email = empty($receiverEmail) ? $param['customerEmail'] : $receiverEmail;
                $name = $param['customerName'] ?? 'Customer';
                $text = $param['text'] ?? '';
                $content = str_replace("{{customerName}}", $name, $content);
                $content = str_replace("{{productList}}", $text, $content); //echo $content;die;
                $body = [
                    "personalizations" => [
                        [
                            "to" => [
                                [
                                    "email" => $email,
                                    "name" => $name,
                                ],
                            ],
                            "subject" => $subject,
                        ],
                    ],
                    "from" => [
                        "email" => $senderEmail,
                        "name" => $senderName,
                    ],
                    "reply_to" => [
                        "email" => $senderEmail,
                        "name" => $senderName,
                    ],
                    "subject" => $subject,
                    "content" => [
                        [
                            "type" => "text/html",
                            "value" => $content,
                        ],
                    ],
                    "categories" => [PC['SENDGRID']['cat'] . $mailerId],
                    "tracking_settings" => [
                        "click_tracking" => [
                            "enable" => true,
                        ],
                        "open_tracking" => [
                            "enable" => true,
                        ],
                        "ganalytics" => [
                            "enable" => true,
                            "utm_source" => $utmSource,
                            "utm_medium" => $utmMedium,
                            "utm_term" => $utmTerm,
                            "utm_content" => $utmContent,
                            "utm_campaign" => $utmCampaign,
                        ],
                    ],
                ];
                $http->post($this->base_url . 'mail/send', json_encode($body), ['headers' => ['Content-Type' => 'application/json', 'Authorization' => $this->token]]);
                $sendStatus = 1;
            }
        } else if ( $emailType == 'test' ) {
            $name = $sendData['name'] ?? '';
            $email = $sendData['email'] ?? '';
            $mailerId = 'Test';
            $body = [
                "personalizations" => [
                    [
                        "to" => [
                            [
                                "email" => $email,
                                "name" => $name,
                            ],
                        ],
                        "subject" => $subject,
                    ],
                ],
                "from" => [
                    "email" => $senderEmail,
                    "name" => $senderName,
                ],
                "reply_to" => [
                    "email" => $senderEmail,
                    "name" => $senderName,
                ],
                "subject" => $subject,
                "content" => [
                    [
                        "type" => "text/html",
                        "value" => $content,
                    ],
                ],
                "categories" => [PC['SENDGRID']['cat'] . $mailerId],
                "tracking_settings" => [
                    "click_tracking" => [
                        "enable" => true,
                    ],
                    "open_tracking" => [
                        "enable" => true,
                    ],
                    "ganalytics" => [
                        "enable" => true,
                        "utm_source" => $utmSource,
                        "utm_medium" => $utmMedium,
                        "utm_term" => $utmTerm,
                        "utm_content" => $utmContent,
                        "utm_campaign" => $utmCampaign,
                    ],
                ],
            ];
            $http->post($this->base_url . 'mail/send', json_encode($body), ['headers' => ['Content-Type' => 'application/json', 'Authorization' => $this->token]]);
            $sendStatus = 1;
        }
        return $sendStatus;
    }

    //Fetch one drift mailer list from database and send email to that customers list, delete current list
    public function sendMailToStoredList()
    {
        try {
            $driftListTable = TableRegistry::get('SubscriptionManager.DriftMailerLists');
            $query = $driftListTable->find('all', ['order' => ['id' => 'ASC'], 'limit' => 1])->hydrate(false)->toArray();
            if (isset($query[0])) {
                $entity = $driftListTable->get($query[0]['id']);
                $driftListTable->delete($entity);
                $sendData = json_decode($query[0]['content'], true);
                if ( is_array($sendData) && !empty($sendData) ) {
                    $this->sendMail($sendData);
                }
            }
        } catch (\Exception $e) {
        }
        return 1;
    }
}
