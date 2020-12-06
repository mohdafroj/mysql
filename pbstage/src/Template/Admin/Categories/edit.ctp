<?php
/**
  * @var \App\View\AppView $this
  * @var \App\Model\Entity\Customer $customer
  */
?>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Categories Information') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('Back'), ['controller' =>'Categories/'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
                <li><?= $this->Html->link(__('Add New'), ['controller' =>'Categories/'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
                <li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $categories['id']], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $categories['id'])]) ?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	
    <div class="col-sm-2 col-xs-12 sidebar tree_div"><!-- start of left_part -->
		<?php
			$path = $this->Url->build('/admin/categories/edit/');
			function RecursiveCategories($cateTreeItem, $livePath, $id, $data=''){
				if( count($cateTreeItem) ){
					if( empty($data) ){
						echo '<ul class="sidebar-menu">';
						$data = 'used';
					}else{
						echo '<ul class="treeview-menu">';
					}
					foreach ($cateTreeItem as $chilName) {
						$child = count($chilName->children);
						if( $child > 0 ){
							echo '<li class="treeview active"><a href="#"><label onClick="location='."'".$livePath.$chilName->id.'/key/'.md5($chilName->id)."'".'" style="cursor:pointer;"><i class="fa fa-folder-o"></i> '.$chilName->name.' ('.$child.')</label><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
							RecursiveCategories($chilName->children, $livePath, $id, $data);
						}else{
							echo '<li class="treeview"><a href="'.$livePath.$chilName->id.'/key/'.md5($chilName->id).'"><i class="fa fa-folder-o"></i> '.$chilName->name.'</a>';
						}
						echo '</li>';
					}
					echo '</ul>';
				}
			}			
		?>
            <ul class="sidebar-menu"><?php echo RecursiveCategories($cateTree, $path, $categories['id']); ?></ul>
    </div><!-- end of left_part -->

	<div class="col-sm-10 col-xs-12 tree_content"><!-- start of right_part -->
	
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<!--ul id="myTab" class="nav nav-tabs tab_div">
                <li class="active"><a href="#categoryInformation" data-toggle="tab">Information</a></li>
                <li><a href="#relatedProducts" data-toggle="tab">Related Products</a></li>
            </ul-->
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12" id="categoryInformation"><!-- start of content_1 -->
			<?= $this->Form->create($categories, ['enctype'=>'multipart/form-data','class' => 'form-horizontal', 'novalidate' => true]); ?>
					<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
					<div class="col-sm-12 col-xs-12 no-padding">Category Name (ID-<?= $categories['id'];?>):&nbsp;<strong><?= $categories['name'];?></strong></div>
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Name <span class="text-red">*</span></label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->text('Categories.name', ['class'=>'form-control', 'placeholder'=>'Enter name']); ?>
											<span class="text-red">
												<?php
													echo isset($error['name']['_empty']) ? $error['name']['_empty']:NULL; 
													echo isset($error['name']['length']) ? $error['name']['length']:NULL; 
													echo isset($error['name']['charNum']) ? $error['name']['charNum']:NULL; 
												?>
											</span>
                                        </div>
                                    </div>
									
									<div class="form-group">
										<label class="col-sm-2 control-label">Is Active</label>
										<div class="col-sm-10">
                                        	<?= $this->Form->select('Categories.is_active', $this->Admin->siteStatus, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= isset($error['is_active']['inList']) ? $error['is_active']['inList']:NULL;?></span>
										</div>
									</div>
                                    
									<div class="form-group">
										<label class="col-sm-2 control-label">URL Key <span class="text-red">*</span></label>
										<div class="col-sm-10">
											<?= $this->Form->text('Categories.url_key', ['class'=>'form-control', 'readOnly'=>true, 'placeholder'=>'Enter url key']); ?>
											<span class="text-red">
												<?php
													echo isset($error['url_key']['_empty']) ? $error['url_key']['_empty']:NULL; 
													echo isset($error['url_key']['urlKey']) ? $error['url_key']['urlKey']:NULL;
													echo isset($error['url_key']['charNum']) ? $error['url_key']['charNum']:NULL; 
												?>
											</span>
										</div>
									</div>
                                    
									<div class="form-group">
										<label class="col-sm-2 control-label">Banner Link</label>
										<div class="col-sm-10">
											<?= $this->Form->text('Categories.banner_link', ['class'=>'form-control', 'placeholder'=>'Enter banner link']); ?>
										</div>
									</div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Short Description</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Categories.short_description', ['id'=>'short_description', 'class'=>'form-control']); ?>
                                        </div>
                                    </div>
									
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Description</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Categories.description', ['id'=>'description', 'class'=>'form-control']); ?>
                                        </div>
                                    </div>
									
									<div class="form-group">
										<label class="col-sm-2 control-label">Image</label>
										<div class="col-sm-10">
											<?php if(!empty($categories['image'])){ echo $this->Html->image('../files/Categories/image/thumbnail-'.$categories['image'], ['alt' => '', 'style' => 'width:25px;height:25;float:left;']); } ?>
											<?= $this->Form->file('Categories.image'); ?>
											<span class="text-red">
												<?php
													echo isset($error['image']['_empty']) ? $error['image']['_empty']:NULL; 
													echo isset($error['image']['validExtension']) ? $error['image']['validExtension']:NULL;
													echo isset($error['image']['validSize']) ? $error['image']['validSize']:NULL;
												?>
											</span>
										</div>
									</div>
                                    
									<div class="form-group">
										<label class="col-sm-2 control-label">Title</label>
										<div class="col-sm-10">
											<?= $this->Form->text('Categories.title', ['class'=>'form-control', 'placeholder'=>'Enter title']); ?>
										</div>
									</div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Meta Keyword</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Categories.meta_keyword', ['class'=>'form-control', 'placeholder'=>'Enter meta keywords']); ?>
                                        </div>
                                    </div>
									
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Meta Description</label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->textarea('Categories.meta_description', ['class'=>'form-control', 'placeholder'=>'Enter meta description']); ?>
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
					
				</div><!-- end of middle_content -->
            <?= $this->Form->end(); ?>
                </div><!-- end of tab -->
				<div class="tab-pane fade col-sm-12 col-xs-12" id="relatedProducts"><!-- start of right_part -->                                                       
				<section class="content col-sm-12 col-xs-12">
					<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
						<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
							<ul class="list-unstyled list-inline pagination_div">
								<li>
									Page
									<span class="span_1">
										<a href="#"><i class="fa fa-caret-left"></i></a>
										<input type="text" class="form-control" value="1">
										<a href="#"><i class="fa fa-caret-right"></i></a>
									</span>
									of 1228 pages
								</li>
								<li>
									Page
									<span class="span_1 span_2">
										<select class="form-control select2">
											<option selected="selected">10</option>
											<option>20</option>
											<option>50</option>
											<option>100</option>
											<option>500</option>
											<option>1000</option>
										</select>
									</span>
									of 1228 pages
								</li>
								<li>Total 24542 records found</li>
							</ul>
						</div><!-- end of pagination -->
            
						<div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
							<button type="button" class="btn btn-div-cart btn-1e">Reset Filter</button>
							<button type="button" class="btn btn-div-buy btn-1b">Search</button>
						</div><!-- end of buttons -->						
					</div><!-- end of pagination or buttons -->
        
					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
						<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
							<thead>
								<tr><th>Checked</th><th>ID</th><th>Product SKU</th><th colspan="2">Product Name</th><th>Price</th></tr>
							</thead>                
							<tbody>
								<tr><!-- start of row_1 -->
									<td data-title="">
										<?= $this->Form->select("status", ['any'=>'Any','yes'=>'Yes','no'=>'No'], ['empty'=> false,'style'=>'width:100%;','class'=>'form-control']);?>
									</td>
									<td data-title="ID">
										<input type="text" class="form-control" placeholder="Enter ...">
									</td>
									<td data-title="Product SKU">
										<input type="text" class="form-control" placeholder="Enter ...">
									</td>
									
									<td data-title="Product Name" colspan="2">
										<input type="text" class="form-control" placeholder="Enter ...">
									</td>
									
									<td data-title="Price">
										<div class="input-group date">
											<div class="input-group-addon">From:</div>
											<input type="text" class="form-control pull-right">
										</div>
										<div class="input-group date">
											<div class="input-group-addon">To&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
											<input type="text" class="form-control pull-right">
										</div>
									</td>									
								</tr><!-- end of row_1 -->
                    
								<tr><!-- start of row_2 -->
									<td data-title="">
										<?= $this->Form->input("status", ['type'=>'checkbox','class'=>'form-control']);?>
									</td>
									<td data-title="ID">100043253</td>
									<td data-title="Product SKU">34344</td>
									<td data-title="Product Name" colspan="2">Urvashi Singh</td>
									<td data-title="Price"><i class="fa fa-rupee"></i> 23</td>
								</tr><!-- end of row_2 -->                    
							</tbody>
						</table>
					</div><!-- end of table -->
				</section>
                </div><!-- end of right_part -->
            </div><!-- end of profile -->
			
        </div><!-- end of right_part -->
            
    </div><!-- end of tab -->
</section>
<?= $this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js') ?>
<script>
	CKEDITOR.replace('short_description');
	CKEDITOR.replace('description');
</script>