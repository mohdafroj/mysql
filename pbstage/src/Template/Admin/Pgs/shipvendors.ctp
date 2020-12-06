<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Shipping Vendors') ?></h3>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
        <?= $this->Form->create(null, ['type'=>'post']) ?>        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th class="text-center" width="10%">S. No.</th>
                        <th>Title</th>
                        <th class="text-center" width="10%">Set Default</th>
                        <th class="text-center" width="15%">Created</th>
                    </tr>
                </thead>                
                <tbody>                    
           <?php foreach ($vendorList as $value):?>
                    <tr>
                        <td class="text-center" data-title="Id"><?= $value['id'] ?></td>
                        <td data-title="Title"><?= $value['title'] ?></td>
                        <td class="text-center" data-title="Default"><input type="radio" name="setDefault" onchange="changeDefault();" value="<?= $value['id'] ?>" style="cursor:pointer;" <?php echo $value['set_default'] ? 'checked':''; ?> ></td>
                        <td class="text-center" data-title="Created"><?= $this->Admin->emptyDate($value['created']); ?></td>
                    </tr>
           <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
        <div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of table -->
		    <?= $this->Form->create(null, ['type'=>'post', 'enctype' => 'multipart/form-data']) ?>
                <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->                            
                                <div class="box-body">
                                <div class="form-group">
										<label class="col-sm-3 control-label"></label>
										<div class="col-sm-2">
                                        	<?= $this->Form->select('setAction', ['0'=>'Download', '1'=>'Upload'], ['empty'=>false, 'escape'=>false, 'class'=>'form-control', 'style'=>'width: 90%;'])?>
										</div>
										<div class="col-sm-2">
                                            <?= $this->Form->select('vendorId', ['1'=>'Delhivery', '3'=>'Shiprocket'], ['empty'=>false, 'class'=>'form-control', 'style'=>'width: 90%;'])?>
										</div>
										<div class="col-sm-5">
                                            <?= $this->Form->file('pincodes', ['class'=>'form-control']); ?>
										</div>
									</div>
                                    <div class="form-group">
                                        <label class="col-sm-12 control-label text-right">&nbsp;</label>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            <button type="submit" class="btn btn-div-buy btn-1b">Submit</button>
                                        </div>
                                    </div>
                                                                        
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            Only one columns - pincode
                                        </div>
                                    </div>
                                                                        
                                </div>
                            
                        </div><!-- end of box_div -->
                    </div><!-- end of col_div -->
					
				</div><!-- end of middle_content -->
            <?= $this->Form->end() ?>
        </div><!-- end of table -->
        </div>
</section>

<script>
    function changeDefault(){
        var form = document.forms[0];
        form.submit();
    }
</script>

