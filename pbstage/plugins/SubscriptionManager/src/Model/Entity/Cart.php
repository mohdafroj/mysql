<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * Cart Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property int $product_id
 * @property int $qty
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \SubscriptionManager\Model\Entity\Customer $customer
 * @property \SubscriptionManager\Model\Entity\Product $product
 */
class Cart extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
