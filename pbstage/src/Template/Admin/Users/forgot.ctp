<div class="login-box">
	<div class="login-logo">
		<?= $this->Html->link(h($title), ['controller'=>'Users'],['class'=>'text-bold']) ?>
	</div>
	<div class="login-box-body">
		<p class="login-box-msg"><strong>Forgot password recovery!</strong></p>
		<?= $this->Flash->render();?>
		<?= $this->Form->create(); ?>
		<div class="form-group has-feedback">
			<?= $this->Form->control('email', ['type'=>'email', 'label'=>false, 'required'=>true, 'placeholder'=>'Please enter registered email', 'class'=>'form-control']); ?>
			<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div><?= $this->Form->button('Submit',['type'=>'submit', 'class'=>'btn btn-primary btn-block btn-flat']) ?></div>
			</div>
		<div class="form-group has-feedback">
			<div class="col-xs-12"><?= $this->Html->link('Sign In', ['controller'=>'Users', 'action'=>'login']); ?></div>
		</div>
		<?= $this->Form->end(); ?>
	</div>
</div>
