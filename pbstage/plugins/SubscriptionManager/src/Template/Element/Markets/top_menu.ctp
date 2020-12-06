<?php 
    $currentAction = $this->request->getParam('action');
    $this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<!-- Content Header (Page header) -->
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3> Drift Markets
                <?php 
                    $bucketId = $bucketId ?? 0;
                    echo isset($bucket->title) ? ' >> '.$bucket->title:NULL; 
                ?>
            </h3>
<?php if( $bucketId > 0 ) {?>
            <ul class="list-inline list-unstyled">
                <li>
                    <?php echo $this->Html->link('Back',"javascript:history.back()", ['class'=>'btn btn-div-buy btn-1b']); ?>
                </li>
                <li>
                    <?= $this->Html->link('New', ['action' =>'mailer', $bucketId, 'key', md5($bucketId), 0, 'ref', md5(0)], ['class'=>'btn btn-div-buy btn-1b']);?>
                </li>
<?php if( 'mailer' == $currentAction ){?>
    <?php   if( $mailer->status == 'active' ){?>
                <li>
                    <?= $this->Html->link(__('<i class="fa fa-send"></i> Send'), '#', ['data-toggle'=> 'modal', 'data-target'=>'#ConfirmDelete', 'escape'=>false,'class'=>'btn btn-danger btn-xs']);  ?>                              
                </li>
    <?php   } ?>
                <li>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $mailer->id], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $mailer->title)]) ?>
                </li>
<?php } ?>                
            </ul>
<?php } ?>            
        </div><!-- end of inner_heading -->
</section>
