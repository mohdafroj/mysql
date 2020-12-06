<section class="content-header col-sm-12 col-xs-12">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Manage Products') ?></h3>
        <ul class="list-inline list-unstyled">
            <li><?= $this->Html->link(__('New Product'), ['controller'=>'Products', 'action' => 'add', 'key', md5('products')], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
        </ul>
    </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
		<?= $this->Form->create(null, ['type'=>'get']) ?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
                <?php echo $this->Element('Admin/pagination');?>
            </div><!-- end of pagination -->
            
            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div"><!-- start of buttons -->
                <?= $this->Html->link('Export To CSV', ['action' => 'exports', '_ext' => 'csv', 'ProductsExport', '?' => $queryString], ['class'=>'btn btn-div-buy btn-1b']);?>
                <?= $this->Html->link('Reset Filter', ['controller' => 'Products'], ['class'=>'btn btn-div-cart btn-1e']);?>
                <?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->
        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'Id') ?></th>
                        <th><?= $this->Paginator->sort('sku_code', 'SKU Code') ?></th>
                        <th><?= $this->Paginator->sort('brand_id', 'Brand') ?></th>
                        <th>Category</th>
                        <th><?= $this->Paginator->sort('name', 'Name') ?></th>
                        <th><?= $this->Paginator->sort('url_key', 'Url Key') ?></th>
                        <th><?= $this->Paginator->sort('size', 'Size') ?></th>
                        <th><?= $this->Paginator->sort('qty', 'Quantity') ?></th>
                        <th><?= $this->Paginator->sort('is_stock', 'Stock') ?></th>
                        <th><?= $this->Paginator->sort('gender', 'Gender') ?></th>
                        <th><?= $this->Paginator->sort('price', 'Price') ?></th>
                        <th><?= $this->Paginator->sort('offer_price', 'Offer Price') ?></th>
                        <!--th><?php //echo $this->Paginator->sort('created', 'Created Date') ?></th-->
                        <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>                
                <tbody>                    
                    <tr><!-- start of row_1 -->
                        <td data-title="Id">
                        	<?= $this->Form->text('id', ['value'=>$id, 'class'=>'form-control', 'placeholder'=>'Enter Id']); ?>
						</td>
                        <td data-title="SKU Code">
                        	<?= $this->Form->text('sku_code', ['value'=>$skuCode, 'class'=>'form-control', 'placeholder'=>'Enter sku code']); ?>
                        </td>
                        <td data-title="Brand">
                            <?= $this->Form->select('brand_id', $brandsList, ['value'=>$brandId,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Category">
                            <?= $this->Form->select('category_id', $cateList, ['value'=>$categoryId,'empty'=> false,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Title">                            
                        	<?= $this->Form->text('name', ['value'=>$name, 'class'=>'form-control', 'placeholder'=>'Enter name']); ?>
                        </td>
                        <td data-title="Url">
                        	<?= $this->Form->text('url_key', ['value'=>$urlKey, 'class'=>'form-control', 'placeholder'=>'Enter url key']); ?>
                        </td>
                        <td data-title="Size">
                        	<?= $this->Form->text('size', ['value'=>$size, 'class'=>'form-control', 'placeholder'=>'Enter size']); ?>
                            <?= $this->Form->select('size_unit', $this->Admin->productSize, ['value'=>$sizeUnit,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Qty">
                        	<?= $this->Form->text('qty', ['value'=>$qty, 'class'=>'form-control', 'placeholder'=>'Enter quantity']); ?>
                        </td>
                        <td data-title="GST Tax">
                            <?= $this->Form->select('is_stock', $this->Admin->productStatus, ['value'=>$stock,'default'=>'','empty'=> TRUE, 'class'=>'form-control'])?>
                        </td>
                        <td data-title="Gender">
                            <?= $this->Form->select('gender', $this->Admin->siteGender, ['value'=>$gender,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Price">
							<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
								<div class="input-group-addon"><i class="fa fa-rupee"></i></div>
								<?= $this->Form->text('price', ['value'=>$price, 'class'=>'form-control', 'placeholder'=>'Enter price']); ?>
							</div>	
                        </td>
                        <td data-title="Offer Price">
							<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
								<div class="input-group-addon"><i class="fa fa-rupee"></i></div>
								<?= $this->Form->text('offer_price', ['value'=>$offerPrice, 'class'=>'form-control', 'placeholder'=>'Enter offer price']); ?>
							</div>	
                        </td>
                        <!--td data-title="Created Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        		<?php //echo $this->Form->text('created', ['value'=>$created, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
                        	</div>
                        </td-->
                        <td data-title="Status">
                            <?= $this->Form->select('is_active', $this->Admin->siteStatus, ['value'=>$isActive,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($products as $value):?>         
                    <tr>
                        <td data-title="Id"><?= $this->Number->format($value->id) ?></td>
                        <td data-title="SKU Code"><?= h($value->sku_code) ?></td>
                        <td data-title="Brand"><?php echo isset($brandsList[$value->brand_id]) ? $brandsList[$value->brand_id]:'NA'; ?></td>
                        <td data-title="Category">
							<?php 
								foreach($value->products_categories as $v){
									echo isset($cateList[$v->category_id]) ? $cateList[$v->category_id].'<br />':'';
								}
							?>
						</td>
                        <td data-title="Name"><?= h($this->Admin->checkValue($value->name)) ?></td>
                        <td data-title="Url"><?= h($this->Admin->checkValue($value->url_key)) ?></td>
                        <td data-title="Size"><?= h($this->Admin->checkValue($value->size)." ".strtoupper($value->size_unit))?></td>
                        <td data-title="Qty"><?= h($this->Admin->checkValue($value->qty)); ?></td>
                        <td data-title="Stock"><?= $this->Admin->productStatus[$value->is_stock] ?? 'N/A' ?></td>
                        <td data-title="Gender"><?= h($this->Admin->checkValue(ucfirst($value->gender))); ?></td>
                        <td data-title="Price"><i class="fa fa-rupee"></i> <?= $this->Number->format($value->price) ?></td>
                        <td data-title="Offer Price"><i class="fa fa-rupee"></i> <?= $this->Number->format($value->offer_price) ?></td>
                        <!--td data-title="Created"><?php //echo h($this->Admin->emptyDate($value->created)); ?></td-->
                        <td data-title="Status"><?= h($this->Admin->checkValue(ucfirst($value->is_active))) ?></td>
                        <td data-title="Action" class="text-center">
                            <?= $this->Html->link('<i class="fa fa-pencil"></i>', ['action' => 'edit', $value->id, 'key', md5($value->id)],['title'=>'Edit Products','escape'=>false]) ?>
                        </td>
                    </tr>
           <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>    

