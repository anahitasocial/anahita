<?php defined('KOOWA') or die('Restricted access') ?>

<div class="row">
	<div class="offset3 span6">
		<form data-behavior="FormValidator " action="<?= @route('view=token') ?>" method="post" class="well">
		<fieldset>
			<legend><?= @text('COM-PEOPLE-TOKEN-FORGOT-PASSWORD') ?></legend>                        
            <div id="flash-message"></div>
			<div class="control-group">				
				<div class="controls">
					<p><?= @text('COM-PEOPLE-TOKEN-FORGOT-PASSWORD-INSTRUCTIONS') ?></p>
					<input class="input-block-level" id="email" name="email" type="text" placeholder="<?= @text('COM-PEOPLE-EMAIL-PLACEHOLDER') ?>"  data-validators="required validate-email" />
				</div>
			</div>
		
			<div class="form-actions">
				<button data-trigger="Request" type="submit" class="btn btn-large pull-right">
					<?= @text('COM-PEOPLE-ACTION-RESET-PASSWORD'); ?>
				</button>
			</div>
		</fieldset>
		</form>
	</div>
</div>