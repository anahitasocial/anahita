<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $page = empty($page) ? @service('repos:pages.page')->getEntity()->reset() : $page; ?>

<form data-behavior="FormValidator" action="<?= @route( $page->getURL().'&oid='.$actor->id ) ?>" method="post">
	<fieldset>
		<legend><?= ($page->persisted()) ? @text('COM-PAGES-PAGE-EDIT') : @text('COM-PAGES-PAGE-ADD') ?></legend>
	
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('COM-PAGES-PAGE-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" class="input-block-level" name="title" value="<?= stripslashes( $page->title ) ?>" size="50" maxlength="255" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="description"><?= @text('COM-PAGES-PAGE-DESCRIPTION') ?></label>
			<div class="controls">
				<?= @editor(array('name'=>'description', 'extended'=>true,'content'=> @escape($page->description), 'html'=>array('data-validators'=>'maxLength:20000', 'cols'=>'10','rows'=>'30', 'class'=>'input-block-level'))) ?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="excerpt"><?= @text('COM-PAGES-PAGE-EXCERPT') ?></label>
			<div class="controls">
				<textarea data-validators="required maxLength:500" class="input-block-level" name="excerpt" cols="10" rows="5" id="an-pages-page-excerpt"><?= @escape( $page->excerpt ) ?></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="published"><?= @text('COM-PAGES-PAGE-ENABLED') ?></label>
			<div class="controls">
				<?= @html('select','enabled', array('options'=>array(@text('LIB-AN-NO'), @text('LIB-AN-YES')), 'selected'=>$page->enabled))->class('small')?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$page, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
		
		<div class="form-actions">
			<a href="<?= ($page->persisted()) ? @route($page->getURL()) : @route('view=pages&oid='.$actor->id) ?>" class="btn">
				<?= @text('LIB-AN-ACTION-CLOSE') ?>
			</a>  
			<button type="submit" class="btn btn-primary" id="an-pages-button-save"><?= @text( ($page->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-PUBLISH') ?></button>
		</div>
		
	</fieldset>
</form>

<?= @message(@text('COM-PAGES-PAGE-ALLOWED-MARKUP-INSTRUCTIONS')) ?>