<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * DriftMailer Entity
 *
 * @property int $id
 * @property int $bucket_id
 * @property int $schedule_id
 * @property string $title
 * @property string $subject
 * @property string $sender_name
 * @property string $send_at
 * @property string $conditions
 * @property string $content
 * @property string $email
 * @property string $utm_source
 * @property string $utm_medium
 * @property string $utm_campaign
 * @property string $utm_term
 * @property string $utm_content
 * @property \Cake\I18n\FrozenTime $created
 * @property string $status
 *
 * @property \SubscriptionManager\Model\Entity\Bucket $bucket
 * @property \SubscriptionManager\Model\Entity\Schedule $schedule
 */
class DriftMailer extends Entity
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
