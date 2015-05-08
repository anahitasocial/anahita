<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $page = @service('repos:pages.page')->getEntity()->reset() ?>

<form class="composer-form" action="<?= @route() ?>" method="post">
	<fieldset>
		<legend><?= @text('COM-PAGES-PAGE-ADD') ?></legend>
	
		<div class="control-group">
			<label class="control-label" for="page-title"><?= @text('COM-PAGES-PAGE-TITLE') ?></label>
			<div class="controls">
				<input id="page-title" class="input-block-level" name="title" value="" maxlength="255" type="text" required autofocus />
			</div>
		</div>
		
		<div class="control-group">
            <label class="control-label" for="page-description">
                <?= @text('COM-PAGES-PAGE-DESCRIPTION') ?>
            </label> 
            
            <div class="controls">
                <?= @editor(array(
                    'name'=>'description',
                    'content'=> '', 
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
			<label class="control-label" for="page-excerpt"><?= @text('COM-PAGES-PAGE-EXCERPT') ?></label>
			<div class="controls">
				<input id="page-excerpt" class="input-block-level" name="excerpt" maxlength="250" type="text" required />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$page, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
		
		<div class="form-actions">			 
			<button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
			    <?= @text('LIB-AN-ACTION-PUBLISH') ?>
			</button>
		</div>
		
	</fieldset>
</form>