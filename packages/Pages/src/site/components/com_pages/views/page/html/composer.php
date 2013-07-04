<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $page = @service('repos:pages.page')->getEntity()->reset() ?>

<form data-behavior="FormValidator ComposerForm" action="<?= @route( $page->getURL().'&oid='.$actor->id ) ?>" method="post">
	<fieldset>
		<legend><?= @text('COM-PAGES-PAGE-ADD') ?></legend>
	
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('COM-PAGES-PAGE-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" class="input-block-level" name="title" value="" maxlength="255" type="text" tabindex="1">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="description"><?= @text('COM-PAGES-PAGE-DESCRIPTION') ?></label>
			<div class="controls">
			    <textarea data-validators="required maxLength:5000" class="input-block-level" name="body" cols="10" rows="5" id="an-pages-page-excerpt" tabindex="2"></textarea>				
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="excerpt"><?= @text('COM-PAGES-PAGE-EXCERPT') ?></label>
			<div class="controls">
				<input data-validators="required maxLength:500" class="input-block-level" name="excerpt" value="" maxlength="250" type="text" id="an-pages-page-excerpt" tabindex="3">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="published"><?= @text('COM-PAGES-PAGE-ENABLED') ?></label>
			<div class="controls">
				<?= @html('select','enabled', array('options'=>array(@text('LIB-AN-NO'), @text('LIB-AN-YES')), 'selected'=>true))->class('small')?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$page, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
		
		<div class="form-actions">			 
			<button type="submit" class="btn btn-primary" id="an-pages-button-save"><?= @text('LIB-AN-ACTION-PUBLISH') ?></button>
		</div>
		
	</fieldset>
</form>