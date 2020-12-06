<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * AlgoFamily Entity
 *
 * @property int $id
 * @property string $title
 * @property string $image
 * @property string $description
 * @property string $is_active
 * @property int $fscore
 */
class AlgoFamily extends Entity
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
