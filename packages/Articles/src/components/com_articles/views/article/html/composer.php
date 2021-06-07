<? defined('ANAHITA') or die('Restricted access'); ?>

<? $article = @service('repos:articles.article')->getEntity()->reset() ?>

<form class="composer-form" action="<?= @route() ?>" method="post">
	<fieldset>
		<legend><?= @text('COM-ARTICLES-ARTICLE-ADD') ?></legend>

		<div class="control-group">
			<label class="control-label" for="article-title"><?= @text('COM-ARTICLES-ARTICLE-TITLE') ?></label>
			<div class="controls">
				<input 
					id="article-title" 
					class="input-block-level" 
					name="title"  
					maxlength="100" 
					type="text" 
					required 
					autofocus 
				/>
			</div>
		</div>

		<div class="control-group">
            <label class="control-label" for="article-description">
                <?= @text('COM-ARTICLES-ARTICLE-DESCRIPTION') ?>
            </label>

            <div class="controls">
				<textarea 
					maxlength="40000" 
					class="input-block-level" 
					name="description" 
					cols="10" 
					rows="5" 
					id="article-description"
				></textarea>
            </div>
        </div>

		<div class="control-group">
			<label class="control-label" for="article-excerpt"><?= @text('COM-ARTICLES-ARTICLE-EXCERPT') ?></label>
			<div class="controls">
				<input 
					id="article-excerpt" 
					class="input-block-level" 
					name="excerpt" 
					maxlength="1000" 
					type="text" 
				/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy', array('entity' => $article, 'auto_submit' => false, 'options' => $actor)) ?>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
			    <?= @text('LIB-AN-ACTION-PUBLISH') ?>
			</button>
		</div>

	</fieldset>
</form>
