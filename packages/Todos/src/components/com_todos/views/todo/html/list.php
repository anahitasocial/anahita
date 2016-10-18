<? defined('KOOWA') or die ?>

<? $highlight = ($todo->open) ? 'an-highlight' : '' ?>
<div class="an-entity <?= $highlight ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($todo->author) ?>
		</div>

		<div class="entity-container">
			<h4 class="author-name">
			    <?= @name($todo->author) ?>
			</h4>

			<ul class="an-meta inline">
				<li><?= @date($todo->creationTime) ?></li>
				<? if (!$todo->owner->eql($todo->author)): ?>
				<li><?= @name($todo->owner) ?></li>
				<? endif; ?>
			</ul>
		</div>
	</div>

	<h3 class="entity-title">
		<a href="<?= @route($todo->getURL()) ?>">
		    <?= @escape($todo->title) ?>
		</a>
	</h3>

	<? if ($todo->description): ?>
	<div class="entity-description">
		<?= @helper('text.truncate', @content($todo->description), array('length' => 500, 'consider_html' => true, 'read_more' => true)); ?>
	</div>
	<? endif; ?>

	<div class="entity-meta">
		<ul class="an-meta inline">
			<li><?= @text('COM-TODOS-TODO-PRIORITY') ?>: <span class="priority <?= @helper('priorityLabel', $todo) ?>"><?= @helper('priorityLabel', $todo) ?></span></li>
			<li><?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $todo->numOfComments) ?></li>

			<? if (!$todo->open) : ?>
			<li><?= sprintf(@text('COM-TODOS-TODO-COMPLETED-BY-REPORT'), @date($todo->openStatusChangeTime), @name($todo->lastChanger)) ?></li>
		<? endif; ?>
		</ul>

		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todo->id ?>">
			<?= @helper('ui.voters', $todo); ?>
		</div>
	</div>

	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>
