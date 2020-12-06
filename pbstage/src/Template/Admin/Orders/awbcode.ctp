<!-- Content Header (Page header) -->
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3>
                Order # <?= h($order->order_number) ?> | <?= date('M d, Y a',strtotime($order->created)) ?>
            </h3>
            <ul class="list-inline list-unstyled">
                <li>
                    <button type="button" onClick="history.go(-1);" class="btn btn-div-cart btn-1e">Back</button>
                </li>
				<?php
				if($order->status == 'accepted')
				{
					?>
					<li>
						<?= $this->Html->link(__('Cancel Order'), ['action' => 'cancel', $order->id, 'key', md5($order->id)], ['onclick' => "return confirm('Are you sure that you want to cancel this order?')", 'class' => 'btn btn-div-buy btn-1b']) ?>
					</li>
					<?php
				}
				?>
				<?php
				if($order->status == 'accepted')
				{
					?>
					<li>
						<?= $this->Html->link(__('Delivered'), ['action' => 'delivered', $order->id, 'key', md5($order->id)], ['onclick' => "return confirm('Are you sure that you want to set the status of this order to Delivered?')", 'class' => 'btn btn-div-buy btn-1b']) ?>
					</li>
					<?php
				}
				?>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<!-- Main content -->
<section class="content col-sm-12 col-xs-12">		
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->  
            <?php echo $this->element('Admin/Orders/left_menu');?>                  
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->                
                    <div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
                <?= $this->Form->create(null, ['class' => 'form-horizontal', 'method'=>'post', 'id' => 'comment_order_form', 'autocomplete' => 'off']); ?>                            
                        <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->
                            
							<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Customer Name
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
												<?php echo $order['customer']['firstname'].' '.$order['customer']['lastname']; ?>
                                            </p>
                                        </div><!-- end of row_1 -->
                                        
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Email Id
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <a href="mailto:<?= $order['customer']['email'] ?>" class="text-cgreen"><?= $order['customer']['email'] ?></a>
                                            </p>
                                        </div><!-- end of row_2 -->
                                        
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_3 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Customer Group
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                 <?php echo ucfirst($order['customer']['is_group']); ?>
                                            </p>
                                        </div><!-- end of row_3 -->
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                AWB Number
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?= $order->tracking_code ?>
                                            </p>
                                        </div><!-- end of row_2 -->                                        
                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->
                            
                            <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Order Number
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?= ucfirst($order->id) ?>
                                            </p>
                                        </div><!-- end of row_2 -->                                        
                                        
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_1 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Order Date
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?= date('Y-m-d H:m:s',strtotime($order->created)) ?>
                                            </p>
                                        </div><!-- end of row_1 -->
                                        
                                        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of row_2 -->
                                            <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">
                                                Order Status
                                            </p>
                                            <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding">
                                                <?= ucfirst($order->status) ?>
                                            </p>
                                        </div><!-- end of row_2 -->                                        
                                        
                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->
                
                            <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                            
                                        <div class="form-group">
											<label class="col-sm-12">Shipping Details <span class="text-red">*</span></label>
										</div>
                                                                                        
										<div class="form-group">
											<label class="col-sm-3 control-label">First Name <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_firstname', ['class'=>'form-control', 'placeholder'=>'Enter firstname', 'id'=>'shipping_firstname', 'value' => $order->shipping_firstname]); ?>
											</div>
										</div>
                                                                                        
										<div class="form-group">
											<label class="col-sm-3 control-label">Last Name <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_lastname', ['class'=>'form-control', 'placeholder'=>'Enter lastname', 'id'=>'shipping_lastname', 'value' => $order->shipping_lastname]); ?>
											</div>
										</div>
                                            
										<div class="form-group">
											<label class="col-sm-3 control-label">Address <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->textarea('shipping_address', ['rows'=>3,'class'=>'form-control', 'placeholder'=>'Enter address', 'id'=>'shipping_address', 'value' => $order->shipping_address]); ?>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-sm-3 control-label">City <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_city', ['class'=>'form-control', 'placeholder'=>'Enter city name', 'id'=>'shipping_city', 'value' => $order->shipping_city]); ?>
											</div>
										</div>
                                                                                                                                    
										<div class="form-group">
											<label class="col-sm-3 control-label"></label>
											<div class="col-sm-9">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
											</div>
										</div>
                                                                                    
                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->
                            
                            <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->

										<div class="form-group">
											<label class="col-sm-3 control-label">State <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_state', ['class'=>'form-control', 'placeholder'=>'Enter state name', 'id'=>'shipping_state', 'value' => $order->shipping_state]); ?>
											</div>
										</div>
                                            
										<div class="form-group">
											<label class="col-sm-3 control-label">Country <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_country', ['class'=>'form-control', 'placeholder'=>'Enter country name', 'id'=>'shipping_country', 'value' => $order->shipping_country]); ?>
											</div>
										</div>
                                            
										<div class="form-group">
											<label class="col-sm-3 control-label">Pincode <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_pincode', ['class'=>'form-control', 'placeholder'=>'Enter pincode name', 'id'=>'shipping_pincode', 'value' => $order->shipping_pincode]); ?>
											</div>
										</div>
                                            
										<div class="form-group">
											<label class="col-sm-3 control-label">Email <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_email', ['class'=>'form-control', 'placeholder'=>'Enter email name', 'id'=>'shipping_email', 'value' => $order->shipping_email]); ?>
											</div>
										</div>
                                            
										<div class="form-group">
											<label class="col-sm-3 control-label">Mobile <span class="text-red">*</span></label>
											<div class="col-sm-9">
												<?= $this->Form->text('shipping_phone', ['class'=>'form-control', 'placeholder'=>'Enter mobile name', 'id'=>'shipping_phone', 'value' => $order->shipping_phone]); ?>
											</div>
										</div>
                                                                                    
                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->

                            <div class="col-sm-12 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                                <div class="box box-default"><!-- start of box_div -->
                                    <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->

                                    <div class="form-group">
											<div class="col-sm-12">
                                                <?php 
                                                    $a = unserialize($order->delhivery_response);
                                                    $a = $a['payload'] ?? [];
                                                    pr($a);
                                                ?>
											</div>
										</div>
                                            
										<div class="form-group">
											<div class="col-sm-12">
                                                <?php 
                                                    $a = $order->pg_response->pg_data ?? '';
                                                    $a = !empty($a) ? json_decode($a) : [];
                                                    pr($a);
                                                ?>
											</div>
										</div>
                                            
                                    </div><!-- end of box_content -->
                                </div><!-- end of box_div -->
                            </div><!-- end of col_div -->
                        </div><!-- end of middle_content -->                        
                <?= $this->Form->end(); ?>            
                    </div><!-- end of content_1 -->                    						
            </div><!-- end of right_part -->
            
        </div><!-- end of tab -->
    </section>
