<?php defined('KOOWA') or die ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-DELETE') ?></h3>

<form action="<?=@route($item->getURL())?>" method="post">
	<input type="hidden" name="action" value="delete" />	
	
	<div class="alert alert-block alert-warning">
  		<p><?= $msg = sprintf(translate(array($item->component.'-DELETE-PROMPT','COM-ACTORS-DELETE-PROMPT'))) ?></p>
  		
  		<p>
  			<button data-trigger="Remove" data-remove-form="!form" data-remove-confirm-message="<?= $msg?>"  class="btn btn-danger"><?= @text('LIB-AN-ACTION-DELETE') ?></button>
  		</p>
	</div>
</form>