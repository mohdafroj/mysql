<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * Admin component
 */
class CatalogComponent extends Component
{
	public function generateCoupons($qty,$length,$format=0,$prefix='',$suffix='') {
		$coupons = [];
		switch($format):
			case 1 : //Alphanumeric
				$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
				break;
			case 2://Alphabetical
				$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
				break;
			case 3://Numeric
				$chars = "0123456789";
				break;
			default:
				$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
		endswitch;
		for($i=0; $i<$qty; $i++):
			$coupons[] = $prefix. substr(str_shuffle($chars), 0, $length) .$suffix;
		endfor;
		return $coupons;
	}
}
