<?php
namespace SubscriptionApi\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Datasource\Exception;


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
    
    public function getCartProducts($cart, $senderList)
    {
        $totalRecord = count($cart);
        $productTable = TableRegistry::get('SubscriptionApi.Products');
        for ($i = 0; $i < $totalRecord; $i++) {
            if (!empty($cart[$i]['carts'])) {
                $productIds = array_column($cart[$i]['carts'], 'product_id');
                $query = $productTable->find('all', ['fields' => ['id', 'url_key','unit', 'size'], 'conditions' => ['id IN' => $productIds, 'is_active' => 'active']])
                    ->contain([
                        'ProductPrices'=>[
                            'queryBuilder' => function($q){
                                return $q->select(['product_id','title','price'])->where(['ProductPrices.location_id' => Configure::read('countryApiId'), 'ProductPrices.is_active' => 'active']);
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
                    $title  = $v['product_prices'][0]['title'] ?? '';
                    $price  = $v['product_prices'][0]['price'] ?? 0;
                    $logo  = $v['product_prices'][0]['price_logo'] ?? 'INR';
                    $forRow = [];
                    $forRow[] = ['urlKey'=>$v['url_key'], 'src'=>$image, 'title'=>$title, 'size'=>$v['size']. ' ' . strtoupper($v['unit']), 'quantity'=>1, 'price'=>$logo.$price];
                    $text .= $this->getProductRow($forRow);
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
        $productTable = TableRegistry::get('SubscriptionApi.Products');
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
                    $logo  = $delivered[$i]['price_logo'] ?? 'INR';
                    $forRow = [];
                    $forRow[] = ['urlKey'=>$product['url_key'], 'src'=>$image, 'title'=>$v['title'], 'size'=>$v['size'], 'quantity'=>$v['quantity'], 'price'=>$logo.$v['price']];
                    $text .= $this->getProductRow($forRow);
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
    {
        $http = new Client();
        $mailerId = $sendData['mailer_id'] ?? '0';
        $subject = $sendData['subject'] ?? 'Shop Now';
        $htmlCode = $sendData['content'] ?? '';
        $senderEmail = !empty($sendData['senderEmail']) ? $sendData['senderEmail'] : PC['COMPANY']['email'];
        $senderName = $sendData['sender'] ?? PC['COMPANY']['tag'];
        $utmSource = $sendData['utm_source'] ?? '';
        $utmMedium = $sendData['utm_medium'] ?? '';
        $utmTerm = $sendData['utm_term'] ?? '';
        $utmContent = $sendData['utm_content'] ?? '';
        $utmCampaign = $sendData['utm_campaign'] ?? '';
        $dynamic = $sendData['dynamic'] ?? [];
        
        if (count($dynamic)) {
            $driftListTable = TableRegistry::get('SubscriptionApi.DriftMailerLists');
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
                $email = $param['customerEmail'];
                $name = $param['customerName'] ?? 'Customer';
                $text = $param['text'] ?? '';
                $content = str_replace("{{customerName}}", $name, $htmlCode);
                $content = str_replace("{{productList}}", $text, $content); //echo $content; die;
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
            }
        }
        return 1;
    }

    //Fetch one drift mailer list from database and send email to that customers list, delete current list
    public function sendMailToStoredList()
    {
        try {
            $driftListTable = TableRegistry::get('SubscriptionApi.DriftMailerLists');
            $query = $driftListTable->find('all', ['order' => ['id' => 'ASC'], 'limit' => 1])->hydrate(false)->toArray();
            if (isset($query[0])) {
                $entity = $driftListTable->get($query[0]['id']);
                $driftListTable->delete($entity);
                $sendData = json_decode($query[0]['content'], true);
                if ( is_array($sendData) && !empty($sendData) ) {
                    $this->sendMail($sendData);
                }
            }
        } catch (\Exception $e) {}
        return 1;
    }

    private function getProductRow( $row ) {
        $text  = '';
        foreach ($row as $value) {
            $text = 
            '<table width="100%" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                            <tr>
                                <td>
                                    <table width="100" border="0" cellspacing="0" cellpadding="0" align="center">
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <img src="'.$value['src'].'" alt="'.$value['title'].'" width="100%" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                    </table>
                                </td>
                                
                                <td style="border-left:1px solid #ccc;" width="15">&nbsp;</td>
                                
                                <td>
                                    
                                    <table width="250" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p style="font-size:14px; color:#3b4e76; margin:0; font-weight:700;">
                                                    '.$value['title'].'
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td>
                                                            <p style="font-size:13px; color:#363636; margin:0;">
                                                                Size '.$value['size'].'
                                                            </p>
                                                        </td>
                                                        <td align="right">';
                                                    if ( isset($value['quantity']) ) {
                                                        $text .= '<p style="font-size:13px; color:#363636; margin:0; font-style:italic;">
                                                                    '.$value['quantity'].' Qty
                                                                </p>';
                                                    }
                                                $text .='</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p style="font-size:14px; color:#363636; margin:0; font-weight:700;">
                                                    '.$value['price'].'
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="7" style="font-size:0;"></td>
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
        return $text;   
    }

}
