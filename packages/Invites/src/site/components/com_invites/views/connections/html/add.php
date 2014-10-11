<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
	<div class="span8">
		<?= @helper('ui.header', array()) ?>
		
		<div class="form-actions">
			<a data-trigger="Submit" href="<?= @route('option=connect&view=setting&server=facebook&oid='.$viewer->id.'&return='.base64_encode(@route('service=facebook'))) ?>" class="btn btn-primary">
            <?= @text('COM-INVITES-ACTION-FB-ADD-ACCOUNT') ?>
			</a>
		</div>
	</div>
</div>