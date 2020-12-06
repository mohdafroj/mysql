<?php
namespace SubscriptionApi\Model\Table;

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
            'className' => 'SubscriptionApi.Invoices',
        ]);
        $this->hasMany('Orders', [
            'foreignKey' => 'payment_method_id',
            'className' => 'ApidManager.Orders',
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

    //Get payment methods fees by id
    public function getPaymentFee($id)
    {   
        $fees = $this->find('all', ['fields'=>['fees'],'conditions' => ['status' => '1', 'id' => $id]])->hydrate(0)->toArray();
        return $fees[0]['fees'] ?? 0;
    }


    /***** This is for payment gateway by code*****/
    public function getPaymentGatewayByCode($code) {
        $data = $this->find('all')->where(['status' => 1, 'code'=>$code])->hydrate(0)->toArray();
        return $data[0] ?? [];
    }

    /***** This is for default gateway *****/
    public function getDefaultPaymentMethod()
    {
        $data = $this->find('all', ['fields' => ['id'], 'conditions' => ['status' => '1', 'active_default' => '1']])->hydrate(0)->toArray();
        return $data[0] ?? 0;
    }

    /***** This is for get active payment gateway *****/
    public function getPaymentMethods()
    {
        return $this->find('all', ['fields'=>['id','title', 'message','code', 'fees'],'conditions' => ['status'=>'1', 'code != '=>'redeem']])->order(['sort_order' => 'ASC'])->hydrate(0)->toArray();
    }

    public function createNew ($param) {
        $query = $this->find('all')->where(['code' => $param['code']])->hydrate(0)->toArray();
        if ( empty($query) ) {
            $obj = $this->newEntity();
            $obj->title = $param['title'] ?? '';
            $obj->code = $param['code'] ?? '';
            $obj->status = $param['status'] ?? '';
            $obj->fees = $param['fees'] ?? 0.00;        
            $query = $this->save($obj)->toArray();
        } else {
            $query = $query[0] ?? [];
        }
        return $query;
    }
}
