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
				<? if (!$photo->owner->eql($photo->author)): ?>
				<li><?= @name($photo->owner) ?></li>
				<? endif; ?>
			</ul>
		</div>
	</div>

	<div class="entity-portrait-medium">
		<? $caption = htmlspecialchars($photo->title, ENT_QUOTES); ?>
		<a data-rel="media-photos-<?= $photo->id ?>" data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $photo->getPortraitURL('original') ?>">
			<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
		</a>
	</div>

	<div class="entity-description-wrapper">
		<? if ($photo->title): ?>
			<h4 class="entity-title">
				<a title="<?= @escape($photo->title) ?>" href="<?= @route($photo->getURL()) ?>">
					<?= @escape($photo->title) ?>
				</a>
			</h4>
		<? elseif ($photo->authorize('edit')) : ?>
			<h4 class="entity-title">
				<span class="muted"><?= @text('LIB-AN-EDITABLE-PLACEHOLDER') ?></span>
			</h4>
		<? endif; ?>

    	<div class="entity-description">
    	<?= @helper('text.truncate', @content(nl2br($photo->description), array('exclude' => array('gist', 'video'))), array('length' => 200, 'read_more' => true, 'consider_html' => true)); ?>
    	</div>
	</div>

	<div class="entity-meta">
		<ul class="an-meta inline">
			<li>
				<a href="<?= @route($photo->getURL()) ?>">
				<?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?>
				</a>
			</li>
		</ul>

		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
			<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>

	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>
