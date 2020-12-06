<?php
namespace SubscriptionManager\View;
use Cake\View\View;

class AppView extends View
{
    public function initialize()
    {
		$this->loadHelper('Html');
        $this->loadHelper('Form');
        $this->loadHelper('Flash');
        $this->loadHelper('Paginator', ['templates' => 'pagination/admin-list']);
        //$this->loadHelper('SubscriptionManager');
    }
}
