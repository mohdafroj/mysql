<section class="content-header col-sm-12 col-xs-12 no-padding-left no-padding-right">
        <div class="col-sm-12 col-xs-12 inner_heading"><!-- start of inner_heading -->
            <h3><?= h('Update User') ?></h3>
            <ul class="list-inline list-unstyled">
                <li><?= $this->Html->link(__('Back'), ['controller' => 'Users/'], ['class'=>'btn btn-div-cart btn-1e']) ?></li>
		        <?php if( $user->username != 'developer' ) :?>
                <li>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user->id], ['block' => false, 'method'=>'delete', 'class' =>'btn btn-div-cart btn-1e', 'confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?>
                </li>
        <?php endif;?>        
            </ul>
        </div><!-- end of inner_heading -->
</section>
<?= $this->Form->create($user, ['context'=>['validator' => 'adminUserUpdate'], 'class' => 'form-horizontal', 'novalidate'=>true]) ?>
<!-- Main content -->
<section class="content col-sm-12 col-xs-12">
        <div class="col-md-12 col-sm-12 col-xs-12 no-padding table_main_div"><!-- start of tab -->
            <div class="col-sm-12 col-xs-12 row-flex row-flex-wrap no-padding margin-md-top"><!-- start of middle_content -->
                <div class="col-sm-3 col-xs-12 flex_box no-padding-left xs-no-padding"></div><!-- start of col_div -->
                <div class="col-sm-6 col-xs-12 flex_box no-padding-left xs-no-padding"><!-- start of col_div -->
                    <div class="box box-default"><!-- start of box_div -->
                        <div class="col-sm-12 col-xs-12 flex_box_content price_detail"><!-- start of box_content -->
                            <div class="box-body">


                            <div class="form-group">
                                <label class="col-sm-3 control-label">Parent User <span class="text-red">*</span></label>
                                <div class="col-sm-9">
                                <?= $this->Form->select('Users.parent_id', $topuser, ['value' => $user['parent_id'], 'empty' => false, 'style' => 'width:100%;', 'class' => 'form-control']) ?>
                                    <span class="text-red"><?= (isset($error['parent_id']['_empty'])) ? $error['parent_id']['_empty'] : null; ?></span>
                                </div>
                            </div>



                                <div class="form-group">
                                    <label class="col-sm-3 control-label">First Name <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->hidden('Users.id'); ?>
                                        <?= $this->Form->text('Users.firstname', ['class'=>'form-control','placeholder'=>'Enter first name']); ?>
                                        <span class="text-red"><?= (isset($error['firstname']['_empty'])) ? $error['firstname']['_empty']:null; ?></span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Last Name <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->text('Users.lastname', ['class'=>'form-control', 'placeholder'=>'Enter last name']); ?>
                                    	<span class="text-red"><?= (isset($error['lastname']['_empty'])) ? $error['lastname']['_empty']:null; ?></span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">User Name <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->label('Users.username', $user->username, ['class'=>'form-control']); ?>
                                    	<span class="text-red"><?= (isset($error['username']['_empty'])) ? $error['username']['_empty']:null; ?></span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Email <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->email('Users.email', ['class'=>'form-control', 'placeholder'=>'Enter valid email']); ?>
                                    	<span class="text-red">
                                    		<?= (isset($error['email']['_empty'])) ? $error['email']['_empty']:null; ?>
                                    		<?= (isset($error['email']['valid'])) ? $error['email']['valid']:null; ?>
                                    	</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Password&nbsp;</label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->password('Users.password', ['value'=>'', 'class'=>'form-control', 'placeholder'=>'Enter password']); ?>
                                    </div>
                                </div>
                                                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Confirm Password&nbsp;</label>
                                    <div class="col-sm-9">
                                        <?= $this->Form->password('confirm_password', ['class'=>'form-control', 'placeholder'=>'Enter confirm password']); ?>
                                    	<span class="text-red"><?= (isset($error['password']['match'])) ? $error['password']['match']:null; ?></span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Status&nbsp;</label>                                    
                                    <div class="col-sm-9">
                                    	<?= $this->Form->select('Users.is_active', $this->Admin->siteStatus, ['value'=>1,'empty'=>false,'style'=>'width:100%;','class'=>'form-control'])?>
                                    </div>
                                </div>
                                
                                <!-- div class="form-group">
                                    <label class="col-sm-3 control-label">Current Password <span class="text-red">*</span></label>
                                    <div class="col-sm-9">
                                        <?php // echo $this->Form->password('current_password', ['class'=>'form-control', 'placeholder'=>'Enter current password']); ?>
                                    	<span class="text-red"><?php //echo (isset($error['current_password']['custom'])) ? $error['current_password']['custom']:null; ?></span>
                                    </div>
                                </div -->
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">&nbsp;</label>                                    
                                    <div class="col-sm-9">
						                <?= $this->Form->button('Save', ['type' => 'submit', 'class'=>'btn btn-div-buy btn-1b']);?>&nbsp;&nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of box_content -->
                    </div><!-- end of box_div -->
                </div><!-- end of col_div -->
            </div><!-- end of middle_content -->
        </div><!-- end of tab -->
</section>
<?= $this->Form->end() ?>
