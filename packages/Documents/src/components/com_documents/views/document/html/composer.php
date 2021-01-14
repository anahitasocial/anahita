<? defined('ANAHITA') or die('Restricted access'); ?>

<? $uploadSizeLimit = get_config_value('documents.uploadlimit', 2); ?>
<? $document = @service('repos:documents.document')->getEntity()->reset() ?>

<form class="composer-form" method="post" action="<?= @route() ?>" enctype="multipart/form-data">
    <fieldset>
	    <legend><?= @text('COM-DOCUMENTS-DOCUMENT-ADD')  ?></legend>

		<div class="control-group">
			<label class="control-label" for="document-file">
			    <?= @text('COM-DOCUMENTS-COMPOSER-FILE-SELECT') ?>
			</label>
			<div class="controls">
				<input 
                    accept="application/*" 
                    id="document-file" 
                    type="file" 
                    name="file" 
                    required 
                    autofocus 
                    data-limit="<?= $uploadSizeLimit ?>" 
                />
			</div>
		</div>
        
        <div class="control-group">
			<label class="control-label" for="document-description">
			    <?= @text('COM-DOCUMENTS-COMPOSER-DOCUMENT-POST-NAME') ?>
			</label>
			<div class="controls">
                <input 
					id="document-name" 
					class="input-block-level" 
					name="name"
					maxlength="100" 
					type="text" 
					required 
					autofocus 
				/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="document-description">
			    <?= @text('COM-DOCUMENTS-COMPOSER-DOCUMENT-POST-DESCRIPTION') ?>
			</label>
			<div class="controls">
				<textarea 
                    id="document-description" 
                    class="input-block-level" 
                    name="body" 
                    cols="5" 
                    rows="3" 
                    maxlength="5000"
                ></textarea>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="privacy">
			    <?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?>
			</label>
			<div class="controls">
				<?= @helper('ui.privacy', array('entity' => $document, 'auto_submit' => false, 'options' => $actor)) ?>
			</div>
		</div>

	</fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
            <?=@text('LIB-AN-ACTION-SHARE')?>
        </button>
    </div>
</form>
