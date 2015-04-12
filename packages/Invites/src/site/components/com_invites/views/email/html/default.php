<?php defined('KOOWA') or die('Restricted access');?>

<script src="media://com_invites/js/email.js" />

<?php $num_emails = 5; ?>

<div class="row">
	<div class="span8">
	    
		<?= @helper('ui.header', array()) ?>
	    	
        <form id="invites-email" name="invites-email" action="<?= @route() ?>" method="post">
        	<?php for ( $i=0; $i < $num_emails; $i++ ) : ?>
        	
        	<div class="control-group">
        	    <label class="control-label"  for="email-<?= $i ?>">
                    <?= sprintf(@text('COM-INVITES-EMAIL-FIELD'), $i + 1 ) ?>
                </label>
        		<div class="controls">
        			<?php $emailPattern = "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" ?>
        			<input id="email-<?= $i ?>" pattern="<?= $emailPattern ?>" maxlength="100" class="input-block-level" type="email" name="email[]" autocomplete="off" />
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
