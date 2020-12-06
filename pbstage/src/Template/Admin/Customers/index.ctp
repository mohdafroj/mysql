<?php
    $this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Manage Customers') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('New Customer'), ['action' => 'add'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
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
                <?= $this->Html->link('Export To CSV', ['action' => 'exports', $lastCustomerId, '_ext' => 'csv', 'customers', '?' => $queryString], ['class'=>'btn btn-div-buy btn-1b']);?>
                <?= $this->Html->link('Reset Filter', ['controller' => 'Customers'], ['class'=>'btn btn-div-cart btn-1e']);?>
                <?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->
        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <th width="5%"><?= $this->Paginator->sort('id', 'S No') ?></th>
                        <th><?= $this->Paginator->sort('mobile', 'Mobile') ?></th>
                        <th><a href="#">Name</a></th>
                        <th><?= $this->Paginator->sort('email', 'Email Id') ?></th>
                        <th><?= $this->Paginator->sort('address', 'Address') ?></th>
                        <th><?= $this->Paginator->sort('city', 'City') ?></th>
                        <th><?= $this->Paginator->sort('pincode', 'Pincode') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Created') ?></th>
                        <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                        <th><a href="#">Actions</a></th>
                    </tr>
                </thead>                
                <tbody>                    
                    <tr><!-- start of row_1 -->
                        <td data-title="S No"></td>
                        <td data-title="Mobile Number">
                        	<?= $this->Form->text('mobile', ['value'=>$mobile, 'class'=>'form-control', 'placeholder'=>'Enter mobile number']); ?>
                        </td>
                        <td data-title="Name">
                            <div class="input-group date">
                                <?= $this->Form->text('firstname', ['value'=>$firstname, 'class'=>'form-control', 'placeholder'=>'First name']); ?>
                        	</div>
                        	<div class="input-group date">
                                <?= $this->Form->text('lastname', ['value'=>$lastname, 'class'=>'form-control', 'placeholder'=>'Last name']); ?>
                        	</div>
                        </td>
                        <td data-title="Email">
                        	<?= $this->Form->email('email', ['value'=>$email, 'class'=>'form-control', 'placeholder'=>'Enter valid email']); ?>
                        </td>
                        <td data-title="Address">
                        	<?= $this->Form->text('address', ['value'=>$address, 'class'=>'form-control', 'placeholder'=>'Enter address']); ?>
                        </td>
                        <td data-title="City">
                        	<?= $this->Form->text('city', ['value'=>$city, 'class'=>'form-control', 'placeholder'=>'Enter city']); ?>
                        </td>
                        <td data-title="Pincode">
                        	<?= $this->Form->text('pincode', ['value'=>$pincode, 'class'=>'form-control', 'placeholder'=>'Enter pincode']); ?>
                        </td>
                        <td data-title="Created Date">
                            <div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i>From:</div>
                        		<?= $this->Form->text('createdFrom', ['value'=>$createdFrom, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
                        	</div>
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i>To:</div>
                        		<?= $this->Form->text('createdTo', ['value'=>$createdTo, 'id'=>'datepicker2', 'class'=>'form-control']); ?>
                        	</div>
                        </td>
                        <td data-title="Status">
                            <?= $this->Form->select('status', $this->Admin->customerStatus, ['value'=>$status,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php 
                $page = $this->Paginator->param('page');
                $perPage = $this->Paginator->param('perPage');
                $i = ($page == 1) ? 1 : (($page - 1) * $perPage) + 1; 
            foreach ($customers as $value):?>         
                    <tr><!-- start of row_2 -->
                        <td data-title="s No"><?= $i++ ?></td>
                        <td data-title="Mobile"><?= h($value->mobile) ?></td>
                        <td data-title="Name">
                            <?php  $name = $value->firstname.' '.$value->lastname;
                                   echo $this->Admin->checkValue(trim($name)); 
                            ?>
                        </td>
                        <td data-title="Email"><?= h($this->Admin->checkValue($value->email)) ?></td>
                        <td data-title="Address"><?= h($this->Admin->checkValue($value->address)); ?></td>
                        <td data-title="City"><?= h($this->Admin->checkValue($value->city)); ?></td>
                        <td data-title="Pincode"><?= h($this->Admin->checkValue($value->pincode)) ?></td>
                        <td data-title="Created Date"><?= h($this->Admin->emptyDate($value->created)); ?></td>
                        <td data-title="Status"><?= h($this->Admin->checkValue(ucfirst($value->is_active))) ?></td>
                        <td data-title="Action" class="text-center">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $value->id, 'key', md5($value->id)]) ?>
                        </td>
                    </tr><!-- end of row_2 -->
                <?php endforeach; ?>    
                </tbody>
            </table>           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>    