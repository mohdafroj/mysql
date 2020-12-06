<?php echo $this->Element('Customers/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
        <div id="myTabContent" class="tab-content"><!-- start of right_part -->
            <div class="in active tab-pane fade col-sm-12 col-xs-12" id="tab_1"><!-- Customer Profile_1 -->
			<?=$this->Form->create($customer, ['class' => 'form-horizontal', 'novalidate' => true]);?>
                <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->

                    <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="box-header with-border"><h3 class="box-title">Create New Account</h3></div>
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">First Name <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?=$this->Form->text('Customers.firstname', ['class' => 'form-control', 'placeholder' => 'Enter firstname']);?>
											<span class="text-red">
												<?php
echo $error['firstname']['_empty'] ?? null;
echo $error['firstname']['length'] ?? null;
echo $error['firstname']['charNum'] ?? null;
?>
											</span>
                                        </div>
                                    </div>

									<div class="form-group">
										<label class="col-sm-3 control-label">Last Name <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->text('Customers.lastname', ['class' => 'form-control', 'placeholder' => 'Enter lastname']);?>
											<span class="text-red">
												<?php
echo $error['lastname']['_empty'] ?? null;
echo $error['lastname']['length'] ?? null;
echo $error['lastname']['charNum'] ?? null;
?>
											</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">Email <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->text('Customers.email', ['class' => 'form-control', 'placeholder' => 'Enter Email Id']);?>
											<span class="text-red">
												<?php
echo $error['email']['_empty'] ?? null;
echo $error['email']['email'] ?? null;
echo $error['email']['length'] ?? null;
echo $error['email']['unique'] ?? null;
?>
											</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label">Mobile <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?=$this->Form->text('Customers.mobile', ['class' => 'form-control', 'placeholder' => 'Enter 10 digit mobile number']);?>
											<span class="text-red">
												<?php
echo $error['mobile']['_empty'] ?? null;
echo $error['mobile']['length'] ?? null;
echo $error['mobile']['unique'] ?? null;
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

                    <div class="col-sm-6 col-xs-12 flex_box no-padding-right xs-no-padding"><!-- start of col_div -->
                        <div class="box"><!-- start of box_div -->
                            <div class="box-header with-border">&nbsp;</div>
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->

								<div class="form-group">
									<label class="col-sm-3 control-label">Gender <span class="text-red">*</span></label>
									<div class="col-sm-9">
										<?=$this->Form->select('Customers.gender', $this->Admin->siteGender, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label">Status <span class="text-red">*</span></label>
									<div class="col-sm-9">
										<?=$this->Form->select('Customers.is_active', $this->Admin->customerStatus, ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label">Password</label>
									<div class="col-sm-9">
										<?=$this->Form->password('Customers.password', ['class' => 'form-control', 'value' => '', 'placeholder' => 'Change password']);?>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label">Confirm Password</label>
									<div class="col-sm-9">
										<?=$this->Form->password('confirm_password', ['class' => 'form-control', 'value' => '', 'placeholder' => 'Enter confirm password']);?>
										<span class="text-red"><?=$error['password']['match'] ?? null;?></span>
									</div>
								</div>
                            </div><!-- end of box_content -->
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
                </div><!-- end of middle_content -->
            <?=$this->Form->end();?>
            </div><!-- end of profile -->


        </div><!-- end of right_part -->

    </div><!-- end of tab -->
</section>