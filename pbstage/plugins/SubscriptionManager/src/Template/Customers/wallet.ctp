<?php echo $this->Element('Customers/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->                    
		<?php echo $this->Element('Customers/left_menu');?>

        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
			<div class="tab-pane fade col-sm-12 col-xs-12 active in" id="tab_5"><!-- Wallet -->
			<?php if( in_array($this->request->session()->read('Auth.User.username'), ['developer','admin']) ){ 
				echo $this->Form->create($customerLogNew, ['type'=>'post','class' => 'form-horizontal', 'novalidate' => true]); ?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top">
					<div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding">
						<div class="box box-default"><!-- start of box_div -->
						<div class="box-header with-border text-center"><h3 class="box-title text-bold">Add Data into Wallet</h3></div>
                            
						<div class="col-sm-6 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Order Number<span class="text-red">*</span></label>
                                        <div class="col-sm-8">
                                        	<?= $this->Form->number('order_id', ['class'=>'form-control', 'placeholder'=>'Enter order number']); ?>
											<span class="text-red">
												<?php
													echo $error['order_id']['_empty'] ?? NULL;
												?>
											</span>
                                        </div>
                                    </div>
									
									<div class="form-group">
										<label class="col-sm-4 control-label">Transaction</label>
										<div class="col-sm-8">
											<?= $this->Form->select('transaction_type', ['0'=>'Debit', '1'=>'Credit'], ['class'=>'form-control']); ?>
										</div>
									</div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Reason<span class="text-red">*</span></label>
                                        <div class="col-sm-8">
                                        	<?= $this->Form->textarea('comments', ['class'=>'form-control', 'rows'=>3, 'placeholder'=>'Enter address']); ?>
											<span class="text-red">
                                                <?php 
                                                    echo $error['comments']['_empty'] ?? NULL;
                                                 ?>
                                            </span>
                                        </div>
                                    </div>
									
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- end of box_content -->

							<div class="col-sm-6 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">

									<div class="form-group">
										<label class="col-sm-4 control-label">PB Cash</label>
										<div class="col-sm-8">
											<?= $this->Form->text('cash', ['class'=>'form-control', 'placeholder'=>'Enter cash']); ?>
										</div>
									</div>
                                    
									<div class="form-group">
										<label class="col-sm-4 control-label">PB Points</label>
										<div class="col-sm-8">
											<?= $this->Form->text('points', ['class'=>'form-control', 'placeholder'=>'Enter points']); ?>
										</div>
									</div>
                                    
									<!--div class="form-group">
										<label class="col-sm-4 control-label">Voucher Amount</label>
										<div class="col-sm-8">
											<?= $this->Form->text('voucher', ['class'=>'form-control', 'placeholder'=>'Enter voucher amount']); ?>
										</div>
									</div-->
                                    
                                </div>
                            </div><!-- end of box_content -->

							
                        </div><!-- end of box_div -->
					</div>
				</div>
				<?= $this->Form->end();
				 }
				 ?>	

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
								<?= $this->Html->link(__('Reset Filter'), ['action' => 'wallet', $customerId, 'key', md5($customerId)], ['class'=>'btn btn-div-cart btn-1e']) ?>
								<?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
							</div><!-- end of buttons -->						
						</div><!-- end of pagination or buttons -->
			
						<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
							<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
								<thead>
									<tr>
										<th>Order #</th>
										<th>Action on</th>
										<th style="width:95px;">Type</th>
										<th>Description</th>
										<th>Currency</th>
										<th>Voucher</th>
										<th>Points</th>
										<th>Cash</th>
									</tr>
								</thead>                
								<tbody>                    
									<tr><!-- start of row_1 -->										
										<td data-title="Transaction">
											<?= $this->Form->text('order_id', ['value'=>$orderId, 'class'=>'form-control', 'placeholder'=>'Order #']); ?>
										</td>
										
										<td data-title="ActionOn">
											<div class="input-group date">
												<div class="input-group-addon">From:<i class="fa fa-calendar"></i></div>
												<?= $this->Form->text('created_from', ['value'=>$createdFrom, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
											</div>
											<div class="input-group date">
												<div class="input-group-addon">To&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-calendar"></i></div>
												<?= $this->Form->text('created_to', ['value'=>$createdTo, 'id'=>'datepicker2', 'class'=>'form-control']); ?>
											</div>
										</td>
										
										<td data-title="Type">
											<?= $this->Form->select("transaction_type", ['1'=>'Credit', '0'=>'Debit'], ['value'=> $transactionType, 'default'=> '', 'empty'=> TRUE, 'style'=>'width:100%;', 'class'=>'form-control']);?>
										</td>
										<td data-title="Description">
											<?= $this->Form->text('comments', ['value' => $comments, 'class' => 'form-control', 'placeholder' => 'Description']); ?>
										</td>
										<td data-title="Currency"></td>
										<td data-title="Voucher">
											<?= $this->Form->text('voucher', ['value' => $voucher, 'class' => 'form-control', 'placeholder' => 'Voucher']); ?>
										</td>
										<td data-title="Points">
											<?= $this->Form->text('points', ['value' => $points, 'class' => 'form-control', 'placeholder' => 'Points']); ?>
										</td>
										<td data-title="Cash">
											<?= $this->Form->text('cash', ['value' => $cash, 'class' => 'form-control', 'placeholder'=>'Cash']); ?>
										</td>
									</tr><!-- end of row_1 -->
						
									<tr><!-- start of row_2 -->
										<?php foreach ($history as $value):?>
											<tr>
												<td data-title="Transaction"><?= $value->order_id ?></td>
												<td data-title="ActionOn"><?= h($this->SubscriptionManager->emptyDate($value->transaction_date)); ?></td>
												<td data-title="Type">
													<?= ($value->transaction_type == '0') ? 'Debit':'Credit' ?>
												</td>
												<td data-title="Description"><?= $value->comments ?></td>
												<td data-title="Currency"><label title="<?= $value->location->title ?>"><?= strtoupper($value->location->currency) ?></label></td>
												<td data-title="Voucher" class="text-right"><?= $value->location->currency_logo.' '.$this->Number->format($value->voucher, ['places' => 2]) ?></td>
												<td data-title="Points" class="text-right"><?= $this->Number->format($value->points, array('places' => 2)) ?></td>
												<td data-title="Cash" class="text-right"><?= $value->location->currency_logo.' '.$this->Number->format($value->cash, ['places' => 2]) ?></td>
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