<?php defined('KOOWA') or die('Restricted access');?>

<script src="com_photos/js/organizer.js" />

<module position="sidebar-b" style="none"></module>

<form data-behavior="FormValidator" id="set-form" method="post" action="<?= @route('view=set&oid='.$actor->id) ?>">
	<input type="hidden" value="addphoto" name="action" />
	<fieldset>
		<legend><?= @text('COM-PHOTOS-SET-ADD') ?></legend>
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('LIB-AN-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" class="input-block-level" name="title" size="50" maxlength="255" tabindex="1" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="description"><?= @text('LIB-AN-MEDIUM-DESCRIPTION') ?></label>
			<div class="controls">
				<textarea data-validators="maxLength:5000" class="input-block-level" name="description" cols="50" rows="5" tabindex="2"></textarea>
			</div>
		</div>

		<div id="medium-selector">
			<?= @controller('photos')->view('photos')->oid($actor->id)->layout('selector') ?>
		</div>
		
		<div id="set-mediums" class="set-mediums an-entities" oid="<?= $actor->id ?>" set_id="0">
			<div class="media-grid"></div>
		</div>
			
		<div class="form-actions">
			<a data-trigger="Cancel" class="btn" href="<?= @route(array('view'=>'sets', 'oid'=>$actor->id)) ?>"><?= @text('LIB-AN-ACTION-CANCEL') ?></a> 
			<button data-trigger="Add" class="btn btn-primary" tabindex="3"><?= @text('LIB-AN-ACTION-ADD') ?></button>
		</div>
	</fieldset>
</form>