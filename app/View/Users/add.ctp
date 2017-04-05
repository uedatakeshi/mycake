<div class="users form">
<?php echo $this->Form->create('User', array('type' => 'file')); ?>
	<fieldset>
		<legend><?php echo __('Add User'); ?></legend>
	<?php
        echo $this->Form->hidden('webroot', array('value' => $this->webroot));
		echo $this->Form->input('username');
		echo $this->Form->input('password');
		echo $this->Form->input('name');
		echo $this->Form->input('email');
        echo $this->Form->input('User.photo', array('type' => 'file'));
        echo $this->Form->input('User.photo_dir', array('type' => 'hidden'));
		echo $this->Form->input('role');
		echo $this->Form->input('status');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Users'), array('action' => 'index')); ?></li>
	</ul>
</div>
