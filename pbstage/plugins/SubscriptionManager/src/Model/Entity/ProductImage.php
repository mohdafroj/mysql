<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProductImage Entity
 *
 * @property int $id
 * @property int $product_id
 * @property string $title
 * @property string $alt_text
 * @property int $img_order
 * @property string $img_thumbnail
 * @property string $img_small
 * @property string $img_base
 * @property string $img_large
 * @property string $img_popup
 * @property bool $is_thumbnail
 * @property bool $is_small
 * @property bool $is_base
 * @property bool $is_large
 * @property bool $exclude
 * @property string $is_active
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \SubscriptionManager\Model\Entity\Product $product
 */
class ProductImage extends Entity
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
