<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * OrderComment Entity
 *
 * @property int $id
 * @property int $order_id
 * @property string $given_by
 * @property string $status
 * @property string $comment
 *
 * @property \SubscriptionManager\Model\Entity\Order $order
 */
class OrderComment extends Entity
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
