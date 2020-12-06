<?php echo $this->Element('Admin/Customers/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->                    
		<?php echo $this->Element('Admin/Customers/left_menu');?>            
            
        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
			<div class="tab-pane fade col-sm-12 col-xs-12 active in" id="tab_5"><!-- Wallet -->
				<?= $this->Form->create(null, ['type'=>'get']) ?>
					<section class="content col-sm-12 col-xs-12">		
						<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
							<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
								<ul class="list-unstyled list-inline pagination_div">
									<li>
										Page
										<span class="span_1">
											<?= $this->Paginator->prev(__('Prev')) ?>
											<input type="text" class="form-control" value="<?= $this->Paginator->counter(['format'=>__('{{page}}')]) ?>">
											<?= $this->Paginator->next(__('Next')) ?>
										</span>
										of <?= $this->Paginator->counter(['format'=>__('{{pages}}')]) ?> pages
									</li>
									<li>
										View
										<span class="span_1 span_2">
											<?= $this->Form->select('limit', $this->Admin->selectMenuOptions, ['value'=>$this->Paginator->param('perPage'),'default'=>50,'empty' => FALSE,'onChange'=>'this.form.submit();','class'=>'form-control']);?>
										</span>
										per page
									</li>
									<li>Total <?= $this->Paginator->counter(['format'=>__('{{count}}')]) ?> records found</li>
								</ul>
							</div><!-- end of pagination -->
				
							<div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
								<?= $this->Html->link(__('Reset Filter'), ['action' => 'cart', $id, 'key', md5($id)], ['class'=>'btn btn-div-cart btn-1e']) ?>
								<?= $this->Form->button('Submit', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
							</div><!-- end of buttons -->						
						</div><!-- end of pagination or buttons -->
			
						<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
							<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
								<thead>
									<tr>
										<th></th>
										<th>SKU</th>
										<th>Title</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Added On</th>
									</tr>
								</thead>
								<tbody> 
									<tr><!-- start of row_1 -->
										<td data-title="Checked">
											
										</td>
										
										<td data-title="SKU">
											<?= $this->Form->text('sku', ['value'=>$sku, 'class'=>'form-control', 'placeholder'=>'Enter sku']); ?>
										</td>
										
										<td data-title="Title">
											<?= $this->Form->text('title', ['value'=>$title, 'class'=>'form-control', 'placeholder'=>'Enter title']); ?>
										</td>
										
										<td data-title="Quantity">
										</td>
										
										<td data-title="Price">
											<div class="input-group date">
												<div class="input-group-addon">From <?= $this->Admin->priceLogo?></div>
												<?= $this->Form->text('fromPrice', ['value'=>$fromPrice, 'class'=>'form-control']); ?>
											</div>
											<div class="input-group date">
												<div class="input-group-addon">To&nbsp;:&nbsp;&nbsp;&nbsp; <?= $this->Admin->priceLogo?></div>
												<?= $this->Form->text('toPrice', ['value'=>$toPrice, 'class'=>'form-control']); ?>
											</div>
										</td>
										
										<td data-title="ActionOn">
										</td>										
									</tr><!-- end of row_1 -->
						
									<tr><!-- start of row_2 -->
										<?php foreach ($cart as $value):?>
											<tr>
												<td data-title="Id">
												<?= $this->Form->hidden('allChecked[]', ['value'=>$value->id]); ?>
												<?= $this->Form->checkbox('checked[]', ['class'=>'minimal', 'value'=>$value->id, 'checked'=>$value->checked]); ?>
												</td>
												<td data-title="SKU"><?= $value->sku_code ?></td>
												<td data-title="Title"><?= $value->title ?></td>
												<td data-title="Quantity">
													<?php 
														echo  $value->_matchingData['Carts']->qty ?? NULL;
														echo  $value->qty ?? NULL;
													?>
												</td>
												<td data-title="Price"><?= $this->Admin->priceLogo.' '.$value->price ?></td>
												<td data-title="Action On">
													<?php
														$date = $value->_matchingData['Carts']->created ?? $value->created;
														echo $this->Admin->emptyDate($date);
													?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tr><!-- end of row_2 -->                    
								</tbody>
							</table>
						</div><!-- end of table -->
					</section>
				<?= $this->Form->end() ?>
			</div><!-- end of product review -->
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>