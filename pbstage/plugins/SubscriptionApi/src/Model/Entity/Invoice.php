<?php
namespace SubscriptionApi\Model\Entity;

use Cake\ORM\Entity;

class Invoice extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
