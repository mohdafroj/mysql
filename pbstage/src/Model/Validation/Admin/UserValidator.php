<?php
namespace App\Model\Validation\Admin;

use Cake\Validation\Validator;

class UserValidator extends Validator
{
	public function __construct()
	{
		parent::__construct();
		// Add validation rules here.
		return $validator
		->notEmpty('username', 'A username is required');
	}
	
	public function updateProfile($validator)
	{
		return $validator
		->notEmpty('firstname', 'First Name is required')
		->notEmpty('username', 'A username is required');
	}
}