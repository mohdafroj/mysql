<?php
$this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Manage Family Attribute') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('Back'), ['controller' =>'Attributes/'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
                <li><?= $this->Html->link(__('Add New'), ['controller' =>'Attributes', 'action'=>'families'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
                <li><?= $this->Form->postLink(__('Add New'), ['controller' =>'Attributes','action' => 'families', 'id'], ['block' => false, 'method'=>'get', 'class' =>'btn btn-div-cart btn-1e']) ?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('id', 'Id') ?></th>
                        <th><?= $this->Paginator->sort('title', 'Title') ?></th>
                        <th><?= $this->Paginator->sort('description', 'Description') ?></th>
                        <th><?= $this->Paginator->sort('image', 'Image') ?></th>
                        <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
		<?= $this->Form->create($family, ['type'=>'post']) ?>
                    <tr>
                        <td data-title="Id"></td>
                        <td data-title="Title">
                        	<?= $this->Form->text('Families.title', ['class'=>'form-control', 'placeholder'=>'Enter Title']); ?>
                        </td>
                        <td data-title="Description">
                        	<?= $this->Form->textarea('Families.description', ['rows'=>'2', 'class'=>'form-control', 'placeholder'=>'Enter description']); ?>
                        </td>
                        <td data-title="Image">
                        	<?= $this->Form->text('Families.image', ['class'=>'form-control', 'placeholder'=>'Enter image url']); ?>
                        </td>
                        <td data-title="Status">
                            <?= $this->Form->select('Families.is_active', $this->Admin->siteStatus, ['empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Action">
							<button type="submit" class="btn btn-div-buy btn-1b">Save</button>
						</td>
                    </tr>
        <?= $this->Form->end() ?>
           <?php foreach ($families as $value):?>         
                    <tr>
                        <td data-title="Id"><?= $this->Number->format($value->id) ?></td>
                        <td data-title="Title"><?= h($this->Admin->checkValue($value->title)) ?></td>
                        <td data-title="Description"><?= h($this->Admin->checkValue($value->description)) ?></td>
                        <td data-title="Image">&nbsp;</td>
                        <td data-title="Status"><?= h($this->Admin->checkValue(ucfirst($value->is_active))) ?></td>
                        <td data-title="Action" class="text-center">
							<?= $this->Form->postLink(__('Edit'), ['action' => 'families', $value->id, 'key', md5('families')], ['block' => false, 'method'=>'get', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to edit # {0}?', $value->id)]) ?>
							<?= $this->Form->postLink(__('Delete'), ['action' => 'families', $value->id, 'key', md5('families')], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $value->id)]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
</section>    

