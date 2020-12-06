<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * Membership Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property int $order_id
 * @property string $title
 * @property float $price
 * @property string $custom_data
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $valid
 * @property string $status
 *
 * @property \SubscriptionManager\Model\Entity\Customer $customer
 * @property \SubscriptionManager\Model\Entity\Order $order
 */
class Membership extends Entity
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
