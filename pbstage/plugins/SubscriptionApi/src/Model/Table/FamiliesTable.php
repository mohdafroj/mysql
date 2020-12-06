<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class FamiliesTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('families');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('image');

        $validator
            ->allowEmpty('description');

        $validator
            ->requirePresence('is_active', 'create')
            ->notEmpty('is_active');

        return $validator;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }

    public function getFamilies()
    {
        return $this->find('all', ['conditions' => ['is_active' => 'active']])->hydrate(0)->toArray();
    }

    public function getScoreByTitle ( $title ) {
		$res = $this->find('all', ['conditions' => ['title' => $title, 'is_active' => 'active']])->hydrate(0)->toArray();
		return $res[0]['fscore'] ?? 0;
    }

}
