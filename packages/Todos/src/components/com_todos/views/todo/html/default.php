<? defined('KOOWA') or die ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header'); ?>
	<?= @template('todo'); ?>
	<? @commands('toolbar') ?>
	<?= @helper('ui.comments', $todo, array('pagination' => true)); ?>
	</div>

	<div class="span4 visible-desktop">
		<h4 class="block-title">
		<?= @text('LIB-AN-META') ?>
		</h4>

		<div class="block-content">
    		<ul class="an-meta">
    			<? if (isset($todo->editor)) : ?>
    			<li><?= sprintf(@text('LIB-AN-ENTITY-EDITOR'), @date($todo->updateTime), @name($todo->editor)) ?></li>
    			<? endif; ?>
    			<? if (!$todo->open) : ?>
    			<li>
    				<?= sprintf(@text('COM-TODOS-TODO-COMPLETED-BY-REPORT'), @date($todo->openStatusChangeTime), @name($todo->lastChanger)) ?>
    			</li>
    			<? endif; ?>
    			<li><?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $todo->numOfComments) ?></li>
    		</ul>
		</div>

		<? if(count($todo->locations) || $todo->authorize('edit')): ?>
		<h4 class="block-title">
			<?= @text('LIB-AN-ENTITY-LOCATIONS') ?>
		</h4>

		<div class="block-content">
		<?= @location($todo) ?>
		</div>
		<? endif; ?>

		<? if ($actor->authorize('administration')): ?>
		<h4 class="block-title">
		    <?= @text('COM-TODOS-TODO-PRIVACY') ?>
		</h4>

		<div class="block-content">
		    <?= @helper('ui.privacy', $todo) ?>
		</div>
		<? endif; ?>
	</div>
</div>
