<?php 
    $crtAction = $this->request->getParam('action');
    $id            = isset($id) ? $id : 0;
?>
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?php echo $id ? '':'Add '; ?>Product Information <?php echo $id ? ' (#'.$id.')' : NULL; ?> </h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('Back'), ['controller' =>'Products/'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
				<li><?= $this->Html->link(__('New Product'), ['controller'=>'Products', 'action' => 'add', 'key', md5('products')], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
                <?php if( $id > 0 ){ ?>
                <li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $id], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $id)]) ?></li>
    <?php }?>            
    <?php if( $crtAction == 'images' ){ ?>
                <li><button type="button" id="updateButton" class="btn btn-div-buy btn-1b" onClick="updateImages();">Save</button></li>
    <?php }?>            
            </ul>
        </div><!-- end of inner_heading -->
</section>