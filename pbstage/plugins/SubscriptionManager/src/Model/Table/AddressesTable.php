<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AddressesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('addresses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Customers'
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
        ->notEmpty('firstname','Please enter firstname!')
        ->add('firstname', [
            'length' => ['rule' => ['lengthBetween', 3, 50], 'message' => 'The firstname should be 3 to 50 character long!'],
            'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The firstname contains only a-z, 0-1 and space characters only!']
        ])
        ->notEmpty('lastname','Please enter lastname!')
        ->add('lastname', [
            'length' => ['rule' => ['lengthBetween', 3, 50], 'message' => 'The lastname should be 3 to 50 character long!'],
            'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The lastname contains only a-z, 0-1 and space characters only!']
        ])
        ->notEmpty('address','Please enter address!')
        ->notEmpty('city','Please enter City/Town/District!')
        ->add('city', [
            'length' => ['rule' => ['lengthBetween', 3, 200], 'message' => 'The city should be 3 to 200 character long!'],
            'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The city contains only a-z, 0-1 and space characters only!']
        ])
        ->add('state', [
            'length' => ['rule' => ['lengthBetween', 3, 200], 'message' => 'The State should be 3 to 200 character long!'],
            'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The State contains only a-z, 0-1 and space characters only!']
        ])
        ->add('country', [
            'length' => ['rule' => ['lengthBetween', 3, 100], 'message' => 'The Country should be 3 to 100 character long!'],
            'charNum' => ['rule' =>['custom', '/^[a-z0-9 ]*$/i'], 'message' => 'The Country contains only a-z, 0-1 and space characters only!']
        ])
        ->notEmpty('pincode','Please enter pincode!')
        ->add('pincode', [
            'length' => ['rule' => ['lengthBetween', 5, 20], 'message' => 'The pincode should be 5 to 20 char long!'],
        ])
        ->notEmpty('mobile','Please enter mobile!')
        ->add('mobile', [
            'length' => ['rule' => ['lengthBetween', 8, 20], 'message' => 'The mobile should be 8 to 20 char long!'],
            ])
        ->notEmpty('email','Please enter email!')
        ->add('email', [
            'email' =>['rule'=>['email'], 'message'=>'Please enter valid email id!'], 
            'length'=>['rule'=>['lengthBetween', 5, 100], 'message' => 'Email should be 5 to 100 char long!'],
        ]);
        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        //$rules->add($rules->existsIn(['email']));
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
