<? defined('KOOWA') or die ?>

<div class="an-entity an-record an-removable">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($item->author) ?>
		</div>

		<div class="entity-container">
			<h4 class="author-name"><?= @name($item->author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($item->creationTime) ?></li>
				<? if (!$item->owner->eql($item->author)): ?>
				<li><?= @name($item->owner) ?></li>
				<? endif; ?>
			</ul>
		</div>
	</div>

	<h3 class="entity-title">
		<a href="<?= @route($item->getURL()) ?>"><?= @escape($item->title) ?></a>
	</h3>

	<? if ($item->description): ?>
	<div class="entity-description">
		<?= @helper('text.truncate', @content($item->description), array('length' => 500, 'consider_html' => true, 'read_more' => true)); ?>
	</div>
	<? endif; ?>

	<div class="entity-meta">
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $item->id ?>">
			<?= @helper('ui.voters', $item); ?>
		</div>
	</div>

	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>
