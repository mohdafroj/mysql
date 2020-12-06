<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Systems Model
 *
 * @method \SubscriptionApi\Model\Entity\System get($primaryKey, $options = [])
 * @method \SubscriptionApi\Model\Entity\System newEntity($data = null, array $options = [])
 * @method \SubscriptionApi\Model\Entity\System[] newEntities(array $data, array $options = [])
 * @method \SubscriptionApi\Model\Entity\System|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \SubscriptionApi\Model\Entity\System patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \SubscriptionApi\Model\Entity\System[] patchEntities($entities, array $data, array $options = [])
 * @method \SubscriptionApi\Model\Entity\System findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SystemsTable extends Table
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

        $this->setTable('systems');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
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
            ->allowEmpty('core_key')
            ->add('core_key', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->allowEmpty('core_value');

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
        $rules->add($rules->isUnique(['core_key']));

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

    public function getInvalidPincodes() {
        $query = $this->find('all', ['fields' => ['id', 'core_value'], 'conditions' => ['core_key'=>'invalidpincodes']])->hydrate(0)->toArray();
        return $query[0] ?? [];
    }

    public function setInvalidPincodes($pincodes) {
        $query = $this->query()->update()->set(['core_value'=>$pincodes])->where(['core_key'=>'invalidpincodes'])->execute();
        return empty($query) ? 0:1;
    }

    public function checkInvalidPincodes($pincodes) {
        $query = $this->find('all', ['fields' => ['id'], 'conditions' => ['core_key'=>'invalidpincodes']])->where(["concat(core_value) REGEXP '($pincodes)'"])->hydrate(0)->toArray();
        return empty($query) ? 0:1;
    }
}
