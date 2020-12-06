<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

class CustomerLog extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
