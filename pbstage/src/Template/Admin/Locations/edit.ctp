<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Locations Information') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('Back'), ['controller' =>'Locations/'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
                <li><?= $this->Html->link(__('Add New'), ['controller' =>'Locations/'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
                <li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $locations['id']], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $locations['id'])]) ?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
	
    <div class="col-sm-2 col-xs-12 sidebar tree_div"><!-- start of left_part -->
		<?php
			$path = $this->Url->build('/admin/locations/edit/');
			function cateFun($cateTreeItem, $livePath, $id, $data=''){
				//if( count($cateTreeItem->children) > 0 ){
					foreach ($cateTreeItem as $chilName) {
						$child = count($chilName->children);
						if( $child > 0 ){
							$active = ($id == $chilName->id) ? 'active ' : NULL;
							$open = ($id == $chilName->id) ? ' open-menu' : NULL;
							$style = ($id == $chilName->id) ? ' style="display:block;"' : NULL;
							$data = $data.'<li class="'.$active.'treeview"><a href="'.$livePath.$chilName->id.'/key/'.md5($chilName->id).'"><i class="fa fa-folder-o"></i> '.$chilName->title.' ('.$child.')<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a><ul class="treeview-menu'.$open.'" '.$style.'>'.cateFun($chilName->children, $livePath, $id, $data).'</ul></li>';
						}else{
							$data = $data.'<li><a href="'.$livePath.$chilName->id.'/key/'.md5($chilName->id).'"><i class="fa fa-folder-o"></i> '.$chilName->title.'</a></li>';
						}
					}
				//}
				return $data;
			}			
		?>
            <ul class="sidebar-menu"><?php echo cateFun($cateTree, $path, $locations['id']); ?></ul>
    </div><!-- end of left_part -->

	<div class="col-sm-10 col-xs-12 tree_content"><!-- start of right_part -->	
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12" id="LocationsInformation"><!-- start of content_1 -->
			<?= $this->Form->create($locations, ['enctype'=>'multipart/form-data','class' => 'form-horizontal', 'novalidate' => true]); ?>
					<div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- Profile -->
					<div class="col-sm-12 col-xs-12 no-padding">Location title (ID-<?= $locations['id'];?>):&nbsp;<strong><?= $locations['title'];?></strong></div>
                    <div class="col-sm-12 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                        <div class="box box-default"><!-- start of box_div -->
                            <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Title <span class="text-red">*</span></label>
                                        <div class="col-sm-10">
                                        	<?= $this->Form->text('Locations.title', ['class'=>'form-control', 'placeholder'=>'Enter title']); ?>
											<span class="text-red">
												<?php
													echo isset($error['title']['_empty']) ? $error['title']['_empty']:NULL; 
													echo isset($error['title']['length']) ? $error['title']['length']:NULL; 
													echo isset($error['title']['charNum']) ? $error['title']['charNum']:NULL; 
												?>
											</span>
                                        </div>
                                    </div>
									
									<div class="form-group">
										<label class="col-sm-2 control-label">Code <span class="text-red">*</span></label>
										<div class="col-sm-10">
											<?= $this->Form->text('Locations.code', ['class'=>'form-control', 'placeholder'=>'Enter code']); ?>
											<span class="text-red">
												<?php
													echo isset($error['code']['_empty']) ? $error['code']['_empty']:NULL; 
													echo isset($error['code']['urlKey']) ? $error['code']['code']:NULL;
													echo isset($error['code']['charNum']) ? $error['code']['charNum']:NULL; 
												?>
											</span>
										</div>
									</div>
                                    
									<div class="form-group">
										<label class="col-sm-2 control-label">Is Active</label>
										<div class="col-sm-10">
                                        	<?= $this->Form->select('Locations.is_active', $this->Admin->siteStatus, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
											<span class="text-red"><?= isset($error['is_active']['inList']) ? $error['is_active']['inList']:NULL;?></span>
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
