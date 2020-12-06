<?php
$this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
    <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
        <h3><?= h('Manage Reviews') ?></h3>
        <ul class="list-inline list-unstyled">
            <li><?= $this->Html->link(__('New Review'), ['controller'=>'Reviews', 'action' => 'add', 'key', md5('reviews')], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
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
                <?= $this->Html->link('Export To CSV', ['action' => 'exports', '_ext' => 'csv', 'ReviewsExport','?'=>$queryString], ['class'=>'btn btn-div-buy btn-1b']);?>
                <?= $this->Html->link('Reset Filter', ['controller' => 'Reviews'], ['class'=>'btn btn-div-cart btn-1e']);?>
                <?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->
        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'Id') ?></th>
                        <th><?= $this->Paginator->sort('email', 'Customer Email') ?></th>
                        <th><?= $this->Paginator->sort('sku_code', 'Product Code') ?></th>
                        <th><?= $this->Paginator->sort('title', 'Title') ?></th>
                        <th><?= $this->Paginator->sort('description', 'Description') ?></th>
                        <th><?= $this->Paginator->sort('rating', 'Rating') ?></th>
                        <th><?= $this->Paginator->sort('location_ip', 'Location IP') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Created At') ?></th>
                        <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>                
                <tbody>                    
                    <tr><!-- start of row_1 -->
                        <td data-title="Id">
                        	<?= $this->Form->text('id', ['value'=>$id, 'class'=>'form-control', 'placeholder'=>'Enter review id']); ?>
						</td>
                        <td data-title="Email">
                        	<?= $this->Form->text('email', ['value'=>$email, 'class'=>'form-control', 'placeholder'=>'Enter customer email']); ?>
                        </td>
                        <td data-title="SKU Code">
                        	<?= $this->Form->text('sku_code', ['value'=>$skuCode, 'class'=>'form-control', 'placeholder'=>'Enter sku code']); ?>
                        </td>
                        <td data-title="Title">                            
                        	<?= $this->Form->text('title', ['value'=>$title, 'class'=>'form-control', 'placeholder'=>'Enter title']); ?>
                        </td>
                        <td data-title="Description">
                        	<!--?= $this->Form->text('description', ['value'=>$description, 'class'=>'form-control', 'placeholder'=>'Enter description']); ?-->
                        </td>
                        <td data-title="Rating">
                        	<?= $this->Form->text('rating', ['value'=>$rating, 'class'=>'form-control', 'placeholder'=>'Enter rating']); ?>
                        </td>
                        <td data-title="IP">
                        	<!--?= $this->Form->text('location_ip', ['value'=>$locationIP, 'class'=>'form-control', 'placeholder'=>'Enter IP address']); ?-->
                        </td>
                        <td data-title="Created Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        		<?= $this->Form->text('created', ['value'=>$created, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
                        	</div>
                        </td>
                        <td data-title="Status">
                            <?= $this->Form->select('is_active', $this->Admin->reviewStatus, ['value'=>$isActive,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php foreach ($reviews as $value):?>         
                    <tr>
                        <td data-title="Id"><?= $this->Number->format($value->id) ?></td>
                        <td data-title="Email"><?= h($value->customer->email) ?></td>
                        <td data-title="SKU Code"><?= h($value->product->sku_code) ?></td>
                        <td data-title="Title"><?= h($this->Admin->checkValue($value->title)) ?></td>
                        <td data-title="Description"><?= h($this->Admin->checkValue($value->description)) ?></td>
                        <td data-title="Rating"><?= h($this->Admin->checkValue($value->rating)); ?></td>
                        <td data-title="IP"><?= h($this->Admin->checkValue($value->location_ip)); ?></td>
                        <td data-title="Created Date"><?= date('Y-m-d h:m:s',strtotime($value->created)) ?></td>
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

