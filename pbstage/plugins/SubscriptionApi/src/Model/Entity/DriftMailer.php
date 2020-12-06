<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class DriftMailer extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
