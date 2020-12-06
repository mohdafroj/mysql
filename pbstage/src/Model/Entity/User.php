<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Text;
/**
 * User Entity
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $username
 * @property string $password
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $logdate
 * @property int $lognum
 * @property int $reload_acl_flag
 * @property int $is_active
 * @property string $extra
 * @property string $rp_token
 * @property \Cake\I18n\FrozenTime $rp_token_created_at
 */
class User extends Entity
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

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];
    
    protected function _setFirstname($value)
    {
    	return trim($value);
    }
    
    protected function _setLastname($value)
    {
    	return trim($value);
    }
    
    protected function _setUsername($value)
    {
    	return trim($value);
    }
    
    protected function _setEmail($value)
    {
    	return trim($value);
    }
    
    protected function _setPassword($value)
    {
    	if( !empty($value) ){
    		$hasher = new DefaultPasswordHasher();
    		return $hasher->hash($value);
    	}else{
    		return $this->password;
    	}
    }
}
