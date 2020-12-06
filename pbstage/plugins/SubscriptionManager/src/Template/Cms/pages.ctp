<?php echo $this->element('Admin/Cms/top_menu');
$conditions = json_decode($cms->conditions, true) ?? [];
$categoryIds = $conditions['categories'] ?? [];
$brandIds = $conditions['brands'] ?? [];
$prices = $conditions['prices'] ?? [];
$sku = $conditions['sku'] ?? '';
$start = $prices[0] ?? 0;
$end = $prices[1] ?? 5000;

?>
<?=$this->Html->css('/admin/treeview/jquery.treeview.css')?>
<?=$this->Html->css('/admin/plugins/bootstrap-slider/slider.css')?>
<?=$this->Html->script('/admin/treeview/jquery.treeview.js')?>

<!-- Main content -->
<section class="content col-sm-12 col-xs-12">

    <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->

        <?=$this->Form->create($cms, ['enctype' => 'multipart/form-data', 'id' => 'submit_form_data', 'class' => 'form-horizontal', 'novalidate' => true]);?>
            <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->

                <div class="col-sm-7 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">

                            <div class="form-group">
                                    <label for="forMailerTitle" class="col-sm-3 control-label">Name</label>
                                    <div class="col-sm-9">
                                        <?=$this->Form->text('name', ['id'=>'name', 'class' => 'form-control', 'placeholder' => 'Enter page name']);?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="forMailerTitle" class="col-sm-3 control-label">Title <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?=$this->Form->text('title', ['id'=>'title','class' => 'form-control', 'placeholder' => 'Enter title']);?>
                                        <span class="text-red titleError">
										    <?php
echo $errors['title']['_empty'] ?? null;
echo $errors['title']['length'] ?? null;
echo $errors['title']['charNum'] ?? null;
?>
										</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="forMailerTitle" class="col-sm-3 control-label">Url key <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?=$this->Form->text('url_key', ['id'=>'url_key','class' => 'form-control', 'placeholder' => 'Enter url key']);?>
                                        <span class="text-red urlKeyError">
										    <?php
echo $errors['url_key']['_empty'] ?? null;
echo $errors['url_key']['charNum'] ?? null;
echo $errors['url_key']['urlKey'] ?? null;
?>
										</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="forMailerTitle" class="col-sm-3 control-label">Page Image</label>
                                    <div class="col-sm-9">
                                        <?=$this->Form->text('image', ['id'=>'image','class' => 'form-control', 'placeholder' => 'Enter image link only']);?>
                                        <span class="text-red urlKeyError">
										    <?php echo $errors['image']['length'] ?? null; ?>
										</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Meta Title</label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->text('meta_title', ['class'=>'form-control', 'placeholder'=>'Enter meta title']); ?>
                                    </div>
								</div>
                                    
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Meta Keyword</label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->textarea('meta_keyword', ['class'=>'form-control', 'placeholder'=>'Enter meta keywords']); ?>
                                    </div>
                                </div>
									
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Meta Description</label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->textarea('meta_description', ['class'=>'form-control', 'placeholder'=>'Enter meta description']); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="forMailerDiscountValue" class="col-sm-3 control-label">AMP Page</label>
                                    <div class="col-sm-9">
                                        <?=$this->Form->select('is_amp', ['No', 'Yes'], ['value' => $cms->is_amp, 'style' => 'cursor:pointer;', 'class' => 'form-control'])?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="forMailerDiscountValue" class="col-sm-3 control-label">Status <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?=$this->Form->select('is_active', ['active' => 'Active', 'inactive' => 'Inactive'], ['value' => $cms->is_active, 'style' => 'width:100%; cursor:pointer;', 'class' => 'form-control'])?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <?=$this->Form->textarea('content', ['value' => $cms->content, 'id' => 'content', 'class' => 'form-control', 'placeholder' => 'Enter html code here']);?>
                                        <span class="text-red">
										    <?php
echo $errors['content']['_empty'] ?? null;
echo $errors['content']['length'] ?? null;
?>
										</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                    <p class="text-red">Please set reference variable {{PageTitle}} for product list!</p>
                                    </div>
                                </div>

                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->

                <div class="col-sm-5 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                    <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="forConditions" class="col-sm-12 text-center bg-success text-success">Set Conditions: Price, Categories and Brands are operate by AND operator.</label>
                                </div>
                                <div class="form-group">
                                    <label for="forPriceRange" class="col-sm-3 control-label">Price:</label>
                                    <div class="col-sm-9">
                                        <div class="input-group date" style="width:50%;float:left;margin-right:1%;">
                                            <div class="input-group-addon">From:</div>
                                            <?= $this->Form->text('start', ['value'=>$start,'class'=>'form-control','style'=>'width:100%;']); ?>
                                        </div>
                                        <div class="input-group date" style="width:49%;">
                                            <div class="input-group-addon">To:</div>
                                            <?= $this->Form->text('end', ['value'=>$end,'class'=>'form-control','style'=>'width:100%;']); ?>
                                        </div>                                    
                                    </div>
                                </div>    
                                <div class="form-group">
                                    <label for="forCategories" class="col-sm-3 control-label">Categories:</label>
                                    <div id="treeviewCategories"  class="col-sm-9"></div>
                                    <span id="selectedCategories">
                                <?php 
                                    foreach($categoryIds as $value){
                                        echo '<input type="hidden" name="categories[]" value="'.$value.'" />';
                                    }
                                ?>    
                                    </span>
                                </div>

                                <div class="form-group">
                                    <label for="forBrands" class="col-sm-3 control-label">Brands:</label>
                                    <div class="col-sm-9">
                                        <?php foreach($brands as $value){?>
                                            <p><input type="checkbox" name="brands[]", value="<?=$value['id']?>" <?= in_array($value['id'], $brandIds) ? 'checked':''; ?> /> <?=$value['title']?></p>
                                        <?php }?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="forBrands" class="col-sm-3 control-label">Product SKU:</label>
                                    <div class="col-sm-9">
                                    <?=$this->Form->textarea('sku', ['value' => $sku, 'class' => 'form-control', 'placeholder' => 'Enter comma separated sku s1,s2,s3 ']);?>
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

<?=$this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js')?>
<script>
	CKEDITOR.replace( 'content', {
    allowedContent: true,
    autoGrow_onStartup: true,
    enterMode: CKEDITOR.ENTER_BR
	});
    CKEDITOR.config.height = 500;
    CKEDITOR.config.uiColor = '#38B8BF';
</script>
<script type="text/javascript">
    let savedCategories = <?=json_encode($categoryIds)?>;
    let treeCategororyObject = <?=json_encode($categories)?>;
    treeCategororyObject = JSON.stringify(treeCategororyObject); 
    savedCategories.map(function(value){
        treeCategororyObject = treeCategororyObject.replace('"id":'+value+',', '"id":'+value+',"checked":true,');
    });
    treeCategororyObject = JSON.parse(treeCategororyObject);
	var tw = new TreeView(treeCategororyObject, {showAlwaysCheckBox:true,fold:false});
    document.getElementById("treeviewCategories").appendChild( tw.root);
$(document).ready(function(){
    $("#treeviewCategories").on("click", function(){
        let categoryStr = '';
        $("#treeviewCategories").find("span.item").map(function(){
            if(this.checked == 1){
                categoryStr += '<input type="hidden" name="categories[]" value="'+this.data.id+'" />'; 
            }
        });
        $("#selectedCategories").html(categoryStr);
        console.log(categoryStr);
    });
});
</script>
