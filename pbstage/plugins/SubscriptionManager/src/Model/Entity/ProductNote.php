<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProductNote Entity
 *
 * @property int $id
 * @property int $product_id
 * @property string $title
 * @property string $description
 * @property string $is_active
 *
 * @property \SubscriptionManager\Model\Entity\Product $product
 */
class ProductNote extends Entity
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
