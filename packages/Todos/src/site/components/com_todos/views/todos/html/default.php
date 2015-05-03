<?php defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">	
		<?= @helper('ui.header', array()) ?>
	
		<?php if($actor && $actor->authorize('action', 'todo:add')) : ?>
		<div id="entity-form-wrapper" class="hide">
		<?= @view('todo')->layout('form')->actor($actor) ?>
		</div>
		<?php endif; ?>
		
		<?= @helper('ui.filterbox', @route('layout=list')) ?>
		<?= @template('list') ?>
	</div>
	
	<div class="span4 visible-desktop">			
		<ul class="nav nav-pills nav-stacked" data-behavior="sortable">
			<li class="nav-header">
		       <?= @text('LIB-AN-SORT-TITLE') ?>
		    </li>
		    <?php $sorts = array('newest', 'priority', 'updated') ?>
		    <?php foreach($sorts as $sort): ?>
			<?php $active = ($sort == 'newest') ? 'active' : '' ?>
			<li class="sort-option <?= $active ?>">
				<a href="<?= @route('layout=list&sort='.$sort) ?>">
				<?= @text('LIB-AN-SORT-'.$sort) ?>
				</a>
			</li>
		    <?php endforeach; ?>
		</ul>
	</div>
</div>