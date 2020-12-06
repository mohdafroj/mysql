<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SchedulesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('schedules');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('DriftMailers', [
            'foreignKey' => 'drift_mailer_id',
            'joinType' => 'INNER',
            'className' => 'SubscriptionManager.DriftMailers',
        ]);
        $this->hasMany('DriftMailers', [
            'foreignKey' => 'schedule_id',
            'className' => 'SubscriptionManager.DriftMailers',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['drift_mailer_id'], 'DriftMailers'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
