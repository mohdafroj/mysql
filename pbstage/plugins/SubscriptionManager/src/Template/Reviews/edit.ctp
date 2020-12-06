<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?=h('Magane Review')?></h3>
            <ul class="list-inline list-unstyled">
                <li><?=$this->Html->link(__('Back'), ['controller' => 'Reviews/'], ['class' => 'btn btn-div-cart btn-1e'])?></li>
				<li><?=$this->Html->link(__('New Review'), ['controller' => 'Reviews', 'action' => 'add', 'key', md5('reviews')], ['class' => 'btn btn-div-buy btn-1b'])?></li>
                <li><?=$this->Form->postLink(__('Delete'), ['action' => 'delete', $id], ['block' => false, 'method' => 'delete', 'class' => 'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $id)])?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><?php //pr($customers);?><!-- start of tab -->
			<?=$this->Form->create($review, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
									<div class="form-group">
										<label class="col-sm-2 control-label">Select Customer&nbsp;<span class="text-red">*</span></label>
										<div class="col-sm-4">
												<?=$this->Form->hidden('customer_id', ['id' => 'customer_new', 'value' => $customers['id']]);?>
												<?php echo $this->Form->unlockField('customer_id'); ?>
												<?=$this->Form->text('customer_email', ['class' => 'form-control', 'placeholder' => 'Enter customer email', 'value' => $customers['email'], 'id' => 'customer_email_for_review']);?>
										</div>
										<label class="col-sm-2 control-label">Select Product&nbsp;<span class="text-red">*</span></label>
										<div class="col-sm-4">
												<?=$this->Form->hidden('product_id', ['id' => 'product_new', 'value' => $products['id']]);?>
												<?php echo $this->Form->unlockField('product_id'); ?>
												<?=$this->Form->text('product_name', ['class' => 'form-control', 'placeholder' => 'Enter product name', 'value' => $products['name'] ?? '', 'id' => 'product_for_review']);?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label"></label>
										<div class="col-sm-4" id="customerEmail"><span><?php echo isset($customers['email']) ? 'selected: ' . $customers['email'] : ''; ?></span>
										</div>
										<label class="col-sm-2 control-label"></label>
										<div class="col-sm-4" id="productName"><span><?php echo isset($products['title']) ? 'selected: ' . $products['title'] : ''; ?></span>
										</div>
									</div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Title</label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->text('Reviews.title', ['class' => 'form-control', 'placeholder' => 'Enter title']);?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Customer Name</label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->text('Reviews.customer_name', ['class' => 'form-control', 'placeholder' => 'Enter customer name']);?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Description <span class="text-red">*</span></label>
                                        <div class="col-sm-10">
                                        	<?=$this->Form->textarea('Reviews.description', ['class' => 'form-control', 'placeholder' => 'Enter description']);?>
                                        </div>
                                    </div>
									<div class="form-group">
									<label class="col-sm-2 control-label">Rating <span class="text-red">*</span></label>
										<div class="col-sm-2">
                                        	<?=$this->Form->select('Reviews.rating', ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5], ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?=$error['is_active']['inList'] ?? ''?></span>
										</div>
										<label class="col-sm-2 control-label">Country <span class="text-red">*</span></label>
										<div class="col-sm-2">
                                        	<?=$this->Form->select('Reviews.location_id', $locations, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
										</div>
										<label class="col-sm-2 control-label">Status <span class="text-red">*</span></label>
										<div class="col-sm-2">
                                        	<?=$this->Form->select('Reviews.is_active', $this->Admin->reviewStatus, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?=$error['is_active']['inList'] ?? ''?></span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label">Created Date</label>
										<div class="col-sm-4">
											<div class="input-group date" style="width:50%;float:left;margin-right:1%;">
												<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
												<?=$this->Form->text('Reviews.created', ['id' => 'datepickerForReviews', 'class' => 'form-control', 'value' => date('Y-m-d h:m:s', strtotime($review['created'])), 'style' => 'width:100%;']);?>
											</div>
										</div>
										<label class="col-sm-2 control-label"></label>
										<div class="col-sm-4">
										</div>
									</div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->

				</div><!-- end of middle_content -->
            <?=$this->Form->end();?>
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>

<script>
$(document).ready(function(){
	if( $("#customer_email_for_review" ).length > 0 ){
		$( "#customer_email_for_review" ).autocomplete({
			source: "<?php echo $this->Url->build(['plugin' => 'SubscriptionManager', 'controller' => 'customers', 'action' => 'search']) ?>",
			minLength: 2,
			select: function( event, ui )
			{
				$('#customer_new').val(ui.item.id);
				$('#customerEmail span').html("selected: "+ui.item.email);
			}
		});
	}else{
		$('#customer_new').val("");
		$('#customerEmail span').html("");
	}
	if( $("#product_for_review" ).length > 0 ){
		$( "#product_for_review" ).autocomplete({
			source: "<?php echo $this->Url->build(['plugin' => 'SubscriptionManager', 'controller' => 'products', 'action' => 'search']) ?>",
			minLength: 2,
			select: function( event, ui )
			{
				$('#product_new').val(ui.item.id);
				$('#productName span').html("selected: "+ui.item.title);
			}
		});
	}else{
		$('#customer_new').val("");
		$('#customerEmail span').html("");
	}
});
</script>


