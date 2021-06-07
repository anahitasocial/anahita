<? defined('ANAHITA') or die('Restricted access'); ?>
<? $uploadSizeLimit = ini_get('upload_max_filesize'); ?>
<? $article = empty($article) ? @service('repos:articles.article')->getEntity()->reset() : $article; ?>

<? if (!empty($article->id)): ?>
<form action="<?= @route($article->getURL().'&oid='.$actor->id) ?>" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><?= @text('COM-ARTICLES-ARTICLE-COVER') ?></legend>
	
		<? if ($article->hasCover()): ?>
		<div class="control-group ">
			<?= @cover($article, 'medium', false) ?>
		</div>
		<? endif ?>
		
		<div class="control-group ">
			<label class="control-label" for="article-cover">
				<?= sprintf(@text('LIB-AN-COVER-SELECT-IMAGE-ON-YOUR-COMPUTER'), $uploadSizeLimit, 1600) ?>
			</label>
			
			<div class="controls">
				<input 
					type="file" 
					name="cover" 
					accept="image/*" 
					data-limit="<?= $uploadSizeLimit ?>" 
				/>
			</div>
		</div>
	</fieldset>
	
    <div class="form-actions">
		<? if ($article->hasCover()): ?>
        <button data-trigger="DeleteCover" class="btn btn-danger">
            <?= @text('LIB-AN-COVER-DELETE') ?>
        </button>
	    <? else: ?>
		<button class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-FILE-UPLOADING') ?>">
			<?= @text('LIB-AN-ACTION-UPLOAD') ?>
		</button>
		<? endif ?>
    </div>
</form>
<? endif; ?>

<form action="<?= @route($article->getURL().'&oid='.$actor->id) ?>" method="post">
	<fieldset>
		<legend><?= ($article->persisted()) ? @text('COM-ARTICLES-ARTICLE-EDIT') : @text('COM-ARTICLES-ARTICLE-ADD') ?></legend>

		<div class="control-group">
			<label class="control-label" for="article-title">
			    <?= @text('COM-ARTICLES-ARTICLE-TITLE') ?>
			</label>

			<div class="controls">
				<input 
					required 
					class="input-block-level" 
					id="article-title" 
					name="title" 
					value="<?= stripslashes($article->title) ?>" 
					maxlength="100" 
					type="text"
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
				><?= @escape($article->description) ?></textarea>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="article-excerpt">
			    <?= @text('COM-ARTICLES-ARTICLE-EXCERPT') ?>
			</label>

			<div class="controls">
				<textarea 
					maxlength="1000" 
					class="input-block-level" 
					name="excerpt" 
					cols="10" 
					rows="5" 
					id="article-excerpt"
				><?= @escape($article->excerpt) ?></textarea>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy', array('entity' => $article, 'auto_submit' => false, 'options' => $actor)) ?>
			</div>
		</div>

		<div class="form-actions">
			<a href="<?= ($article->persisted()) ? @route($article->getURL()) : @route('view=articles&oid='.$actor->id) ?>" class="btn">
				<?= @text('LIB-AN-ACTION-CLOSE') ?>
			</a>
		    <? $action = ($article->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-POST' ?>
	        <? $actionLoading = ($article->persisted()) ? 'LIB-AN-MEDIUM-UPDATING' : 'LIB-AN-MEDIUM-POSTING' ?>
	        <button class="btn btn-primary" data-loading-text="<?= @text($actionLoading) ?>">
	            <?= @text($action) ?>
	        </button>
	    </div>
	</fieldset>
</form>
