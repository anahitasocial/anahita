<?php defined('KOOWA') or die('Restricted access');?>

<script src="media://com_invites/js/email.js" />

<?php $num_emails = 5; ?>

<div class="row">
	<div class="span8">
	    
		<?= @helper('ui.header', array()) ?>
	    	
        <form id="invites-email" name="invites-email" action="<?= @route() ?>" method="post">
        	<?php for($i=0; $i<$num_emails; $i++): ?>
        	
        	<div class="control-group">
        		<div class="controls">
        			<div class="input-prepend">
        				<span class="add-on"><i class="icon-envelope"></i></span>
        				<?php $emailPattern = "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" ?>
        				<input pattern="<?= $emailPattern ?>" maxlength="100" class="email span3" type="email" name="email[]" autocomplete="off" placeholder="<?= sprintf(@text('COM-INVITES-EMAIL-FIELD'), $i+1) ?>" />
        			</div>
        		</div>
        	</div>
        	<?php endfor; ?>
        	
        	<div class="form-actions">
        		<button type="submit" class="btn btn-primary">
        			<?= @text('COM-INVITES-EMAIL-SEND-INVITES') ?>
        		</button>
        	</div>
        </form>
	</div>
</div>
