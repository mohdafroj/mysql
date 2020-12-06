<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * Product Entity
 *
 * @property int $id
 * @property string $title
 * @property string $sku_code
 * @property int $sort_order
 * @property string $short_description
 * @property string $meta_title
 * @property string $meta_keyword
 * @property string $meta_description
 * @property string $is_active
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\ProductCategory[] $product_categories
 * @property \App\Model\Entity\Review[] $reviews
 * @property \App\Model\Entity\UrlRewrite[] $url_rewrite
 * @property \App\Model\Entity\Wishlist[] $wishlists
 */
class Product extends Entity
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
