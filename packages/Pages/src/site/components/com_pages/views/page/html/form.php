<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $page = empty($page) ? @service('repos:pages.page')->getEntity()->reset() : $page; ?>

<form action="<?= @route( $page->getURL().'&oid='.$actor->id ) ?>" method="post">
	<fieldset>
		<legend><?= ($page->persisted()) ? @text('COM-PAGES-PAGE-EDIT') : @text('COM-PAGES-PAGE-ADD') ?></legend>
	
		<div class="control-group">
			<label class="control-label" for="page-title">
			    <?= @text('COM-PAGES-PAGE-TITLE') ?>
			</label> 
			
			<div class="controls">
				<input required class="input-block-level" id="page-title" name="title" value="<?= stripslashes( $page->title ) ?>" size="50" maxlength="255" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="page-description">
			    <?= @text('COM-PAGES-PAGE-DESCRIPTION') ?>
			</label> 
			
			<div class="controls">
				<?= @editor(array(
				    'name'=>'description',
				    'content'=> @escape($page->description), 
				    'html' => array(    
				        'maxlength'=>'20000', 
				        'cols'=>'5',
				        'rows'=>'5', 
				        'class'=>'input-block-level', 
				        'id'=>'page-description' 
                        )
                )); ?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="page-excerpt">
			    <?= @text('COM-PAGES-PAGE-EXCERPT') ?>
			</label> 
			
			<div class="controls">
				<textarea required maxlength="500" class="input-block-level" name="excerpt" cols="10" rows="5" id="page-excerpt"><?= @escape( $page->excerpt ) ?></textarea>
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
			
			<?php $action = ($page->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-POST' ?>
            <?php $actionLoading = ($page->persisted()) ? 'LIB-AN-MEDIUM-UPDATING' : 'LIB-AN-MEDIUM-POSTING' ?>
            <button class="btn btn-primary" data-loading-text="<?= @text($actionLoading) ?>">
                <?= @text($action) ?>
            </button>
		</div>
		
	</fieldset>
</form>