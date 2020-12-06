<?php 
	//$this->Paginator->setTemplates(['templates'=>'admin-list']);
?>
<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Manage Users') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('New User'), ['action' => 'add'], ['class'=>'btn btn-div-buy btn-1b']) ?></li>
            </ul>
        </div><!-- end of inner_heading -->
</section>
<section class="content col-sm-12 col-xs-12">
		<?= $this->Form->create(null, ['type'=>'get']) ?>
        <div class="col-sm-12 col-xs-12 no-padding"><!-- start of pagination or buttons -->
        	<div class="col-md-8 col-sm-12 col-xs-12 no-padding-left xs-no-padding"><!-- start of pagination -->
            	
            </div><!-- end of pagination -->
            
            <div class="col-md-4 col-sm-12 col-xs-12 no-padding-right xs-no-padding buttons_div" style="margin-bottom:15px;"><!-- start of buttons -->
                <?= $this->Html->link('Export To CSV', ['controller' => 'Users', 'action' => 'exports', '_ext' => 'csv', 'UsersExport', '?' => $queryString], ['class'=>'btn btn-div-buy btn-1b']);?>
                <?= $this->Html->link('Reset Filter', ['controller' => 'Users/'], ['class'=>'btn btn-div-cart btn-1e']);?>
                <?= $this->Form->button('Search', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>
            </div><!-- end of buttons -->
        </div><!-- end of pagination or buttons -->
        
        <div class="col-sm-12 col-xs-12 table_view responsive-mobile-table"><!-- start of table -->
            <table class="col-xs-12 table-bordered table-hover table-condensed no-padding no-border">
                <thead>
                    <tr>
                        <!-- <th><?= $this->Paginator->sort('id', 'Id') ?></th>
                        <th><?= $this->Paginator->sort('username', 'User Name') ?></th>
                        <th><?= $this->Paginator->sort('firstname', 'First Name') ?></th>
                        <th><?= $this->Paginator->sort('lastname', 'Last Name') ?></th>
                        <th><?= $this->Paginator->sort('email', 'Email Id') ?></th>
                        <th><?= $this->Paginator->sort('Parent', 'Parent') ?></th>
                        <th><?= $this->Paginator->sort('created', 'Created Date') ?></th>
                        <th><?= $this->Paginator->sort('modified', 'Modified Date') ?></th>
                        <th><?= $this->Paginator->sort('is_active', 'Status') ?></th>
                        <th><?= __('Permission') ?></th>
                        <th><?= __('Actions') ?></th> -->

                         <th><?= __('Id') ?></th>
                        <th><?=  __('User Name') ?></th>
                        <th><?=  __('First Name') ?></th>
                        <th><?=  __('Last Name') ?></th>
                        <th><?=  __('Email Id') ?></th>                       
                        <th><?= __('Created Date') ?></th>
                        <th><?= __('Modified Date') ?></th>
                        <th><?=  __('Status') ?></th>
                        <th><?= __('Permission') ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>                
                <tbody>                    
                    <tr><!-- start of row_1 -->
                        <td data-title="Id"></td>
                        <td data-title="Enter Name">
                        	<?= $this->Form->text('username', ['value'=>$username, 'class'=>'form-control', 'placeholder'=>'Enter username']); ?>
                        </td>
                        <td data-title="First Name">                            
                        	<?= $this->Form->text('firstname', ['value'=>$firstname, 'class'=>'form-control', 'placeholder'=>'Enter first name']); ?>
                        </td>
                        <td data-title="Last Name">
                        	<?= $this->Form->text('lastname', ['value'=>$lastname, 'class'=>'form-control', 'placeholder'=>'Enter last name']); ?>
                        </td>
                        <td data-title="Email">
                        	<?= $this->Form->email('email', ['value'=>$email, 'class'=>'form-control', 'placeholder'=>'Enter valid email']); ?>
                        </td>
                        <td data-title="Created Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                        		<?= $this->Form->text('created', ['value'=>$created, 'id'=>'datepicker1', 'class'=>'form-control']); ?>
                        	</div>
                        </td>
                        <td data-title="Modified Date">
                        	<div class="input-group date">
                        		<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <?= $this->Form->text('modified', ['value'=>$modified, 'id'=>'datepicker2', 'class'=>'form-control pull-right']); ?>
                                <input type="hidden" name="parent_id" value="10" class="form-control" />
                        	</div>
                        </td>
                        <td data-title="Status">
                            <?= $this->Form->select('status', $this->Admin->siteStatus, ['value'=>$status,'default'=>'','empty'=> TRUE,'style'=>'width:100%;','class'=>'form-control'])?>
                        </td>
                        <td data-title="Permisssion">&nbsp;</td>
                        <td data-title="Action">&nbsp;</td>
                    </tr><!-- end of row_1 -->
           <?php 
           foreach ($users as $value):
           // pr( $value['id']);
					//if( ($value->username != 'developer') ):
		   ?>         
                    <tr><!-- start of row_2 -->
                        <td data-title="Id"><?= $this->Number->format($value['id']) ?></td>
                        <td data-title="Name"><?= h($this->Admin->checkValue($value['firstname'].$value['lastname'].'( '.$value['username'].' )')) ?></td>
                        <td data-title="First Name"><?= h($this->Admin->checkValue($value['firstname'])) ?></td>
                        <td data-title="Last Name"><?= h($this->Admin->checkValue($value['lastname'])) ?></td>
                        <td data-title="Email"><?= h($this->Admin->checkValue($value['email'])) ?></td>
                        
                        <td data-title="Created Date"><?= h($this->Admin->emptyDate($value['created'])); ?></td>
                        <td data-title="Modified Date"><?= h($this->Admin->emptyDate($value['modified'])) ?></td>
                        <td data-title="Status"><?= h(ucfirst($value['is_active'])) ?></td>
                        <td data-title="Permission" class="text-center">
                            <?= $this->Html->link(__('Permission'), ['action' => 'Permission', $value['id']]) ?>
                        </td>
                        <td data-title="Action" class="text-center">
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $value['id']]) ?>
                            <?= $this->Html->link(__('Delete'), ['action' => 'delete', $value['id']]) ?>
                        </td>
                    </tr><!-- end of row_2 -->
           <?php 	//endif;        
                 endforeach; 
		   ?>    
                </tbody>
            </table>
           
        </div><!-- end of table -->
        <?= $this->Form->end() ?>
</section>    