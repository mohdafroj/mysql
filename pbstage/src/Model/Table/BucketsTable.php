<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
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
        $this->addBehavior('Auditable');
        $this->hasMany('DriftMailers', [
            'foreignKey' => 'bucket_id'
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
}
