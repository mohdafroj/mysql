<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Orders Model
 *
 * @property \App\Model\Table\CustomersTable|\Cake\ORM\Association\BelongsTo $Customers
 * @property \App\Model\Table\OrderDetailsTable|\Cake\ORM\Association\HasMany $OrderDetails
 *
 * @method \App\Model\Entity\Order get($primaryKey, $options = [])
 * @method \App\Model\Entity\Order newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Order[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Order|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Order patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Order[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Order findOrCreate($search, callable $callback = null, $options = [])
 */
class OrdersTable extends Table
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

        $this->setTable('orders');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Auditable');
        $this->belongsTo('Customers');
        $this->belongsTo('PaymentMethods');
        $this->belongsTo('Couriers', [
            'foreignKey' => 'delhivery_pickup_id',
        ]);

	    $this->hasOne('PgResponses');
        $this->hasMany('OrderDetails', [
            'foreignKey' => 'order_id'
        ]);
        $this->hasMany('OrderComments', [
            'foreignKey' => 'order_id'
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
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('order_mode');

        $validator
            ->numeric('order_amount')
            ->requirePresence('order_amount', 'create')
            ->notEmpty('order_amount');

        $validator
            ->numeric('order_discount')
            ->requirePresence('order_discount', 'create')
            ->notEmpty('order_discount');

        $validator
            ->numeric('order_shipping_amount')
            ->requirePresence('order_shipping_amount', 'create')
            ->notEmpty('order_shipping_amount');

        $validator
            ->numeric('order_mode_amount')
            ->requirePresence('order_mode_amount', 'create')
            ->notEmpty('order_mode_amount');

        $validator
            ->numeric('order_tax')
            ->requirePresence('order_tax', 'create')
            ->notEmpty('order_tax');

        $validator
            ->allowEmpty('order_coupon');

        $validator
            ->allowEmpty('order_tracking_number');

        $validator
            ->allowEmpty('order_email');

        $validator
            ->dateTime('order_date')
            ->requirePresence('order_date', 'create')
            ->notEmpty('order_date');

        $validator
            ->allowEmpty('status');

        $validator
            ->allowEmpty('shipping_firstname');

        $validator
            ->allowEmpty('shipping_lastname');

        $validator
            ->allowEmpty('shipping_address');

        $validator
            ->allowEmpty('shipping_city');

        $validator
            ->allowEmpty('shipping_state');

        $validator
            ->allowEmpty('shipping_country');

        $validator
            ->allowEmpty('shipping_pincode');

        $validator
            ->allowEmpty('shipping_email');

        $validator
            ->allowEmpty('shipping_phone');

        $validator
            ->allowEmpty('billing_firstname');

        $validator
            ->allowEmpty('billing_lastname');

        $validator
            ->allowEmpty('billing_address');

        $validator
            ->allowEmpty('billing_city');

        $validator
            ->allowEmpty('billing_state');

        $validator
            ->allowEmpty('billing_country');

        $validator
            ->allowEmpty('billing_pincode');

        $validator
            ->allowEmpty('billing_email');

        $validator
            ->allowEmpty('billing_phone');

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

        return $rules;
    }
}
