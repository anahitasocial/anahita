<? defined('KOOWA') or die; ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_actors/js/cover.js" />
<? else: ?>
<script src="com_actors/js/min/cover.min.js" />
<? endif; ?>

<? if ($article->coverSet()): ?>
<div
	class="cover-container parallax-window"
	data-parallax="scroll"
	data-image-src="<?= $article->getCoverURL('large'); ?>"
	data-src-large="<?= $article->getCoverURL('large'); ?>"
	data-src-medium="<?= $article->getCoverURL('medium'); ?>">
</div>
<? endif; ?>

<div class="row-fluid<?= ($article->coverSet()) ? ' has-cover' : '' ?>" id="node-container">
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
