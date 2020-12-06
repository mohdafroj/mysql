<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProductCategory Entity
 *
 * @property int $product_id
 * @property int $category_id
 *
 * @property \SubscriptionManager\Model\Entity\Product $product
 * @property \SubscriptionManager\Model\Entity\Category $category
 */
class ProductCategory extends Entity
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
        'product_id' => false,
        'category_id' => false
    ];
}
