<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Email;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception;

class DriftComponent extends Component
{
    //use MailerAwareTrait;
    private $token = 'Bearer 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';
    private $base_url = 'https://api.sendgrid.com/v3/';

    public function myTest($param = [])
    {
        $body = [
            "personalizations" => [
                [
                    "to" => [
                        [
                            "email" => "mohd.afroj@gmail.com",
                            "name" => "Mohd Afroj",
                        ],
                    ],
                    "subject" => "Sendgrid Test Mailer by Web API",
                ],
            ],
            "from" => [
                "email" => "connect@perfumebooth.com",
                "name" => "Mohd Afroj Ansari",
            ],
            "reply_to" => [
                "email" => "connect@perfumebooth.com",
                "name" => "Mohd Afroj Ansari",
            ],
            "subject" => "Sendgrid Test Mailer by Web API",
            "content" => [
                [
                    "type" => "text/html",
                    "value" => "Hiiii, <a href='https://www.perfumebooth.com/'>Click Here</a> <b>Test Code</b>",
                ],
            ],
            "categories" => [
                'test-123',
            ],
            "tracking_settings" => [
                "click_tracking" => [
                    "enable" => true,
                ],
                "open_tracking" => [
                    "enable" => true,
                    "substitution_tag" => "tag name",
                ],
                "ganalytics" => [
                    "enable" => true,
                    "utm_source" => "utm_source",
                    "utm_medium" => "utm_medium",
                    "utm_term" => "utm_term",
                    "utm_content" => "utm_content",
                    "utm_campaign" => "utm_campaign",
                ],
            ],
        ];

        $http = new Client();
        $result = $http->post($this->base_url . 'mail/send', json_encode($body), ['headers' => ['Content-Type' => 'application/json', 'Authorization' => $this->token]]);
        //$result   = $http->get($this->base_url.'templates', [], ['headers' => ['Content-Type'=>'application/json', 'Authorization'=>$this->token]]);
        //$result   = $http->get($this->base_url.'messages', ['limit'=>10], ['headers'=>['Authorization'=>$this->token]]);
        $response = $result->json;
        return $response;
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

    public function getBounces($param = [])
    {
        $body = [

        ];
        $http = new Client();
        $result = $http->get($this->base_url . 'suppression/bounces', $body, ['headers' => ['Accept' => 'application/json', 'Authorization' => $this->token]]);
        $response = $result->json;
        return $response;
    }

    //Add customer cart products to mailer templates
    public function getCartProducts($cart, $senderList)
    {
        $totalRecord = count($cart);
        $productTable = TableRegistry::get('Products');
        for ($i = 0; $i < $totalRecord; $i++) {
            if (!empty($cart[$i]['carts'])) {
                $productIds = array_column($cart[$i]['carts'], 'product_id');
                $query = $productTable->find('all', ['fields' => ['id', 'url_key', 'title', 'size', 'size_unit', 'price'], 'conditions' => ['id IN' => $productIds, 'is_active' => 'active']])
                    ->contain(['ProductsImages' => [
                        'queryBuilder' => function ($q) {
                            return $q->select(['product_id', 'img_large'])->where(['ProductsImages.is_large' => '1', 'ProductsImages.is_active' => 'active']);
                        },
                    ]])
                    ->hydrate(false)
                    ->toArray();
                //create store sub data
                $text = '';
                foreach ($query as $v) {
                    $image = $v['products_images'][0]['img_large'] ?? '';
                    $urlKey = 'https://www.perfumebooth.com/' . $v['url_key'];
                    $text = $text . '
					<table width="100%" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
									<tr>
										<td style="background:#fff; border-radius:50%; overflow:hidden; padding:15px;">
											<table width="90" border="0" cellspacing="0" cellpadding="0" align="center">
												<tr>
													<td align="center">
														<a href="' . $urlKey . '" target="_blank" style="text-decoration:none; padding:0; font-weight:500; font-size:11px; white-space:nowrap;"><img src="' . $image . '" alt="Dreamz Woman" width="100%" /></a>
													</td>
												</tr>
											</table>
										</td>

										<td width="15">&nbsp;</td>

										<td>
											<table width="300" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td>
														<p style="font-size:12px; margin:0; font-weight:500;">
															<a style="text-decoration:none; padding:0; font-weight:500; font-size:14px; display:inline-block; float:left;" href="' . $urlKey . '" target="_blank">' . $v['title'] . '</a>
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10" style="font-size:0;"></td>
												</tr>
												<tr>
													<td>
														<table width="100" align="left" border="0" cellpadding="0" cellspacing="0">
															<tr>
																<td>
																	<p style="font-size:14px; margin:0;">
																		Size ' . $v['size'] . ' ' . strtoupper($v['size_unit']) . '
																	</p>
																</td>
															</tr>
														</table>
														
													</td>
												</tr>
												<tr>
													<td width="100%" height="10" style="font-size:0;"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; margin:0;">
															Quantity : 1
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10" style="font-size:0;"></td>
												</tr>
												<tr>
													<td>
														<table width="100" align="left" border="0" cellpadding="0" cellspacing="0">
															<tr>
																<td>
																	<p style="font-size:14px; margin:0; font-weight:700;">
																		<img src="https://storage.googleapis.com/perfumebooth/emailer-images/symbols/rupees_gray.png" alt="rupees" width="6"> ' . $v['price'] . '
																	</p>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10" style="font-size:0;"></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td width="100%" height="25" style="font-size:0;"></td>
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
        } //pr($senderList); die;
        return $this->sendMail($senderList);
    }

    //Add customer orderes products to mailer templates
    public function getDeliveredProducts($delivered, $senderList)
    {
        $totalRecord = count($delivered);
        $productTable = TableRegistry::get('Products');
        for ($i = 0; $i < $totalRecord; $i++) {
            if ( ($delivered[$i]['customer']['newsletter'] == 1) && ($delivered[$i]['customer']['valid_email'] == '1') ) {
                $text = '';
                foreach ($delivered[$i]['order_details'] as $v) {
                    $urlKey = '';
                    $image = '';
                    $product = $productTable->get($v['product_id'], ['fields' => ['id', 'url_key'], 'contain' => ['ProductsImages']])->toArray();
                    $urlKey = 'https://www.perfumebooth.com/' . $product['url_key'];
                    foreach ($product['products_images'] as $value) {
                        if (($value['is_large'] == 1) && ($value['is_active'] == 'active')) {
                            $image = $value['img_large'];
                            break;
                        }
                    }
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
													<td width="100%" height="10" style="font-size:0;"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
															Size ' . $v['size'] . '
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10" style="font-size:0;"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
														' . $v['qty'] . ' Qty
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="10" style="font-size:0;"></td>
												</tr>
												<tr>
													<td>
														<p style="font-size:14px; color:#363636; margin:0;">
															<img src="https://storage.googleapis.com/perfumebooth/emailer-images/symbols/rupees_gray.png" alt="rupees" width="6"> ' . $v['price'] . '
															<a href="' . $urlKey . '"  style=" text-decoration:none; font-size:14px; font-weight:500; color:#f03f3f; white-space:nowrap; text-transform:uppercase; display:inline-block; float:right; padding:0 10px; text-decoration:underline;" target="_blank"> Review Now </a>
														</p>
													</td>
												</tr>
												<tr>
													<td width="100%" height="15" style="font-size:0;"></td>
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

    //send mail to customer via sendgrid mail setting
    public function sendMailOld($sendData = [])
    {
        $status = 0;
        $mailerId = $sendData['mailer_id'] ?? '0';
        $subject = $sendData['subject'] ?? 'Shop Now';
        $htmlCode = $sendData['content'] ?? '';
        $senderEmail = !empty($sendData['sender_email']) ? $sendData['sender_email'] : 'connect@perfumebooth.com';
        $senderName = $sendData['sender'] ?? 'PerfumeBooth';
        $utmSource = $sendData['utm_source'] ?? '';
        $utmMedium = $sendData['utm_medium'] ?? '';
        $utmTerm = $sendData['utm_term'] ?? '';
        $utmContent = $sendData['utm_content'] ?? '';
        $utmCampaign = $sendData['utm_campaign'] ?? '';
        $dynamic = $sendData['dynamic'] ?? [];

        if (count($dynamic)) {
            foreach ($dynamic as $param) {
                $email = $param['customerEmail'] ?? 'mohd.afroj@perfumebooth.com';
                $name = $param['customerName'] ?? 'Customer';
                $text = $param['text'] ?? '';
                $content = str_replace("{{customerName}}", $name, $htmlCode);
                $content = str_replace("{{productList}}", $text, $content); //echo $content;die;
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailObj = new Email('Sendgrid');
                    $emailObj->from([$senderEmail => $senderName])->to([$email => $name])->subject($subject)->emailFormat('html')->send($content);
                    $status = 1;
                }
                //die;
            }
        }
        return $status;
    }

    //send mail to customer via sendgrid mail api setting
    public function sendMail($sendData = [])
    {
        $status = 0;
        $sizeOfList = 2000;
        $http = new Client();
        $mailerId = $sendData['mailer_id'] ?? 0;
        $subject = $sendData['subject'] ?? 'Shop Now';
        $htmlCode = $sendData['content'] ?? '';
        $senderEmail = !empty($sendData['sender_email']) ? $sendData['sender_email'] : 'connect@perfumebooth.com';
        $senderName = $sendData['sender'] ?? 'PerfumeBooth';
        $utmSource = $sendData['utm_source'] ?? '';
        $utmMedium = $sendData['utm_medium'] ?? '';
        $utmTerm = $sendData['utm_term'] ?? '';
        $utmContent = $sendData['utm_content'] ?? '';
        $utmCampaign = $sendData['utm_campaign'] ?? '';
        $dynamic = $sendData['dynamic'] ?? [];

        //add record to testing data
        //$query = TableRegistry::get('TempDatas')->query()->insert(['name','email']);
        //foreach ($dynamic as $param) { $query = $query->values(['name'=>$param['customerName'] ?? 'Customer', 'email'=>$param['customerEmail']]); }
        //$query->execute();
        //return 1;

        if ( count($dynamic) ) { //&& ( PB_BANNER_STATUS != 1 ) 
            $driftListTable = TableRegistry::get('DriftMailerLists');
            $chunkDynamic = array_chunk($dynamic, $sizeOfList);  
            $counter = count($chunkDynamic);
            for ($i = 0; $i < $counter - 1; $i++) {
                //save data into drift mailer list table
                $sendData['dynamic'] = $chunkDynamic[$i];
                $dirftList = $driftListTable->newEntity();
                $dirftList->drift_mailer_id = $mailerId;
                $dirftList->size_of_list = $sizeOfList;
                $dirftList->content = json_encode($sendData);
                $driftListTable->save($dirftList);
            }
            $dynamic = $chunkDynamic[$counter - 1]; // last entry of chunk array
            try {
                foreach ($dynamic as $param) {
                    $status = 1;
                    $email = $param['customerEmail'];
                    $name = $param['customerName'] ?? 'Customer';
                    $text = $param['text'] ?? '';
                    $content = str_replace("{{customerName}}", $name, $htmlCode);
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
                        "categories" => ['PB-' . $mailerId],
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
                    if( in_array($this->request->host(), ['www.perfumebooth.com']) ){ $http->post($this->base_url . 'mail/send', json_encode($body), ['headers' => ['Content-Type' => 'application/json', 'Authorization' => $this->token]]); }
                }
            } catch (\Exception $e) {}
            //pr($dynamic);
        }
        return 1;
    }

    //Fetch one drift mailer list from database and send email to that customers list, delete current list
    public function sendMailToStoredList()
    {
        $status = 0;
        try {
            $driftListTable = TableRegistry::get('DriftMailerLists');
            $query = $driftListTable->find('all', ['order' => ['id' => 'ASC'], 'limit' => 1])->hydrate(false)->toArray();
            if (isset($query[0])) {
                $entity = $driftListTable->get($query[0]['id']);
                $driftListTable->delete($entity);
                //pr($sendData);die;
                $sendData = json_decode($query[0]['content'], true);
                if (is_array($sendData)) {
                    $status = 1;
                    $this->sendMail($sendData);
                }
            }
        } catch (\Exception $e) {
        }
        return $status;
    }

    //Email verifier function
    public function checkEmail($email) {
        $response = 0;
        $http = new Client();
        try {
            $params = ['email'=>$email, 'timeout'=>SYS['EMAILV']['timeout'], 'api'=>SYS['EMAILV']['token']];
            $res = $http->get(SYS['EMAILV']['base_url'], $params, [ 'headers'=>['Content-Type'=>'application/json']]);
            $res = $res->json ?? [];
            $credit = $res['credits'] ?? 0;
            $response = $res['resultcode'] ?? 0;
            /*switch($res['resultcode']) {
                case 1:
                    echo "Ok";
                    break;
                case 2:
                    echo "Catch All";
                    break;
                case 3:
                    echo "Unknown";
                    break;
                case 4:
                    echo "Error: ".$j->error;
                    break;
                case 5:
                    echo "Disposable";
                    break;
                case 6:
                    echo "Invlaid";
                    break;
            }*/
        }catch( \Exception $ex ) {}
        return $response;
    }
}
