<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CustomerReferrals Model
 *
 * @property \SubscriptionApi\Model\Table\CustomersTable|\Cake\ORM\Association\BelongsTo $Customers
 * @property \SubscriptionApi\Model\Table\ReferralsTable|\Cake\ORM\Association\BelongsTo $Referrals
 * @property \SubscriptionApi\Model\Table\OrdersTable|\Cake\ORM\Association\BelongsTo $Orders
 *
 * @method \SubscriptionApi\Model\Entity\CustomerReferral get($primaryKey, $options = [])
 * @method \SubscriptionApi\Model\Entity\CustomerReferral newEntity($data = null, array $options = [])
 * @method \SubscriptionApi\Model\Entity\CustomerReferral[] newEntities(array $data, array $options = [])
 * @method \SubscriptionApi\Model\Entity\CustomerReferral|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionApi\Model\Entity\CustomerReferral patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionApi\Model\Entity\CustomerReferral[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionApi\Model\Entity\CustomerReferral findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CustomerReferralsTable extends Table
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

        $this->setTable('customer_referrals');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'className' => 'SubscriptionApi.Customers'
        ]);
        $this->belongsTo('Referrals', [
            'foreignKey' => 'referral_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionApi.Referrals'
        ]);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionApi.Orders'
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('comments', 'create')
            ->notEmpty('comments');

        $validator
            ->boolean('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

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
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['referral_id'], 'Referrals'));
        $rules->add($rules->existsIn(['order_id'], 'Orders'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }

    public function creditToEarn ($customerId, $orderId) {
        $status = 0;
        $currentTime = time();
        $query = $this->find('all', ['fields' => ['id', 'created'], 'conditions' => ['referral_id'=>$customerId, 'order_id'=>'0']])->hydrate(0)->toArray();
        if ( count($query) && ( $currentTime <= (strtotime($query[0]['created']) + PC['REFER_TIME'] )) ) {
            $this->query()->update()->set(['order_id'=>$orderId])->where(['id'=>$query[0]['id']])->execute();
            $status = 1;
        }
        return $status;
    }

    public function earnedToHold ($customerId, $count=1) {
        $ids = [];
        $currentTime = time();
        $query = $this->find('all', ['fields' => ['id', 'created'], 'conditions' => ['customer_id'=>$customerId, 'order_id != '=>'0', 'status'=>0], 'order'=>['id'=>'ASC']])->hydrate(0)->toArray();
        foreach ($query as $value) {
            if ( ( $currentTime <= (strtotime($value['created']) + PC['REFER_TIME'] )) ) {
                $ids[] = $value['id'];
            }
            if ( count($ids) == $count ) {
                break;
            }
        }
        if ( count($ids) > 0 ) {
            $this->query()->update()->set(['status'=>1])->where(['id IN'=>$ids])->execute();
        }
        return $ids;
    }

    public function holdToRedeemed ($ids) {
        $currentTime = time();
        if ( count($ids) > 0 ) {
            $this->query()->update()->set(['status'=>2])->where(['id IN '=>$ids])->execute();
        }
        return true;
    }


}
