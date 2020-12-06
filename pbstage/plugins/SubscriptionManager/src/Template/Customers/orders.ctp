<?php echo $this->Element('Customers/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->                    
		<?php echo $this->Element('Customers/left_menu');?>            
        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
			<div class="tab-pane fade col-sm-12 col-xs-12 active in" id="tab_3"><!-- Orders -->
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
								<?= $this->Html->link(__('Reset Filter'), ['action' => 'orders', $customerId, 'key', md5($customerId)], ['class'=>'btn btn-div-cart btn-1e']) ?>
								<?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
							</div><!-- end of buttons -->						
						</div><!-- end of pagination or buttons -->
			
						<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
							<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
								<thead>
									<tr>
										<th>Order #id</th>
										<th>Purchase on</th>
										<th>Ship Name</th>
										<th>Country</th>
										<th>Total Amount</th>
										<th>Action</th>
									</tr>
								</thead>                
								<tbody>                    
									<tr><!-- start of row_1 -->
										<td data-title="Order #id">
											<div class="input-group date">
												<div class="input-group-addon">PC</div>
												<?= $this->Form->text('order_id', ['value' => $orderId, 'class' => 'form-control', 'placeholder' => 'Resource']); ?>
											</div>
										</td>
										
										<td data-title="Purchase on">
											<div class="input-group date">
												<div class="input-group-addon">From:<i class="fa fa-calendar"></i></div>
												<?= $this->Form->text('created_from', ['value'=>$createdFrom, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
											</div>
											<div class="input-group date">
												<div class="input-group-addon">To&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-calendar"></i></div>
												<?= $this->Form->text('created_to', ['value'=>$createdTo, 'id'=>'datepicker2', 'class'=>'form-control']); ?>
											</div>
										</td>
										
										<td data-title="Ship Name">
											<?= $this->Form->text('ship_name', ['value' => $ship_name, 'class' => 'form-control', 'placeholder' => 'Ship Name']); ?>
										</td>
										
										<td data-title="Country"></td>
										
										<td data-title="Total Amount">
											<div class="input-group date">
												<div class="input-group-addon">From:</div>
												<?= $this->Form->text('from_amount', ['value' => $from_amount, 'class' => 'form-control pull-right', 'placeholder' => '']); ?>
											</div>
											<div class="input-group date">
												<div class="input-group-addon">To&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
												<?= $this->Form->text('to_amount', ['value' => $to_amount, 'class' => 'form-control pull-right', 'placeholder' => '']); ?>
											</div>
										</td>
										<td data-title="Action">&nbsp;</td>
									</tr><!-- end of row_1 -->
									
									<?php foreach ($orders as $value):?>
										<tr><!-- start of row_2 -->
											<td data-title="Order #id">
												PC<?= $value->id ?>
											</td>
											<td data-title="Purchase on">
												<?= $this->SubscriptionManager->emptyDate($value->created);?>
											</td>
											<td data-title="Ship Name">
												<?= $value->shipping_firstname.' '.$value->shipping_lastname ?>
											</td>
											<td data-title="Country">
												<label title="<?= strtoupper($value->location->currency) ?>"><?= strtoupper($value->location->title) ?></label>
											</td>
											<td data-title="Amount" class="text-right">
												<?= $value->location->currency_logo.' '.$this->Number->format($value->payment_amount, array('places' => 2)) ?>
											</td>
											<td data-title="Action">
												<?= $this->Html->link(__('View'), ['controller' => 'Orders', 'action' => 'view', $value->id, 'key', md5($value->id)]) ?>
											</td>
										</tr><!-- end of row_2 -->
									<?php endforeach; ?>
								</tbody>
							</table>
						</div><!-- end of table -->
					</section>
				<?= $this->Form->end() ?>
			</div><!-- end of orders -->
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>