<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Controller\ComponentRegistry;
use App\Controller\Component\Customer;
use Cake\Network\Http\Client;

class SendgridComponent extends Component
{
	private $token 	  = 'Bearer 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';
	private $base_url = 'https://api.sendgrid.com/v3/';
	
	public function myTest($param=[])
	{
		$body 	  = [
			"personalizations"=>[
				[
					"to"=>[
						[
							"email"=>"mohd.afroj@gmail.com",
							"name"=>"Mohd Afroj"
						]
					],
					"subject"=>"Sendgrid Test Mailer by Web API"
				]
			],
			"content"=>[
				[
					"type"=>"text/html",
					"value"=>"Hiiii, <b>Test Code</b>"
				]
			],
			"from"=>[
				"email"=>"mohd.afroj@perfumebooth.com",
				"name"=>"Mohd Afroj Ansari"
			],
			"reply_to"=>[
				"email"=>"mohd.afroj@perfumebooth.com",
				"name"=>"Mohd Afroj Ansari"
			]

		];
		
		$http 	  = new Client();
		$result   = $http->post($this->base_url.'mail/send', json_encode($body), ['headers' => ['Content-Type'=>'application/json', 'Authorization'=>$this->token]]);
		$response = $result->json;
		return $response;
	}
	
	public function getBounces($param=[])
	{
		$body 	  = [
			
		];
		
		$http 	  = new Client();
		$result   = $http->get($this->base_url.'suppression/bounces', $body, ['headers' => ['Accept'=>'application/json', 'Authorization'=>$this->token]]);
		$response = $result->json;		
		return $response;
	}
	
	public function cancelOrder($waybill)
	{
		if($waybill != '')
		{
			$inputArray					= array();
			$inputArray['waybill']		= $waybill;
			$inputArray['cancellation']	= 'true';			
			$http 		= new Client();
			$result 	= $http->post($this->base_url.'/api/p/edit', json_encode($inputArray), ['headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json', 'Authorization'=>$this->token]]);
			$response 	= $result->json;
			return $response;
		}
		return array();
	}
}
