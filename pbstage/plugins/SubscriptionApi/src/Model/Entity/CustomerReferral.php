<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

/**
 * CustomerReferral Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property int $referral_id
 * @property int $order_id
 * @property string $comments
 * @property bool $status
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \SubscriptionApi\Model\Entity\Customer $customer
 * @property \SubscriptionApi\Model\Entity\Referral $referral
 * @property \SubscriptionApi\Model\Entity\Order $order
 */
class CustomerReferral extends Entity
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
