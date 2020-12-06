<?php 
    $crtAction = $this->request->getParam('action');
    $id            = isset($id) ? $id : 0;
?>
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Customer Information') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('Back'), ['controller' =>'Customers','action' => 'index'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
                <li><?= $this->Html->link(__('New Customer'), ['action' => 'add'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
                <li><?= $this->Form->postLink(__('Delete Customer'), ['action' => 'delete', $id], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-buy btn-1b', 'confirm' => __('Are you sure you want to delete # {0}?', $id)]) ?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
