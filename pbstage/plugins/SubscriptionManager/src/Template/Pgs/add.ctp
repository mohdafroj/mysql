<?php
echo $this->Element('Pgs/top_menu');
?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
			<?=$this->Form->create($pg, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->

                                <div class="box-body">
									<div class="form-group">
                                        <label class="col-sm-12 control-label"><span class="text-red">Add Payment Gateway Method</span></label>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Title <span class="text-red">*</span></label>
                                        <div class="col-sm-9">
                                        	<?=$this->Form->text('title', ['class' => 'form-control', 'placeholder' => 'Enter title']);?>
											<span class="text-red">
												<?php
echo $error['title']['_empty'] ?? null;
echo $error['title']['length'] ?? null;
echo $error['title']['charNum'] ?? null;
?>
											</span>
                                        </div>
                                    </div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Code <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<?= $this->Form->select('code', $this->SubscriptionManager->paymentGateway, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Fees <span class="text-red">*</span></label>
										<div class="col-sm-9">
											<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
												<div class="input-group-addon"><?=$this->SubscriptionManager->priceLogo?></div>
												<?=$this->Form->text('fees', ['class' => 'form-control', 'style' => 'width:100%', 'placeholder' => 'Enter price']);?>
												<span class="text-red">
													<?php
echo $error['fees']['_empty'] ?? null;
echo $error['fees']['fees'] ?? null;
?>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Status</label>
										<div class="col-sm-9">
                                        	<?=$this->Form->select('status', ['1' => 'Active', '0' => 'Inactive'], ['empty' => false, 'style' => 'width:100%;', 'class' => 'form-control'])?>
											<span class="text-red"><?= $error['status']['inList'] ?? null;?></span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label">Message:</label>
										<div class="col-sm-9">
											<?= $this->Form->textarea('message', ['style'=>'width:100%;', 'placeholder'=>'Please enter popup message', 'class'=>'form-control'])?>
										</div>
									</div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
                                        </div>
                                    </div>

                                </div>

                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->

				</div><!-- end of middle_content -->
            <?=$this->Form->end();?>
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>
