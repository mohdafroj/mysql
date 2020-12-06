<?php
namespace SubscriptionApi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WishlistsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('wishlists');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('SubscriptionApi.Customers');
        $this->belongsTo('SubscriptionApi.Products');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('location_ip');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['customer_id'], 'Customers'));
        $rules->add($rules->existsIn(['product_id'], 'Products'));

        return $rules;
    }

    public static function defaultConnectionName()
    {
        return 'subscription_manager';
    }

    /***** This is add Customer wishlist items *****/
    public function addItemIntoWishlist($customerId, $itemId)
    {
        $message = 'Sorry, Invalid data!';
        $status = 0;
        if (($customerId > 0) && ($itemId > 0)) {
            $query = $this->find('all', ['conditions' => ['customer_id' => $customerId, 'product_id' => $itemId]])->toArray();
            if (count($query) > 0) {
                $message = 'Sorry, Item already exists into your wishlist!';
            } else {
                $wList = $this->newEntity();
                $wList->customer_id = $customerId;
                $wList->product_id = $itemId;
                $status = ($this->save($wList)) ? 1 : 0;
                $message = $status ? 'One item added into wishlist!' : 'Sorry, try aogain!';
            }
        }
        return ['message' => $message, 'status' => $status];
    }
    
    /***** This is delete Customer wishlist items *****/
    public function revomeItemFromWishlists($customerId, $itemId = 0)
    {
        $wList = $this->query()->delete()->where(['customer_id' => $customerId, 'product_id' => $itemId])->execute();
        return ($wList != false) ? true : false;
    }


}
