<? defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">
		<?= @helper('ui.header') ?>

		<? if ($actor && $actor->authorize('action', 'todo:add')) : ?>
		<div id="entity-form-wrapper" class="hide">
		<?= @view('todo')->layout('form')->actor($actor) ?>
		</div>
		<? endif; ?>

		<?= @helper('ui.filterbox', @route('layout=list')) ?>
		<?= @template('list') ?>
	</div>

	<div class="span4 visible-desktop">
		<ul class="nav nav-pills nav-stacked" data-behavior="sortable">
			<li class="nav-header">
		       <?= @text('LIB-AN-SORT-TITLE') ?>
		    </li>
		    <? $sorts = array('newest', 'priority', 'updated') ?>
		    <? foreach ($sorts as $sort): ?>
			<? $active = ($sort == 'newest') ? 'active' : '' ?>
			<li class="sort-option <?= $active ?>">
				<a href="<?= @route('layout=list&sort='.$sort) ?>">
				<?= @text('LIB-AN-SORT-'.$sort) ?>
				</a>
			</li>
		    <? endforeach; ?>
		</ul>
	</div>
</div>
