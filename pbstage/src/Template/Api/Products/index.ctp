<?php
$this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
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
                        <th><?= $this->Paginator->sort('title', 'Title') ?></th>
                        <th><?= $this->Paginator->sort('url_key', 'Url Key') ?></th>
                        <th><?= $this->Paginator->sort('size', 'Size') ?></th>
                        <th><?= $this->Paginator->sort('qty', 'Quantity') ?></th>
                        <th><?= $this->Paginator->sort('goods_tax', 'GST Tax') ?></th>
                        <th><?= $this->Paginator->sort('price', 'Price') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Created Date') ?></th>
                        <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>                
                <tbody>                    
                    <tr><!-- start of row_1 -->
                        <td data-title="Id"></td>
                        <td data-title="SKU Code">
                        	<?= $this->Form->text('sku_code', ['value'=>$skuCode, 'class'=>'form-control', 'placeholder'=>'Enter sku code']); ?>
                        </td>
                        <td data-title="Title">                            
                        	<?= $this->Form->text('title', ['value'=>$title, 'class'=>'form-control', 'placeholder'=>'Enter title']); ?>
                        </td>
                        <td data-title="Url">
                        	<?= $this->Form->text('url_key', ['value'=>$urlKey, 'class'=>'form-control', 'placeholder'=>'Enter url key']); ?>
                        </td>
                        <td data-title="Size">
                        	<?= $this->Form->text('size', ['value'=>$size, 'class'=>'form-control', 'placeholder'=>'Enter size']); ?>
                        </td>
                        <td data-title="Qty">
                        	<?= $this->Form->text('qty', ['value'=>$qty, 'class'=>'form-control', 'placeholder'=>'Enter quantity']); ?>
                        </td>
                        <td data-title="GST Tax">
                            <?= $this->Form->select('goods_tax', $this->Admin->productTax, ['value'=>$goodsTax,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Price">
							<div class="input-group date" style="width:100%;float:left;margin-right:1%;">
								<div class="input-group-addon"><i class="fa fa-rupee"></i></div>
								<?= $this->Form->text('price', ['value'=>$price, 'class'=>'form-control', 'placeholder'=>'Enter price']); ?>
							</div>	
                        </td>
                        <td data-title="Created Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        		<?= $this->Form->text('created', ['value'=>$created, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
                        	</div>
                        </td>
                        <td data-title="Status">
                            <?= $this->Form->select('is_active', $this->Admin->siteStatus, ['value'=>$isActive,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($products as $value):?>         
                    <tr>
                        <td data-title="Id"><?= $this->Number->format($value->id) ?></td>
                        <td data-title="SKU Code"><?= h($value->sku_code) ?></td>
                        <td data-title="Title"><?= h($this->Admin->checkValue($value->title)) ?></td>
                        <td data-title="Url"><?= h($this->Admin->checkValue($value->url_key)) ?></td>
                        <td data-title="Size"><?= h($this->Admin->checkValue($value->size)." ".strtoupper($value->size_unit))?></td>
                        <td data-title="Qty"><?= h($this->Admin->checkValue($value->qty)); ?></td>
                        <td data-title="GST Tax"><?= h($this->Admin->checkValue(strtoupper($value->goods_tax))); ?></td>
                        <td data-title="Price"><i class="fa fa-rupee"></i> <?= $this->Number->format($value->price) ?></td>
                        <td data-title="Created Date"><?= h($this->Admin->emptyDate($value->created)); ?></td>
                        <td data-title="Status"><?= h($this->Admin->checkValue(ucfirst($value->is_active))) ?></td>
                        <td data-title="Action" class="text-center">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $value->id, 'key', md5($value->id)]) ?>
                        </td>
                    </tr>
           <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>    

<script>

(function(a,b,c){
    
})();

</script>