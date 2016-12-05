<? defined('KOOWA') or die('Restricted access');?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="media://com_invites/js/email.js" />
<? else: ?>
<script src="media://com_invites/js/min/email.min.js" />
<? endif; ?>

<? $numEmails = 3; ?>

<div class="row">
	<div class="span8">

		<?= @helper('ui.header') ?>

        <form id="invites-email" name="invites-email" action="<?= @route() ?>" method="post">
        	<? for ($i = 0; $i < $numEmails; ++$i) : ?>

        	<div class="control-group">
        	    <label class="control-label"  for="email-<?= $i ?>">
                    <?= sprintf(@text('COM-INVITES-EMAIL-FIELD'), $i + 1) ?>
                </label>
        		<div class="controls">
        			<? $emailPattern = "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" ?>
        			<input id="email-<?= $i ?>" pattern="<?= $emailPattern ?>" maxlength="100" class="input-block-level" type="email" name="email[]" autocomplete="off" />
        		</div>
        	</div>
        	<? endfor; ?>

        	<div class="form-actions">
        		<button type="submit" class="btn btn-primary">
        			<?= @text('COM-INVITES-EMAIL-SEND-INVITES') ?>
        		</button>
        	</div>
        </form>
	</div>
</div>
