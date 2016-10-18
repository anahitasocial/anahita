<? defined('KOOWA') or die; ?>

<? if ($photo->authorize('edit')) : ?>
<div class="an-entity editable" data-url="<?= @route($photo->getURL()) ?>">
<? else : ?>
<div class="an-entity">
<? endif; ?>
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($photo->author) ?>
		</div>

		<div class="entity-container">
			<h4 class="author-name"><?= @name($photo->author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($photo->creationTime) ?></li>
				<? if (!$photo->owner->eql($viewer)): ?>
				<li><?= @name($photo->owner) ?></li>
				<? endif; ?>
			</ul>
		</div>
	</div>

	<div class="entity-portrait-medium">
		<a title="<?= @escape($photo->title) ?>" href="<?= @route($photo->getURL()) ?>">
			<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
		</a>
	</div>

	<div class="entity-description-wrapper">
		<? if ($photo->title): ?>
			<h3 class="entity-title">
				<a title="<?= @escape($photo->title) ?>" href="<?= @route($photo->getURL()) ?>">
					<?= @escape($photo->title) ?>
				</a>
			</h3>
		<? elseif ($photo->authorize('edit')) : ?>
			<h3 class="entity-title">
				<span class="muted"><?= @text('LIB-AN-EDITABLE-PLACEHOLDER') ?></span>
			</h3>
		<? endif; ?>

		<div class="entity-description">
			<?= @content(nl2br($photo->description), array('exclude' => array('gist', 'video'))) ?>
		</div>
	</div>

	<div class="entity-meta">
		<ul class="an-meta inline">
			<li><?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?></li>
			<? if ($photo->lastCommenter): ?>
			<li><?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($photo->lastCommenter), @date($photo->lastCommentTime)) ?></li>
			<? endif; ?>
		</ul>

		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
			<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>

	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>
