<?php echo $this->Element('Products/top_menu'); ?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
			<?=$this->Form->create(null, ['enctype' => 'multipart/form-data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            File contain following column like column "A" for "Product Id", column "B" for "Cross Price", column "C" for "Price", column "D" for "Tier1 Price", column "E" for "Tier2 Price" and column "F" for "Tier3 Price".
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label class="col-sm-2 control-label">Check for update-</label>
                                    <?php foreach ($allColumn as $key=>$col) {?>
                                            <label class="col-sm-1 control-label"><?php echo ucfirst($col) ?>-</label>
                                            <div class="col-sm-1">
                                                <?=$this->Form->checkbox("prices[$key]", ['value'=>$col, 'hiddenField'=>false]);?>
                                            </div>
                                    <?php }?>
                                        </div>
                                    </div>        									
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label class="col-sm-2 control-label">Choose file(csv)<span class="text-red">*</span></label>
                                            <div class="col-sm-10">
                                                <?=$this->Form->file('name', ['class' => 'form-control', 'placeholder' => 'Enter name']);?>
                                            </div>
                                        </div>
                                    </div>        									
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-3">
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
        </div><!-- end of right_part -->
    </div><!-- end of tab -->
</section>
