<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

/**
 * System Entity
 *
 * @property int $id
 * @property string $core_key
 * @property string $core_value
 * @property \Cake\I18n\FrozenTime $created
 */
class System extends Entity
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
