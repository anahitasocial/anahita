<?php defined('KOOWA') or die('Restricted access');?>

<script src="media://com_invites/js/email.js" />

<module position="sidebar-b" style="simple"></module>

<?php $num_emails = 5; ?>

<form data-behavior="FormValidator" id="email-invites" action="<?= @route() ?>" method="post">
	<?php for($i=0; $i<$num_emails; $i++): ?>
	
	<div class="control-group">
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><i class="icon-envelope"></i></span>
				<input class="email required validate-email span3" type="text" name="email[]" autocomplete="off" placeholder="<?= sprintf(@text('COM-INVITES-EMAIL-FIELD'), $i+1) ?>" />
			</div>
		</div>
	</div>
	<?php endfor; ?>
	
	<div class="form-actions">
		<button class="btn btn-primary" data-trigger="Invite">
			<?= @text('COM-INVITES-EMAIL-SEND-INVITES') ?>
		</button>
	</div>
</form>
