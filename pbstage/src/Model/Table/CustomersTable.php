<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Customers Model
 *
 * @method \App\Model\Entity\Customer get($primaryKey, $options = [])
 * @method \App\Model\Entity\Customer newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Customer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Customer|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Customer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Customer[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Customer findOrCreate($search, callable $callback = null, $options = [])
 */
class CustomersTable extends Table
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

        $this->setTable('customers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Auditable');
        $this->hasMany('Addresses', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('Memberships', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('Carts', [
            'foreignKey' => 'customer_id',
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
            ->notEmpty('firstname', 'Please enter firstname!')
            ->add('firstname', [
                'length' => ['rule' => ['lengthBetween', 3, 15], 'message' => 'The field should be 3 to 15 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->notEmpty('lastname', 'Please enter lastname!')
            ->add('lastname', [
                'length' => ['rule' => ['lengthBetween', 3, 15], 'message' => 'The field should be 3 to 15 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->notEmpty('email')
            ->add('email', [
                'email' => ['rule' => ['email'], 'message' => 'Please enter valid email id!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This email id already registered!'],
            ])
            ->notEmpty('mobile')
            ->add('mobile', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Mobile should be number only!'],
                'length' => ['rule' => ['lengthBetween', 10, 10], 'message' => 'Mobile should be 10 digit number!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This mobile number already registered!'],
            ])
            ->notEmpty('address', 'Please enter address!')
            ->notEmpty('city', 'Please enter City/Town/District!')
            ->add('city', [
                'length' => ['rule' => ['lengthBetween', 2, 15], 'message' => 'City should be 2 to 15 chars long!'],
            ])
            ->notEmpty('pincode', 'Please enter pincode!')
            ->add('pincode', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Pincode should be 6 digit numeric number!'],
                'length' => ['rule' => ['lengthBetween', 6, 6], 'message' => 'Pincode should be 6 digit numeric number!'],
            ]);

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
        $rules->add($rules->isUnique(['mobile']));
        return $rules;
    }

    public function validationAdminNewProfile($validator)
    {
        $validator
            ->notEmpty('firstname', 'Please enter firstname!')
            ->add('firstname', [
                'length' => ['rule' => ['lengthBetween', 3, 15], 'message' => 'The field should be 3 to 15 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->notEmpty('lastname', 'Please enter lastname!')
            ->add('lastname', [
                'length' => ['rule' => ['lengthBetween', 3, 15], 'message' => 'The field should be 3 to 15 chars long!'],
                //'charNum' => ['rule' => ['alphaNumeric'], 'message' => 'The field should be alpha numeric only!'],
            ])
            ->notEmpty('email')
            ->add('email', [
                'email' => ['rule' => ['email'], 'message' => 'Please enter valid email id!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This email id already registered!'],
            ])
            ->notEmpty('mobile')
            ->add('mobile', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Mobile should be number only!'],
                'length' => ['rule' => ['lengthBetween', 10, 10], 'message' => 'Mobile should be 10 digit number!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This mobile number already registered!'],
            ])
            ->notEmpty('address', 'Please enter address!')
            ->notEmpty('city', 'Please enter City/Town/District!')
            ->add('city', [
                'length' => ['rule' => ['lengthBetween', 2, 15], 'message' => 'City should be 2 to 15 chars long!'],
            ])
            ->notEmpty('pincode', 'Please enter pincode!')
            ->add('pincode', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Pincode should be 6 digit numeric number!'],
                'length' => ['rule' => ['lengthBetween', 6, 6], 'message' => 'Pincode should be 6 digit numeric number!'],
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
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This email id already registered!'],
            ])
            ->notEmpty('mobile')
            ->add('mobile', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Mobile should be number only!'],
                'length' => ['rule' => ['lengthBetween', 10, 10], 'message' => 'Mobile should be 10 digit number!'],
                'unique' => ['rule' => 'validateUnique', 'provider' => 'table', 'message' => 'This mobile number already registered!'],
            ])
            ->allowEmpty('address')
            ->allowEmpty('city')
            ->add('city', [
                'length' => ['rule' => ['lengthBetween', 5, 15], 'message' => 'The field should be 5 to 15 chars long!'],
            ])
            ->allowEmpty('pincode')
            ->add('pincode', [
                '_empty' => ['rule' => ['numeric'], 'message' => 'Pincode should be 6 digit numeric number!'],
                'length' => ['rule' => ['lengthBetween', 6, 6], 'message' => 'Pincode should be 6 digit numeric number!'],
            ])
            ->allowEmpty('password')
            ->add('password', [
                'match' => ['rule' => ['compareWith', 'confirm_password'],
                    'message' => 'Confirm OTP should not match!',
                ],
            ]);

        return $validator;
    }
}
