<!-- Main content -->
<section class="content col-sm-12 col-xs-12">

    <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->

        <?=$this->Form->create(null, ['enctype' => 'multipart/form-data', 'id' => 'submit_form_data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
            <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->

                <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">

                                <div class="form-group">
                                    <label class="control-label">Enter Pincodes as commas seperated, where we are not provided service! Default should be 000000</label>
								</div>
                                    
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <?= $this->Form->textarea('pincodes', ['value'=>$pincodes, 'class'=>'form-control', 'rows'=>'30', 'placeholder'=>'Enter meta description']); ?>
                                    </div>
                                </div>

                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->

                <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                    <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" id="saveData" class="btn btn-div-buy btn-1b btn-sm-long">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->

            </div><!-- end of middle_content -->
<?php
echo $this->Form->end();
?>


    </div><!-- end of tab -->

</section>
    <!-- /.content -->

