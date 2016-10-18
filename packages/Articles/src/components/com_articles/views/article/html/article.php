<? defined('KOOWA') or die ?>

<? @commands('toolbar') ?>

<div class="an-entity">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($article->author) ?>
		</div>

		<div class="entity-container">
		    <? if ($article->owner->authorize('administration') && $article->pinned): ?>
            <span class="label label-info pull-right"><?= @text('LIB-AN-PINNED') ?></span>
            <? endif; ?>
			<h4 class="author-name"><?= @name($article->author) ?></h4>
			<div class="an-meta">
				<?= @date($article->creationTime) ?>
			</div>
		</div>
	</div>

	<h1 class="entity-title">
		<?= @escape($article->title) ?>
	</h1>

	<? if ($article->description): ?>
	<div class="entity-description">
		<?= @content($article->description) ?>
	</div>
	<? endif; ?>

	<div class="entity-meta">
		<div id="vote-count-wrapper-<?= $article->id ?>">
			<?= @helper('ui.voters', $article); ?>
		</div>
	</div>
</div>
