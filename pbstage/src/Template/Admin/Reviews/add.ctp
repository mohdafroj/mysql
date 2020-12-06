<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Magane Review') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('Back'), ['controller' =>'Reviews/'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
				<li><?= $this->Html->link(__('New Review'), ['controller'=>'Reviews', 'action' => 'add', 'key', md5('reviews')], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><?php //pr($customers);?><!-- start of tab -->
			<?= $this->Form->create($review, ['enctype'=>'multipart/form-data', 'id'=>'submit_review_form', 'class'=>'form-horizontal', 'novalidate' => true]); ?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
									<div class="form-group">
										<label class="col-sm-2 control-label">Customer&nbsp;<span class="text-red">*</span></label>
										<div class="col-sm-4">
												<?= $this->Form->hidden('customer_id', ['id'=>'customer_new']); ?>
												<?php echo $this->Form->unlockField('customer_id');?>
												<?= $this->Form->text('customer_email', ['class'=>'form-control', 'placeholder'=>'Enter customer email', 'value' => '', 'id'=>'customer_email_for_review']); ?>
										</div>
										<label class="col-sm-2 control-label">Select Product&nbsp;<span class="text-red">*</span></label>
										<div class="col-sm-4">
												<?= $this->Form->hidden('product_id', ['id'=>'product_new']); ?>
												<?php echo $this->Form->unlockField('product_id');?>
												<?= $this->Form->text('product_name', ['class'=>'form-control', 'placeholder'=>'Enter product name', 'value' => '', 'id' =>'product_for_review']); ?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label"></label>
										<div class="col-sm-4" id="customerEmail"><span></span>
										</div>
										<label class="col-sm-2 control-label"></label>
										<div class="col-sm-4" id="productName"><span></span>
										</div>
									</div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Title</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->text('title', ['class'=>'form-control', 'placeholder'=>'Enter title']); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Customer Name</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->text('customer_name', ['class'=>'form-control', 'placeholder'=>'Enter customer name']); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Description <span class="text-red">*</span></label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('description', ['class'=>'form-control', 'placeholder'=>'Enter description', 'id'=>'review_description']); ?>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-2 control-label">Rating <span class="text-red">*</span></label>
										<div class="col-sm-4">
                                        	<?= $this->Form->select('rating', ['1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5], ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= isset($error['is_active']['inList']) ? $error['is_active']['inList']:NULL;?></span>
										</div>
										<label class="col-sm-2 control-label">Status <span class="text-red">*</span></label>
										<div class="col-sm-4">
                                        	<?= $this->Form->select('is_active', $this->Admin->reviewStatus, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= isset($error['is_active']['inList']) ? $error['is_active']['inList']:NULL;?></span>
										</div>
									</div>
									
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            <button type="submit" id="submitReview" class="btn btn-div-buy btn-1b">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
					
				</div><!-- end of middle_content -->
            <?= $this->Form->end(); ?>
        </div><!-- end of right_part -->            
    </div><!-- end of tab -->
</section>
<?= $this->Html->script($this->Url->build('/js/admin/reviews.js', true)) ?>
