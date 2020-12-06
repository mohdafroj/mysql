<?php
$this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Manage Payment Methods') ?></h3>
        <ul class="list-inline list-unstyled">
            <li><?= $this->Html->link(__('View Methods'), ['controller'=>'Pgs', 'action' => 'index'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
            <li><?= $this->Html->link(__('New'), ['controller'=>'Pgs', 'action' => 'add', 'key', md5('pgs')], ['class'=>'btn btn-div-buy btn-1e']) ?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>