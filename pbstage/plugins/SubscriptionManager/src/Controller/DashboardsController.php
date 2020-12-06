<?php
namespace SubscriptionManager\Controller;

use SubscriptionManager\Controller\AppController;

class DashboardsController extends AppController
{

    public function index()
    {
        $dashboards = []; //$this->paginate($this->Dashboards);

        $this->set(compact('dashboards'));
        $this->set('_serialize', ['dashboards']);
    }

}
