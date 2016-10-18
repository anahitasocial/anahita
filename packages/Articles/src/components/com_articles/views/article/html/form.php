<? defined('KOOWA') or die('Restricted access'); ?>

<? $article = empty($article) ? @service('repos:articles.article')->getEntity()->reset() : $article; ?>

<form action="<?= @route($article->getURL().'&oid='.$actor->id) ?>" method="post">
	<fieldset>
		<legend><?= ($article->persisted()) ? @text('COM-ARTICLES-ARTICLE-EDIT') : @text('COM-ARTICLES-ARTICLE-ADD') ?></legend>

		<div class="control-group">
			<label class="control-label" for="article-title">
			    <?= @text('COM-ARTICLES-ARTICLE-TITLE') ?>
			</label>

			<div class="controls">
				<input required class="input-block-level" id="article-title" name="title" value="<?= stripslashes($article->title) ?>" size="50" maxlength="255" type="text">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="article-description">
			    <?= @text('COM-ARTICLES-ARTICLE-DESCRIPTION') ?>
			</label>

			<div class="controls">
				<?= @editor(array(
                    'name' => 'description',
                    'content' => @escape($article->description),
                    'html' => array(
                        'maxlength' => '20000',
                        'cols' => '5',
                        'rows' => '5',
                        'class' => 'input-block-level',
                        'id' => 'article-description',
                        ),
                )); ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="article-excerpt">
			    <?= @text('COM-ARTICLES-ARTICLE-EXCERPT') ?>
			</label>

			<div class="controls">
				<textarea required maxlength="500" class="input-block-level" name="excerpt" cols="10" rows="5" id="article-excerpt"><?= @escape($article->excerpt) ?></textarea>
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
