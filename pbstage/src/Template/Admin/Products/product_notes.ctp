<?php echo $this->Element('Admin/Products/top_menu');?>
<section class="content col-sm-12 col-xs-12">
	<div class="col-sm-12 col-xs-12"><!-- start of right_part -->
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div tree_table"><!-- start of tab -->
			<?php echo $this->Element('Admin/Products/sub_menu');?>
            <div id="myTabContent" class="tab-content tab_div_content"><!-- start of right_part -->
				<div class="tab-pane fade in active col-sm-12 col-xs-12"><!-- start of content_1 -->
				<section class="content col-sm-12 col-xs-12">
					<div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
						<div class="col-md-12 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div">
							<?= $this->Form->create($addnotes, ['enctype'=>'multipart/form-data', 'class'=>'form-horizontal', 'novalidate' => true]); ?>
							<div class="box-body">
                                <div class="form-group col-sm-3 col-xs-3">
                                    <div class="col-sm-12">
                                        <?= $this->Form->select('ProductsNotes.title', $this->Admin->productNote, ['class'=>'form-control'])?>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6 col-xs-6">
                                    <label for="Description" class="col-sm-2 control-label">Description:</label>                                    
                                    <div class="col-sm-10">
										<?= $this->Form->textarea('ProductsNotes.description', ['class'=>'form-control', 'placeholder'=>'Enter description']); ?>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3 col-xs-3">
                                    <div class="col-sm-12">
                                       <?= $this->Form->button('Save', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
                                    </div>
                                </div>
                            </div>
							<?= $this->Form->end(); ?>
						</div>
						<div class="col-md-12 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
							<?php echo $this->Element('Admin/pagination');?>
						</div><!-- end of pagination -->            
					</div><!-- end of pagination or buttons -->        
					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
						<table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
							<thead>
								<tr><th>S No</th><th>Title</th><th>Description</th><th class="text-center">Action</th></tr>
							</thead>                
							<tbody>								
                    <?php $i = 1;
						  foreach ($notes as $value):?> 
								<tr>
									<td data-title="S No"><?php echo $i++;?></td>
									<td data-title="Title"><?= h($this->Admin->checkValue($value->title)) ?></td>
									<td data-title="Description"><?= h($this->Admin->checkValue($value->description)) ?></td>
									<td data-title="Action" class="text-center">
										<?php echo $this->Form->postLink(__('Delete'), ['action' => 'note-delete', $value->id, $id], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $value->id)]); ?>
									</td>
								</tr>
					<?php endforeach;?>			
							</tbody>                                                   
						</table>                                                       
					</div><!-- end of table -->                                        
				</section>                                                             
                </div><!-- end of right_part -->                                       
            </div><!-- end of profile -->                                              
			                                                                           
        </div><!-- end of right_part -->                                               
                                                                                       
    </div><!-- end of tab -->                                                          
</section>                                                                             
                                                           
                                                                                       