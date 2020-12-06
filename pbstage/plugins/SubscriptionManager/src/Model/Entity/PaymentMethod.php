<?php
namespace SubscriptionManager\Model\Entity;

use Cake\ORM\Entity;

class PaymentMethod extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
