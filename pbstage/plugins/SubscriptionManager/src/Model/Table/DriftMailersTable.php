<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DriftMailersTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('drift_mailers');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Buckets', [
            'foreignKey' => 'bucket_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Buckets',
        ]);
        $this->belongsTo('Schedules', [
            'foreignKey' => 'schedule_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.Schedules',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('subject');

        $validator
            ->allowEmpty('sender_name');

        $validator
            ->allowEmpty('send_at');

        $validator
            ->allowEmpty('conditions');

        $validator
            ->allowEmpty('content');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->allowEmpty('utm_source');

        $validator
            ->allowEmpty('utm_medium');

        $validator
            ->allowEmpty('utm_campaign');

        $validator
            ->allowEmpty('utm_term');

        $validator
            ->allowEmpty('utm_content');

        $validator
            ->allowEmpty('status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['bucket_id'], 'Buckets'));
        $rules->add($rules->existsIn(['schedule_id'], 'Schedules'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
