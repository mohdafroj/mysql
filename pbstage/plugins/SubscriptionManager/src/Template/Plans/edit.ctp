<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?=h('Manage Plans -> Edit')?></h3>
        <ul class="list-inline list-unstyled">
		<li><?=$this->Html->link(__('List'), ['controller' => 'Plans', 'action' => 'index'], ['class' =>'btn btn-div-buy btn-1b'])?></li>
		<li><?=$this->Html->link(__('New Product'), ['controller' => 'Plans', 'action' => 'add', 'key', md5('products')], ['class' =>'btn btn-div-buy btn-1b'])?></li>
		<li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $id], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $id)]) ?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
		<?=$this->Form->create($plan, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
			<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
				<div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
					<div class="box box-default"><!-- start of box_div -->
						<div class="col-sm-6 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
							<div class="box-body">
								<div class="form-group">
									<label class="col-sm-3 control-label">Name <span class="text-red">*</span></label>
									<div class="col-sm-9">
										<?=$this->Form->text('Plans.name', ['class' => 'form-control', 'placeholder' => 'Enter name']);?>
										<span class="text-red">
											<?php echo $error['name']['_empty'] ?? ''; ?>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">SKU Code <span class="text-red">*</span></label>
									<div class="col-sm-9">
										<?=$this->Form->text('Plans.sku', ['class' => 'form-control', 'placeholder' => 'Enter sku code']);?>
										<span class="text-red">
											<?php
												echo $error['sku']['_empty'] ?? '';
												echo $error['sku']['length'] ?? '';
												echo $error['sku']['charNum'] ?? '';
											?>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Price<span class="text-red">*</span></label>
									<div class="col-sm-9">
										<div class="input-group date">
											<div class="input-group-addon"><i class="fa fa-inr"></i></div>
											<?=$this->Form->text('Plans.price', ['class' => 'form-control', 'placeholder' => 'Enter url key']);?>
										</div>
										<span class="text-red">
											<?php
												echo $error['price']['_empty'] ?? '';
												echo $error['price']['charNum'] ?? '';
											?>
										</span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Duration </label>
									<div class="col-sm-9">
										<?=$this->Form->select('Plans.duration', $this->SubscriptionManager->planDuration, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Status <span class="text-red">*</span></label>
									<div class="col-sm-9">
										<?=$this->Form->select('Plans.is_active', $this->SubscriptionManager->siteStatus, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
										<span class="text-red"><?=isset($error['is_active']['inList']) ? $error['is_active']['inList'] : null;?></span>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Picture</label>
									<div class="col-sm-9">
										<?=$this->Form->text('Plans.image', ['class' => 'form-control', 'placeholder' => 'Enter image link']);?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">Description</label>
									<div class="col-sm-9">
										<?=$this->Form->textarea('Plans.description', ['id'=>'description', 'class' => 'form-control', 'placeholder' => 'Enter enter description']);?>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-9 text-center">
										<button type="submit" class="btn btn-div-buy btn-1b">Save</button>
									</div>
								</div>
							</div>
						</div><!-- end of box_content -->
					</div><!-- end of box_div -->
				</div><!-- end of col_div -->

			</div><!-- end of middle_content -->
		<?=$this->Form->end();?>
    </div><!-- end of tab -->
</section>
<?=$this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js')?>
<script>
	CKEDITOR.replace('description');
</script>