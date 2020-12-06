<?php 
    $currentAction = $this->request->getParam('action');
    $this->Paginator->setTemplates(['templates'=>'admin-list']);
	$usedCustomers = isset($customData['used_customers']) ? $customData['used_customers'] : [];
    
?>
<!-- Content Header (Page header) -->
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?php echo ($currentAction == 'addCoupons') ? 'Manage Coupons Code':'Manage Shopping Rule'; ?></h3>
            <ul class="list-inline list-unstyled">
<?php if( isset($rule->id) && $rule->id ){?>
                <li><?= $this->Html->link(__('View Rule'), ['action' => 'editRule', $rule->id, 'key', md5($rule->id)], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
                <li>
                    <?= $this->Html->link(__('Add Coupons'), ['action' =>'addCoupons', $rule->id, 'key', md5($rule->id)], ['class'=>'btn btn-div-buy btn-1b']) ?>
                </li>
 <?php } ?>                
                <li><?= $this->Html->link(__('Back'), ['action' =>'index'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
                <li><?= $this->Html->link(__('New Rule'), ['action' => 'addRule', 'key', md5('shopping')], ['class'=>'btn btn-div-buy btn-1b']) ?></li>

<?php if(  isset($rule->id) && !count($usedCustomers) ){?>
                <li>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $rule->id], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $rule->id)]) ?>
                </li>
<?php } ?>                
            </ul>
        </div><!-- end of inner_heading -->
</section>
