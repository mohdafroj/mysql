<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Location Entity
 *
 * @property int $id
 * @property string $title
 * @property string $code
 * @property int $lft
 * @property int $rght
 * @property int $parent_id
 * @property bool $is_active
 *
 * @property \App\Model\Entity\ParentLocation $parent_location
 * @property \App\Model\Entity\Customer[] $customers
 * @property \App\Model\Entity\ChildLocation[] $child_locations
 */
class Location extends Entity
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
