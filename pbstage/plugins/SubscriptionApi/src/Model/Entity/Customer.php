<?php
namespace SubscriptionApi\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

class Customer extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
        //'email' => false,
        //'gender' => false,
        'dob' => false,
        //'mobile' => false,
        'created' => false,
    ];

    protected $_hidden = [
        'password',
    ];

    protected function _setFirstname($value)
    {
        return trim($value);
    }

    protected function _setLastname($value)
    {
        return trim($value);
    }

    protected function _setPassword($value)
    {
        if (!empty($value)) {
            //$hasher = new DefaultPasswordHasher();
            return md5($value); //;$hasher->hash($value);
        } else {
            return $this->password;
        }
    }
    protected function _setProfession($value)
    {
        return trim($value);
    }

    protected function _setAddress($value)
    {
        return trim($value);
    }

    protected function _setCity($value)
    {
        return trim($value);
    }

    protected function _setPincode($value)
    {
        return trim($value);
    }

    protected function _setIsGroup($value)
    {
        return trim($value);
    }

    protected function _setImage($value)
    {
        return trim($value);
    }
    protected function _setLocationId($value)
    {
        return trim($value);
    }
    protected function _setModified($value)
    {
        return trim($value);
    }
    protected function _setLogdate($value)
    {
        return trim($value);
    }
    protected function _setLonum($value)
    {
        return trim($value);
    }
    protected function _setIsActive($value)
    {
        return trim($value);
    }
    protected function _setRpToken($value)
    {
        if (!empty($value)) {
            //$hasher = new DefaultPasswordHasher();
            return md5($value); //$hasher->hash($value);
        } else {
            return $this->rp_token;
        }
    }
    protected function _setRpTokenCreatedAt($value)
    {
        return trim($value);
    }
    protected function _setApiToken($value)
    {
        if (!empty($value)) {
            $hasher = new DefaultPasswordHasher();
            return $hasher->hash($value);
        } else {
            return $this->api_token;
        }
    }
    protected function _setApiTokenCreatedAt($value)
    {
        return trim($value);
    }
}
