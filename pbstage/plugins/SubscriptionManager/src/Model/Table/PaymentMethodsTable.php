<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PaymentMethodsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('payment_methods');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Invoices', [
            'foreignKey' => 'payment_method_id',
            'className' => 'SubscriptionManager.Invoices',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'payment_method_id',
            'className' => 'SubscriptionManager.Orders',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title')
            ->add('title', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->numeric('fees')
            ->requirePresence('fees', 'create')
            ->notEmpty('fees');

        $validator
            ->requirePresence('active_default', 'create')
            ->notEmpty('active_default');

        $validator
            ->integer('sort_order')
            ->requirePresence('sort_order', 'create')
            ->notEmpty('sort_order');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['title']));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }

    /***** This is for get payment gateway by given id *****/
    public function getPaymentGatewayById($id)
    {   
        $data = $this->find('all', ['conditions' => ['id' => $id]])->hydrate(0)->toArray();
        return $data[0] ?? [];
    }

}
