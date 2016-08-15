<? defined('KOOWA') or die; ?>

<div class="an-entity">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($item->author) ?>
		</div>

		<div class="entity-container">
			<h4 class="author-name">
				<?= @name($item->author) ?>
			</h4>

			<ul class="an-meta inline">
				<li><?= @date($item->creationTime) ?></li>
				<li>
					<a href="<?= @route($item->parent->getURL()).'#permalink='.$item->id ?>">
						<?= @text('LIB-AN-COMMENT-VIEW-POST') ?>
					</a>
				</li>
			</ul>
		</div>
	</div>

	<? if (!empty($item->parent->title)): ?>
	<h3 class="entity-title">
		<a href="<?= @route($item->parent->getURL()).'#permalink='.$item->id ?>">
			<?= @escape($item->parent->title) ?>
		</a>
	</h3>
	<? endif; ?>

	<div class="entity-description">
		<?= @helper('text.truncate', @content($item->body), array('length' => 400, 'consider_html' => true)) ?>
	</div>

	<div class="entity-meta">
		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $item->id ?>">
			<?= @helper('ui.voters', $item); ?>
		</div>
	</div>
</div>
