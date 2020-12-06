<div class="login-box">
  <div class="login-logo">
		<?= $this->Html->link(h($title), ['controller'=>'Users'],['class'=>'text-bold']); ?>
  </div>
  <div class="login-box-body">
  <p class="login-box-msg"><strong><?php echo $now ?></strong></p>
  <p class="login-box-msg"><strong>Please login authorized users only!</strong></p>
    <?= $this->Flash->render();?>
    <?= $this->Form->create(); ?>
      <div class="form-group has-feedback">
			<?= $this->Form->control('username', ['type'=>'text', 'label'=>false, 'placeholder'=>'Enter username', 'class'=>'form-control']); ?>
			<span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
			<?= $this->Form->control('password', ['type'=>'password', 'label'=>false, 'placeholder'=>'Enter password', 'class'=>'form-control']); ?>
			<span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-12">
			<?= $this->Form->button('Sign In',['type'=>'submit', 'class'=>'btn btn-primary btn-block btn-flat']); ?>
        </div>
      </div>
      <div class="form-group has-feedback">
        <div class="col-xs-12">
			<?= $this->Html->link('Forgot Password', ['controller'=>'Users', 'action'=>'forgot']); ?>
        </div>
      </div>
    <?= $this->Form->end(); ?>
  </div>
</div>
