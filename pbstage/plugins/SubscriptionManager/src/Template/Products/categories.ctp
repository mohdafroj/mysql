<?php echo $this->Element('Products/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<?php echo $this->Element('Products/sub_menu');?>
			<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
			<?= $this->Form->create(null, ['enctype'=>'multipart/form-data', 'id'=>'categoryForm','class'=>'form-horizontal','novalidate' => true]); ?>
				<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body"><style> ul.treeview-menu { background: #fff !important; }</style>
						<?php 
							function cateFun($cateTreeItem, $categories, $data=''){
								if( count($cateTreeItem) ){
									foreach ($cateTreeItem as $chilName) {
										$child = count($chilName->children);
										$checked = in_array($chilName->id, $categories) ? 'checked="checked"':NULL;
										if( $child > 0 ){
											echo '<li class="treeview active"><a href=""><label><input type="checkbox" value="'.$chilName->id.'" class="minimal" '.$checked.' /></label> '.$chilName->name.' ('.$child.')<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a><ul class="treeview-menu menu-open" style="display:block;">';
											cateFun($chilName->children, $categories, $data);
											echo '</ul></li>';
										}else{
											echo '<li><input type="checkbox" value="'.$chilName->id.'" class="minimal" '.$checked.' /> '.$chilName->name.'</li>';
										}
									}
								}
							}			
						?>
									<div class="form-group sidebar tree_div">
										<ul class="sidebar-menu"><?php echo cateFun($cateTree, $categoriesIds); ?></ul>										
									</div>
									<?= $this->Form->text("chkd_ids", ['value'=>NULL, 'id'=>'chkd_ids', 'style'=>'width:0px;height:0px;']); ?>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-10">
                                            <button id="submitCategory" type="button" class="btn btn-div-buy btn-1b">Save</button>
                                        </div>
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
<!---
https://www.looksgud.in/blog/top-10-watch-brands-in-india-list/
https://www.oliotr.com/tag-heuer-pendulum-mens-automatic-watch?gclid=Cj0KCQjw0K7NBRC7ARIsAEaqLRH81YDpQCIaOgRwGz_gCORVIxioMSXQzvXZm33GzkN9WYQYX-H4FT8aAgzKEALw_wcB
https://www.amazon.in/Fastrack-Party-Analog-Black-Watch/dp/B009VPA6QS/ref=sr_1_43?s=watches&ie=UTF8&qid=1504508604&sr=1-43&nodeID=1350387031&psd=1&keywords=fastrack+man+watch
---->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript">
	$(document).ready(function (){
		$("#submitCategory").on("click", function(){
			var checks = $("input:checkbox:checked").map(function(){
				return $(this).val();
			}).get();
			$("#chkd_ids").val(checks);
			$("#categoryForm").submit();
		});
		var checks = $("input:checkbox:checked").map(function(){
			return $(this).val();
		}).get();
		$("#chkd_ids").val(checks);
   });
</script>				
