<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CustomersTable extends Table
{
    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('customers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->hasMany('SubscriptionManager.Addresses', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('SubscriptionManager.Memberships', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('SubscriptionManager.Orders', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('SubscriptionManager.CustomerWallets', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('SubscriptionManager.Carts', [
            'foreignKey' => 'customer_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('firstname', 'create')
            ->notEmpty('firstname');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->email('mobile')
            ->requirePresence('mobile', 'create')
            ->notEmpty('mobile');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }

    public function validationAdminNewProfile($validator)
    {
        $validator
            ->notEmpty('firstname', 'Please enter firstname!')
            ->add('firstname', [
                'length' => ['rule' => ['lengthBetween', 3, 50], 'message' => 'The field should be 3 to 50 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->notEmpty('lastname', 'Please enter lastname!')
            ->add('lastname', [
                'length' => ['rule' => ['lengthBetween', 3, 50], 'message' => 'The field should be 3 to 50 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->notEmpty('email')
            ->add('email', [
                'email' => ['rule' => ['email'], 'message' => 'Please enter valid email id!'],
                'length' => ['rule' => ['lengthBetween', 5, 100], 'message' => 'Email should be 5 to 100 char long!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This email id already registered!'],
            ])
            ->notEmpty('mobile')
            ->add('mobile', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Mobile should be number only!'],
                'length' => ['rule' => ['lengthBetween', 8, 20], 'message' => 'Mobile should be 8 to 20 digit number!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This mobile number already registered!'],
            ])
            ->allowEmpty('password')
            ->add('password', [
                'match' => ['rule' => ['compareWith', 'confirm_password'],
                    'message' => 'Confirm password should not match!',
                ],
            ]);

        return $validator;
    }
    public function validationAdminProfile($validator)
    {
        $validator
            ->allowEmpty('firstname')
            ->add('firstname', [
                'length' => ['rule' => ['lengthBetween', 3, 15], 'message' => 'The field should be 3 to 15 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->allowEmpty('lastname')
            ->add('lastname', [
                'length' => ['rule' => ['lengthBetween', 3, 15], 'message' => 'The field should be 3 to 15 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->notEmpty('email')
            ->add('email', [
                'email' => ['rule' => ['email'], 'message' => 'Please enter valid email id!'],
                'length' => ['rule' => ['lengthBetween', 5, 100], 'message' => 'Email should be 5 to 100 char long!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This email id already registered!'],
            ])
            ->notEmpty('mobile')
            ->add('mobile', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Mobile should be number only!'],
                'length' => ['rule' => ['lengthBetween', 8, 20], 'message' => 'Mobile should be 8 to 20 digit number!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This mobile number already registered!'],
            ])
            ->allowEmpty('password')
            ->add('password', [
                'match' => ['rule' => ['compareWith', 'confirm_password'],
                    'message' => 'Confirm password should not match!',
                ],
            ]);

        return $validator;
    }
}
