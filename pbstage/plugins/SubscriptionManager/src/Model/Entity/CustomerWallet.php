<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

class CustomerWallet extends Entity
{

    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
