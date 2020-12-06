<?php
namespace SubscriptionManager\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class BucketsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('buckets');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->hasMany('DriftMailers', [
            'foreignKey' => 'bucket_id',
            'className' => 'SubscriptionManager.DriftMailers',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        return $validator;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }
}
