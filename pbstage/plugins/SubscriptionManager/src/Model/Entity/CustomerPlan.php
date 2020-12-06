<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * CustomerPlan Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property string $sku
 * @property int $duration
 * @property float $price
 * @property string $currency
 * @property string $description
 * @property string $image
 * @property string $is_active
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \SubscriptionManager\Model\Entity\Customer $customer
 */
class CustomerPlan extends Entity
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
