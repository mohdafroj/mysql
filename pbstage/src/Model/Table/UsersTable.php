<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
		//$this->addBehavior('Tree');
		$this->addBehavior('Timestamp');
		$this->addBehavior('Auditable');
        $this->belongsTo('ParentUsers', [
            'className' => 'Users',
            'foreignKey' => 'parent_id'
        ]);
        
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
    	$validator
    	->requirePresence('firstname',['create', 'update'])
    	->notEmpty('firstname', 'First name is required!')
    	->add('firstname', 'notEmpty', ['rule' => 'notEmpty', 'message' => 'First name required!']);
    	
    	$validator
    	->requirePresence('lastname',['create', 'update'])
    	->notEmpty('lastname', 'Last name is required!');
    	
    	$validator
    	->requirePresence('email',['create', 'update'])
    	->email('email')
    	->notEmpty('email', 'Email is required!');
    	
    	$validator
    	->requirePresence('username',['create', 'update'])
    	->notEmpty('username', 'Username is required!');
    	
    	$validator
    	->requirePresence('password', [ 'create', 'update'])
    	->allowEmpty('password');
    	
    	$validator
    	->integer('is_active')
    	->requirePresence('is_active', [ 'create', 'update'])
    	->notEmpty('is_active');
    	
    	return $validator;
    }
    
    static function validationAdminUserAdd($validator)
    {
    	$validator
    	->notEmpty('firstname',  __('Please enter first name!'))
    	->notEmpty('lastname',  __('Please enter last name!'))
    	->notEmpty('username',  __('Please enter user name!'))
    	->add('username', '_empty', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('Username already exist, Please enter another one!')])
    	->notEmpty('email',  __('Please enter a email id!'))
    	->add('email', [
    			'valid' => [
    					'rule' => 'email',
    					'message' => 'Please enter a valid email id!',
    			],
    			'_empty' => [
    					'message' => 'The email already taken, Please enter another one!',
    					'rule' => 'validateUnique',
    					'provider' => 'table'
    			]
    	])
    	->notEmpty('password')
    	->add('password', [
    			'match' => ['rule' => ['compareWith', 'confirm_password'],
    					'message' => 'Confirm password should not match!'
    			]
    	])
    	->inList('is_active', ['active','inactive']);
    	return $validator;
    }
    
    static function validationAdminProfile($validator)
    {
    	$validator
    	->notEmpty('firstname',  __('First name should not be empty!'))
    	->notEmpty('lastname',  __('Last name should not be empty!'))
    	->notEmpty('username',  __('User name should not be empty!'))
    	->add('username', '_empty', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('Username already exist!')])
    	->notEmpty('email', 'validFormat', ['rule'=>'email', 'message' => __('Please enter a valid email id!')])
    	->allowEmpty('password')
    	->add('password', [
    			'match' => ['rule' => ['compareWith', 'confirm_password'],
    			'message' => 'Confirm password should not match!'
    			]
    	])
    	->inList('is_active', ['active','inactive'])
    	->add('current_password','custom',[
    		'rule'=>  function($value, $context){
    		$user = $this->get($this->Auth->user('id'));
    			if ($user) {
    				if ((new DefaultPasswordHasher)->check($value, $user->password)) {
    					return true;
    				}
    			}
    			return false;
    			},
    		'message'=>'The current password does not match!',
    	]);
    	//->notEmpty('current_password');
    	return $validator;
    }
    
    static function validationAdminUserUpdate($validator)
    {
    	$validator
    	->notEmpty('firstname',  __('Please enter first name!'))
    	->notEmpty('lastname',  __('Please enter last name!'))
    	->notEmpty('username',  __('Please enter user name!'))
    	->add('username', '_empty', ['rule' => 'validateUnique', 'provider' => 'table', 'message' => __('Username already exist, Please enter another one!')])
    	->notEmpty('email',  __('Please enter a email id!'))
    	->add('email', [
    			'valid' => [
    					'rule' => 'email',
    					'message' => 'Please enter a valid email id!',
    			],
    			'_empty' => [
    					'message' => 'The email already taken, Please enter another one!',
    					'rule' => 'validateUnique',
    					'provider' => 'table'
    			]
    	])
    	->allowEmpty('password')
    	->add('password', [
    			'match' => ['rule' => ['compareWith', 'confirm_password'],
    					'message' => 'Confirm password should not match!'
    			]
    	])
    	->inList('is_active', ['active','inactive']);
//     	->add('current_password','custom',[
//     			'rule'=>  function($value, $context){
//     			$user = $this->Users->get($this->Auth->users('id'));
//     			if ($user) {
//     				if ((new DefaultPasswordHasher)->check($value, $user->password)) {
//     					return true;
//     				}
//     			}
//     			return false;
//     			},
//     		'message'=>'The current password does not match!',
//     	])
//    		->notEmpty('current_password');
    	return $validator;
    }
/**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }
        
}
