<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * AlgoProduct Entity
 *
 * @property int $id
 * @property string $product_name
 * @property string $fragrantica_brands
 * @property string $url
 * @property string $group_name
 * @property string $image
 * @property string $main_accords
 * @property string $user_votes_notes
 * @property string $perfume_pyramid
 * @property string $main_notes_according_votes
 * @property \Cake\I18n\FrozenTime $created_date
 */
class AlgoProduct extends Entity
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
