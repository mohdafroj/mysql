<?php
$this->Paginator->setTemplates(['templates'=>'admin-list']);
$this->assign('title', 'Category Brands');
?>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Manage Category Brand') ?></h3>
        <ul class="list-inline list-unstyled">
            <li><?= $this->Html->link(__('Add New'), ['controller'=>'Categories', 'action' => 'brand', 'view'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
		<?= $this->Form->create(null, ['type'=>'get']) ?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
            	<ul class="list-unstyled list-inline pagination_div">
                	<li>
                    	Page 
                        <span class="span_1">
							<?= $this->Paginator->prev(__('Prev')) ?>
                            <input type="text" class="form-control" value="<?= $this->Paginator->counter(['format'=>__('{{page}}')]) ?>">
							<?= $this->Paginator->next(__('Next')) ?>
                        </span>
                        of <?= $this->Paginator->counter(['format'=>__('{{pages}}')]) ?> pages
                    </li>
                	<li>
                    	View
                        <span class="span_1 span_2">
                            <?= $this->Form->select('limit', $this->Admin->selectMenuOptions, ['value'=>$this->Paginator->param('perPage'),'default'=>50,'empty' => FALSE,'onChange'=>'this.form.submit();','class'=>'form-control']);?>
                        </span>
                        per page
                    </li>
                	<li>Total <?= $this->Paginator->counter(['format'=>__('{{count}}')]) ?> records found</li>
                </ul>
            </div><!-- end of pagination -->
            
            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?= $this->Html->link('Reset Filter', ['controller' => 'Categories', 'action' => 'brand', 'view'], ['class'=>'btn btn-div-cart btn-1e']);?>
                <?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->
        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>URL Key</th>
                        <th>Sorting</th>
                        <th>Banner Image</th>
                        <th>Product Image</th>
                        <th class="text-center"><?= __('Actions') ?></th>
                    </tr>
                </thead>                
                <tbody>                    
                    <tr><!-- start of row_1 -->
                        <td data-title="Category">
                            <?= $this->Form->select('category_id', $cateList, ['value'=>$categoryId,'empty'=> false,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Brand">
                            <?= $this->Form->select('brand_id', $brandsList, ['value'=>$brandId,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="URL Key">
                        	<?= $this->Form->text('url_key', ['value'=>$urlKey, 'class'=>'form-control', 'placeholder'=>'Enter url key']); ?>
                        </td>
                        <td data-title="Sorting">
                        </td>
                        <td data-title="Banner Image">
                        </td>
                        <td data-title="Product Image">
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($products as $value):?>         
                    <tr>
                        <td data-title="Category"><?php echo isset($cateList[$value->category_id]) ? $cateList[$value->category_id]:''; ?></td>
                        <td data-title="Brand"><?php echo isset($brandsList[$value->brand_id]) ? $brandsList[$value->brand_id]:'NA'; ?></td>
                        <td data-title="URL Key"><?= h($this->Admin->checkValue($value->url_key)) ?></td>
                        <td data-title="Sorting"><?= h($value->sort_order) ?></td>
                        <td data-title="Banner Image"><img src="<?php echo $value->logo1; ?>" alt="invalid"></td>
                        <td data-title="Product Image"><img src="<?php echo $value->logo2; ?>" alt="invalid"></td>
                        <td data-title="Action" class="text-center">
                            <?= $this->Html->link(__('<i class="fa fa-pencil"></i>'), ['action' => 'brand', 'edit', $value->id, 'key', md5($value->id)], ['escape'=>false]) ?>&nbsp;&nbsp;|&nbsp;&nbsp;
							<?= $this->Form->postLink(__('<i class="fa fa-trash"></i>'), ['action' => 'brand', 'delete', $value->id], ['block' => false, 'escape'=>false, 'method'=>'delete', 'confirm' => __('Are you sure you want to delete # {0}?', $value->id)]) ?>
                        </td>
                    </tr>
           <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
		<?= $this->Form->create(null, []) ?>
					<div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->  
						<div class="box-body">
							<div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-5">
									<?php echo $this->Form->hidden('id', ['value'=>$record['id']]); ?>
									<?= $this->Form->select('category_id', $cateList, ['value'=>$record['category_id'],'empty'=> false,'style'=>'width:100%;','class'=>'form-control'])?>
                                </div>
                                <div class="col-sm-5">
									<?= $this->Form->select('brand_id', $brandsList, ['value'=>$record['brand_id'],'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Banner Image:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('logo1', ['class'=>'form-control', 'value'=>$record['logo1'], 'placeholder'=>'Please enter a valid link!']); ?>
									<span class="text-red">
									
									</span>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Product Image:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('logo2', ['class'=>'form-control', 'value'=>$record['logo2'], 'placeholder'=>'Please enter a valid link!']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">URL Key:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('url_key', ['class'=>'form-control', 'value'=>$record['url_key'], 'placeholder'=>'Please enter a valid link!']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Tag Line:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->text('tag_line', ['class'=>'form-control', 'value'=>$record['tag_line'], 'placeholder'=>'Please enter a tag line!']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Description:</label>
                                <div class="col-sm-10">
                                    <?= $this->Form->textarea('description', ['id'=>'description', 'value'=>$record['description'],  'class'=>'form-control', 'placeholder'=>'Enter description']); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-sm-2 control-label">Sort Order:</label>
                                <div class="col-sm-2">
                                    <?= $this->Form->text('sort_order', ['class'=>'form-control', 'value'=>$record['sort_order'], 'placeholder'=>'Please enter sort order!']); ?>
                                </div>
                                <div class="col-sm-8">
                                </div>
                            </div>
							<div class="form-group">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-div-buy btn-1b">Save</button>
                                </div>
                            </div>
						</div>
					</div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>    

