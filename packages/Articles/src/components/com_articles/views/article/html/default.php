<? defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header') ?>
	<?= @template('article') ?>
	<?= @helper('ui.comments', $article) ?>
	</div>

	<div class="span4 visible-desktop">
		<h4 class="block-title">
		<?= @text('COM-ARTICLES-META-ADDITIONAL-INFORMATION') ?>
		</h4>

		<div class="block-content">
    		<ul class="an-meta">
    			<li><?= sprintf(@text('LIB-AN-ENTITY-EDITOR'), @date($article->updateTime), @name($article->editor)) ?></li>
    			<li><?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $article->numOfComments) ?></li>
    		</ul>
		</div>

		<? if(count($article->locations) || $article->authorize('edit')): ?>
		<h4 class="block-title">
			<?= @text('LIB-AN-ENTITY-LOCATIONS') ?>
		</h4>

		<div class="block-content">
		<?= @location($article) ?>
		</div>
		<? endif; ?>

		<? if ($actor->authorize('administration')): ?>
		<h4 class="block-title">
		<?= @text('COM-ARTICLES-ARTICLE-PRIVACY') ?>
		</h4>

		<div class="block-content">
		<?= @helper('ui.privacy', $article) ?>
		</div>
		<? endif; ?>
	</div>
</div>
