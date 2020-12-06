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
                <li><?= $this->Html->link(__('Add New'), ['controller' =>'Categories'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	
    <div class="col-sm-2 col-xs-12 sidebar tree_div"><!-- start of left_part -->
		<?php
			function RecursiveCategories($cateTreeItem, $livePath, $data=''){
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
							RecursiveCategories($chilName->children, $livePath, $data);
						}else{
							echo '<li><a href="'.$livePath.$chilName->id.'/key/'.md5($chilName->id).'"><i class="fa fa-folder-o"></i> '.$chilName->name.'</a>';
						}
						echo '</li>';
					}
					echo '</ul>';
				}
			}			
		?>
			<?php
					$path = $this->Url->build('/admin/categories/edit/');
					RecursiveCategories($cateTree,$path); 
				?>
    </div><!-- end of left_part -->

	<div class="col-sm-10 col-xs-12 tree_content"><!-- start of right_part -->
	
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12" id="categoryInformation"><!-- start of content_1 -->
			<?= $this->Form->create($categories, ['enctype'=>'multipart/form-data','class' => 'form-horizontal', 'novalidate' => true]); ?>
					<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
									<div class="form-group">
										<label class="col-sm-2 control-label">Parent Category <span class="text-red">*</span></label>
										<div class="col-sm-10">
                                        	<?= $this->Form->select('Categories.parent_id', $cateList, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
										</div>
									</div>
                                    
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
											<?= $this->Form->text('Categories.url_key', ['class'=>'form-control', 'placeholder'=>'Enter url key']); ?>
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
            </div><!-- end of profile -->
			
        </div><!-- end of right_part -->
            
    </div><!-- end of tab -->
</section>
<?= $this->Html->script('https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js') ?>
<script>
	CKEDITOR.replace('short_description');
	CKEDITOR.replace('description');
</script>