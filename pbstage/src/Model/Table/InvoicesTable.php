<?php
namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Invoices Model
 *
 * @property \App\Model\Table\CustomersTable|\Cake\ORM\Association\BelongsTo $Customers
 * @property \App\Model\Table\InvoiceDetailsTable|\Cake\ORM\Association\HasMany $InvoiceDetails
 *
 * @method \App\Model\Entity\Invoice get($primaryKey, $options = [])
 * @method \App\Model\Entity\Invoice newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Invoice[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Invoice|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Invoice patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Invoice[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Invoice findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class InvoicesTable extends Table
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

        $this->setTable('invoices');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Auditable');
        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Couriers', [
            'foreignKey' => 'pickup_id',
        ]);
        //->setBindingkey('pickup_id');
        $this->hasMany('InvoiceDetails', [
            'foreignKey' => 'invoice_id',
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
            ->requirePresence('invoice_number', 'create')
            ->notEmpty('invoice_number');

        $validator
            ->requirePresence('order_number', 'create')
            ->notEmpty('order_number');

        $validator
            ->allowEmpty('payment_mode');

        $validator
            ->numeric('payment_amount')
            ->requirePresence('payment_amount', 'create')
            ->notEmpty('payment_amount');

        $validator
            ->numeric('discount')
            ->requirePresence('discount', 'create')
            ->notEmpty('discount');

        $validator
            ->allowEmpty('ship_method');

        $validator
            ->numeric('ship_amount')
            ->requirePresence('ship_amount', 'create')
            ->notEmpty('ship_amount');

        $validator
            ->numeric('mode_amount')
            ->requirePresence('mode_amount', 'create')
            ->notEmpty('mode_amount');

        $validator
            ->allowEmpty('coupon_code');

        $validator
            ->allowEmpty('tracking_code');

        $validator
            ->allowEmpty('mobile');

        $validator
            ->allowEmpty('email');

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
        // $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));

        return $rules;
    }
}
