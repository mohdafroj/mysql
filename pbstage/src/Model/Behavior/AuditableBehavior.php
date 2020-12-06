<?php

namespace App\Model\Behavior;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use \Cake\ORM\TableRegistry;

class AuditableBehavior extends Behavior
{
    public function afterSave(Event $event, Entity $entity )
    {
        $userId = $_SESSION['Auth']['User']['id'] ?? 0;
        if($userId) {
            $data = [			        
                'user_id' => $userId,
                'entity_name' =>$event->subject()->table(),
                'content' => json_encode($entity),			
                'machine_ip'=>$_SERVER['REMOTE_ADDR'],
                'action_type' => $entity->isNew() ? 'CREATE' : 'UPDATE'    
                ];
            $Audits = TableRegistry::get('SysLogs');
            $audit = $Audits->newEntity($data);
            $audit = $Audits->save($audit);
        }
    }
    
    public function afterDelete(Event $event, Entity $entity )
    {
        $userId = $_SESSION['Auth']['User']['id'] ?? 0;
        if($userId){
            $data = [			        
                'user_id' => $userId,
                'entity_name' =>$event->subject()->table(),
                'content' => json_encode($entity),			
                'machine_ip'=>$_SERVER['REMOTE_ADDR'],
                'action_type' => 'DELETE'    
                ];
            $Audits = TableRegistry::get('SysLogs');
            $audit = $Audits->newEntity($data);
            $audit = $Audits->save($audit);
        }
           
	}
}
?>