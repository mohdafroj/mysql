<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class CustomerLog extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
