<?php
    $action = $this->request->getParam('action');
    $this->Paginator->setTemplates(['templates' => 'admin-list']);
?>
<!-- Content Header (Page header) -->
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3> Cms >> 
                <?php
                    echo ($action != 'index') ? ($cms->title ?? 'Add New Page') : 'Pages';
                ?>
            </h3>
            <ul class="list-inline list-unstyled">
                <li>
                    <?php echo $this->Html->link('Back', "javascript:history.back()", ['class' => 'btn btn-div-buy btn-1b']); ?>
                </li>
                <li>
                    <?=$this->Html->link('New', ['action' => 'Pages'], ['class' => 'btn btn-div-buy btn-1b']);?>
                </li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
