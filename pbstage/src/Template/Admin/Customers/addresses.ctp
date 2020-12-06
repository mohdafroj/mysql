<?php echo $this->Element('Admin/Customers/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->                    
		<?php echo $this->Element('Admin/Customers/left_menu');?>
        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->            
            <div class="in active tab-pane fade col-sm-12 col-xs-12"><!-- Customer Profile_1 --> 
                <h3 class="box-title">Addresses</h3>
                <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
            <?php foreach($addressesList as $value){?>    
                    <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12">
								<?php if( $value->set_default == '1' ){ ?>
									<p class="box-title btn btn-success">Default Address</p>
								<?php }else{
									 echo $this->Form->postLink(__('Click to Set Default Address'), ['action' => 'addresses', $id, 'key', md5($id)], ['block' => false, 'data'=>['address-id'=>$value->id], 'method'=>'patch', 'class' =>'btn btn-warning col-sm-4 col-xs-4 no-padding', 'confirm' => __('Are you sure you want to set default address # {0}?', $value->id)]);
								 } ?>
							</div>
                            <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <strong><?= h($value->firstname) ?> <?= h($value->lastname) ?></strong>, <?= h($value->address) ?>,
                                    <?= h($value->city) ?>, <?= h($value->state) ?>, <?= h($value->country) ?> (<?= h($value->pincode) ?>)
                                    <p>Email: <a href="mailto:<?= h($value->email) ?>" class="text-cgreen"><?= h($value->email) ?></a>, Mobile: <?= h($value->mobile) ?></p>
                                </div>
                                        
                                <div class="col-sm-12 col-xs-12 text-center">
                                    <?= $this->Html->link(__('Edit This Address'), ['action' => 'addresses', $id, 'key', md5($id),'?'=>['address-id'=>$value->id]],['class'=>'btn btn-default col-sm-4 col-xs-4 no-padding']) ?>
                                    <span class="col-sm-4 col-xs-4">&nbsp;</span>
                                    <?= $this->Form->postLink(__('Delete This Address'), ['action' => 'addresses', $id, 'key', md5($id)], ['block' => false, 'data'=>['address-id'=>$value->id], 'method'=>'delete', 'class' =>'btn btn-default col-sm-4 col-xs-4 no-padding', 'confirm' => __('Are you sure you want to delete this address # {0}?', $value->id)]) ?>
                                </div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
            <?php }?>
				</div><!-- end of middle_content -->            
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
				<?= $this->Form->create($address, ['class' => 'form-horizontal col-sm-12 col-xs-12', 'novalidate' => true]); ?>                                   
				<div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding">
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="box-header with-border"><h3 class="box-title">Add/Update Address</h3></div>
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Firstname<span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('firstname', ['class'=>'form-control', 'placeholder'=>'Enter firstname']); ?>
											<span class="text-red">
												<?php
													echo $error['firstname']['_empty'] ?? NULL; 
													echo $error['firstname']['length'] ?? NULL; 
													echo $error['firstname']['charNum'] ?? NULL; 
												?>
											</span>
                                        </div>
                                    </div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label">Lastname<span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('lastname', ['class'=>'form-control', 'placeholder'=>'Enter lastname']); ?>
											<span class="text-red">
												<?php
													echo $error['lastname']['_empty'] ?? NULL; 
													echo $error['lastname']['length'] ?? NULL; 
													echo $error['lastname']['charNum'] ?? NULL; 
												?>
											</span>
										</div>
									</div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Address<span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->textarea('address', ['class'=>'form-control','rows'=>'2', 'placeholder'=>'Enter address']); ?>
											<span class="text-red"><?= $error['address']['_empty'] ?? NULL; ?></span>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-3 control-label">City<span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('city', ['class'=>'form-control', 'placeholder'=>'Enter City/Town/District']); ?>
											<span class="text-red">
												<?php
													echo $error['city']['_empty'] ?? NULL; 
													echo $error['city']['length'] ?? NULL; 
													echo $error['city']['charNum'] ?? NULL; 
												?>
											</span>
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
                    <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding">
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
									<div class="form-group">
										<label class="col-sm-3 control-label">State<span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('state', $locationList, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Country<span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('country', ['India'=>'India'], ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Pincode<span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('pincode', ['class'=>'form-control', 'placeholder'=>'Enter Pincode/Zipcode']); ?>
											<span class="text-red">
												<?php
													echo $error['pincode']['_empty'] ?? NULL; 
													echo $error['pincode']['length'] ?? NULL; 
													echo $error['pincode']['charNum'] ?? NULL; 
												?>
											</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">Mobile<span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('mobile', ['class'=>'form-control', 'placeholder'=>'Enter mobile']); ?>
											<span class="text-red">
												<?php
													echo $error['mobile']['_empty'] ?? NULL; 
													echo $error['mobile']['length'] ?? NULL; 
													echo $error['mobile']['charNum'] ?? NULL; 
												?>
											</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">Email<span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('email', ['class'=>'form-control', 'placeholder'=>'Enter email id']); ?>
											<span class="text-red">
												<?php
													echo $error['email']['_empty'] ?? NULL;
													echo $error['email']['valid'] ?? NULL; 
													echo $error['email']['email'] ?? NULL; 
												?>
											</span>
										</div>
									</div>
                                </div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
				<?= $this->Form->end(); ?>
                </div><!-- end of middle_content -->                
            </div><!-- end of profile -->			              
        </div><!-- end of right_part -->            
    </div><!-- end of tab -->
</section>