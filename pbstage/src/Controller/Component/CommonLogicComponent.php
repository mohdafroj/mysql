<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * Admin component
 */
class CommonLogicComponent extends Component
{
	public function generatePassword($length = 8 ) {
       $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
       $password = substr( str_shuffle( $chars ), 0, $length );
       return $password;
    }
	
	public function getLocationDetails($location_id)
	{
		$state			= '';
		$country		= '';
		$locationTable 	= TableRegistry::get('Locations');
		$location		= $locationTable->get($location_id);
		if(count($location) > 0)
		{
			$state		= $location->title;
			if($location->parent_id > 0)
			{
				$parent_location	= $locationTable->get($location->parent_id);
				if(count($parent_location) > 0)
				{
					$location	= $parent_location->title;
				}
			}
		}
		return array('state' => $state, 'country' => $location);
	}
	
	public function getDmainEmailStatus(){
		$status = 0;
		switch($_SERVER['SERVER_NAME']){
			case 'www.perfumebooth.com': $status = 1; break;
			case 'www.perfumeoffer.com': break;
			default:
		}
		return $status;
	}

}
