<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * Admin component
 */
class AdminComponent extends Component
{
	protected $selectOptions = ['50'=>'50','100'=>'100','200'=>'200','500'=>'500','7000'=>'7000','1000'=>'1000','1500'=>'1500','2000'=>'2000'];
	
	protected $adminAuth = [
			'authenticate' =>[
				'Form' => [
					'fields' => ['username' => 'username', 'password' => 'password']
				]
			],
			'loginAction' => ['controller' => 'Users',	'action' => 'login'],
			'unauthorizedRedirect' => $this->referer() // If unauthorized, return them to page they were just on
		];
		
}
