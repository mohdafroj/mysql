<?php echo $this->Element('Admin/Customers/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->                    
		<?php echo $this->Element('Admin/Customers/left_menu');?>
        <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
		
            <div class="in active tab-pane fade col-sm-12 col-xs-12" id="tab_1"><!-- Customer Profile_1 --> 
			<?= $this->Form->create($customer, ['class' => 'form-horizontal', 'novalidate' => true]); ?>
                <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="box-header with-border"><!-- start of box_heading -->
                                <h3 class="box-title">Account Information</h3>
                            </div><!-- end of box_heading -->
                                    
                            <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
							
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Name:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= h($customer->firstname) ?> <?= h($customer->lastname) ?></p>
                                </div>
                                        
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Email:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><a href="mailto:<?= h($customer->email) ?>" class="text-cgreen"><?= h($customer->email) ?></a></p>
                                </div>
                                        
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Mobile:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= h($customer->mobile) ?></p>
                                </div>
                                        
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Date of Birth:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= $this->Admin->emptyDate($customer->dob) ?></p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Vouchers:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= $this->Admin->priceLogo ?><?=VOUCHER_501?>(<?= $customer->voucher_amount%100 ?>) and <?= $this->Admin->priceLogo ?><?=VOUCHER_100?>(<?= ($customer->voucher_amount - VOUCHER_501*($customer->voucher_amount%100))/100 ?>)</p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Voucher Amount:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= $this->Admin->priceLogo ?> <?= $customer->voucher_amount ?></p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">PB Cash Amount:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= $this->Admin->priceLogo ?> <?= $customer->pb_cash ?></p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">PB Point:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= $customer->pb_points ?></p>
                                </div>
                                
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
                            
                    <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="box-header with-border">&nbsp;</div>
                            <div class="col-sm-12 col-xs-12 flex_box_content"><!-- start of box_content -->
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Created at:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= h($this->Admin->emptyDate($customer->created)); ?></p>
                                </div>
                                        
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Modified at:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= h($this->Admin->emptyDate($customer->modified)); ?></p>
                                </div>
                                        
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Last Login:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= h($this->Admin->emptyDate($customer->logdate)); ?></p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Login Nums:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= $this->Number->format($customer->lognum) ?></p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Track Page:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?= $customer->track_page ?></p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Membership:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?php echo ($member['error']) ? $member['error']:NULL; ?></p>
                                </div>
                                
                                <div class="col-sm-12 col-xs-12 no-padding">
                                    <p class="col-sm-5 col-xs-6 no-padding-left xs-no-padding">Validity:</p>
                                    <p class="col-sm-7 col-xs-6 no-padding-right xs-no-padding"><?php echo ($member['status']) ? $this->Admin->emptyDate($member['validity']):NULL; ?></p>
                                </div>
                                
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
                            
                    <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="box-header with-border"><h3 class="box-title">Change Information</h3></div>
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">First Name</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('Customers.firstname', ['class'=>'form-control', 'placeholder'=>'Enter firstname']); ?>
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
										<label class="col-sm-3 control-label">Last Name</label>
										<div class="col-sm-9">
											<?= $this->Form->text('Customers.lastname', ['class'=>'form-control', 'placeholder'=>'Enter lastname']); ?>
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
										<label class="col-sm-3 control-label">Email <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('Customers.email', ['class'=>'form-control', 'placeholder'=>'Enter Email Id']); ?>
											<span class="text-red">
												<?php
													echo $error['email']['_empty'] ?? NULL; 
													echo $error['email']['email'] ?? NULL;
													echo $error['email']['unique'] ?? NULL;
												?>
											</span>
										</div>
									</div>
                                    
									<div class="form-group">
										<label class="col-sm-3 control-label">Mobile <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->text('Customers.mobile', ['class'=>'form-control', 'placeholder'=>'Enter 10 digit mobile number']); ?>
											<span class="text-red">
												<?php
													echo $error['mobile']['_empty'] ?? NULL; 
													echo $error['mobile']['length'] ?? NULL; 
													echo $error['mobile']['unique'] ?? NULL; 
												?>
											</span>
										</div>
									</div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Address</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->textarea('Customers.address', ['class'=>'form-control', 'placeholder'=>'Enter address']); ?>
											<span class="text-red"><?= $error['address']['_empty'] ?? NULL; ?></span>
                                        </div>
                                    </div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label">Location <span class="text-red">*</span></label>
										<div class="col-sm-9">
                                        	<?= $this->Form->select('Customers.location_id', $locList, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
										</div>
									</div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">OTP</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->password('Customers.password', ['class'=>'form-control', 'value'=>'', 'placeholder'=>'Change OTP']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Confirm OTP</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->password('confirm_password', ['class'=>'form-control', 'value'=>'', 'placeholder'=>'Enter confirm OTP']); ?>
											<span class="text-red"><?= (isset($error['password']['match'])) ? $error['password']['match']:null; ?></span>
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
					
                    <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                        <div class="box"><!-- start of box_div -->
                            <div class="box-header with-border">&nbsp;</div>                                    
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
									
									<div class="form-group">
										<label class="col-sm-3 control-label">Profession </label>
										<div class="col-sm-9">
											<?= $this->Form->text('Customers.profession', ['class'=>'form-control', 'placeholder'=>'Enter profession']); ?>
										</div>
									</div>
                                    
									<div class="form-group">
                                        <label class="col-sm-3 control-label">City</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('Customers.city', ['class'=>'form-control', 'placeholder'=>'Enter city']); ?>
											<span class="text-red">
												<?php
													echo $error['city']['_empty'] ?? NULL; 
													echo $error['city']['length'] ?? NULL; 
												?>
											</span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Pincode</label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->text('Customers.pincode', ['class'=>'form-control','placeholder'=>'Enter pincode']); ?>
											<span class="text-red">
												<?php
													echo $error['pincode']['_empty'] ?? NULL; 
													echo $error['pincode']['length'] ?? NULL; 
												?>
											</span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Group <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->select('Customers.is_group', $this->Admin->customerGroup, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Gender <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->select('Customers.gender', $this->Admin->siteGender, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Status <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?= $this->Form->select('Customers.is_active', $this->Admin->customerStatus, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Valid Email:</label>
                                        <div class="col-sm-3 radio">
                                        	<?= $this->Form->radio('Customers.valid_email', [['value'=>'0','text'=>'NO','id'=>'valid_email_no']],['hiddenField'=>false]); ?>
                                        </div>
                                        <div class="col-sm-6 radio">
                                        	<?= $this->Form->radio('Customers.valid_email', [['value'=>'1','text'=>'YES','id'=>'valid_email_yes']],['hiddenField'=>false]); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Newsletter:</label>
                                        <div class="col-sm-3 radio">
                                        	<?= $this->Form->radio('Customers.newsletter', [['value'=>'0','text'=>'NO','id'=>'newsletter_no']],['hiddenField'=>false]); ?>
                                        </div>
                                        <div class="col-sm-6 radio">
                                        	<?= $this->Form->radio('Customers.newsletter', [['value'=>'1','text'=>'YES','id'=>'newsletter_yes']],['hiddenField'=>false]); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Buy Refill:</label>
                                        <div class="col-sm-3 radio">
                                        	<?= $this->Form->radio('Customers.scentshot_active', [['value'=>'0','text'=>'NO','id'=>'scentshot_active_no']],['hiddenField'=>false]); ?>
                                        </div>
                                        <div class="col-sm-6 radio">
                                        	<?= $this->Form->radio('Customers.scentshot_active', [['value'=>'1','text'=>'YES','id'=>'scentshot_active_yes']],['hiddenField'=>false]); ?>
                                        </div>
                                    </div>

                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
                </div><!-- end of middle_content -->
            <?= $this->Form->end(); ?>
            </div><!-- end of profile -->
			
              
        </div><!-- end of right_part -->
            
    </div><!-- end of tab -->
</section>