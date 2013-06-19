<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'login')) ?>

<module position="sidebar-b" style="none"></module>

<h2><?= @text('COM-SUB-LOGIN-PROMPT-TITLE') ?></h2>
<p><?= @text('COM-SUB-LOGIN-PROMPT-DESCRIPTION'); ?></p>

<?php @template('_login_form', array('username'=>'', 'password'=>'', 'return_url'=>base64_encode(@route(array('id'=>$item->id, 'layout'=>'payment'))))) ?>

<div class="form-actions">
	<a data-trigger="BS.showPopup" class="btn btn-large btn-primary" data-bs-showpopup-url="<?= @route('option=people&view=session&layout=modal_simple&return='.base64UrlEncode(@route('layout=payment')))?>">
		<?=@text('COM-SUB-LOGIN-ACTION')?>
	</a>
</div>

<h2><?= @text('COM-SUB-REGISTER-PROMPT-TITLE') ?></h2>
<p><?= @text('COM-SUB-REGISTER-PROMMPT-DESCRIPTION') ?></p>

<form data-behavior="FormValidator" action="<?=@route('id='.$item->id)?>" method="post">
	<input type="hidden" name="action" value="payment" />
			
	<?= @helper('ui.form', array(
		'COM-SUB-REGISTER-FULL-NAME'       => @html('textfield', 'user[name]', $person->name)->dataValidators('required'),
		'COM-SUB-REGISTER-USERNAME'	       => @html('textfield', 'user[username]', $person->username)->dataValidators('required validate-remote url:\''.@route('option=com_people&view=person', false).'\' key:\'username\''),
		'COM-SUB-REGISTER-EMAIL'		   => @html('textfield', 'user[email]', $person->email)->dataValidators('required validate-email validate-remote url:\''.@route('option=com_people&view=person', false).'\' key:\'email\''),
		'COM-SUB-REGISTER-PASSWORD'		   => @html('passwordfield',  'user[password]', '')->dataValidators('required')				
	)); ?>
			
	<div class="form-actions">
		<button type="submit" class="btn btn-large"><?=@text('COM-SUB-REGISTER-ACTION')?></button>
	</div>
</form>