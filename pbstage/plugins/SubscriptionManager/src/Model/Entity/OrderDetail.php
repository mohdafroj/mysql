<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * OrderDetail Entity
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $title
 * @property string $sku_code
 * @property string $size
 * @property float $price
 * @property int $qty
 * @property float $discount
 * @property string $short_description
 *
 * @property \SubscriptionManager\Model\Entity\Order $order
 * @property \SubscriptionManager\Model\Entity\Product $product
 */
class OrderDetail extends Entity
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
