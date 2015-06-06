<?php defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">
		<?= @helper('ui.header', array()) ?>
		<?= @template('list') ?>
	</div>
	
	<div class="span4">
		<ul class="nav nav-pills nav-stacked" data-behavior="sortable">
			<li class="nav-header">
       		<?= @text('LIB-AN-SORT-TITLE') ?>
    		</li>
    		<li class="sort-option active">
				<a href="<?= @route('layout=list&sort=newest') ?>">
				<?= @text('LIB-AN-SORT-NEWEST') ?>
				</a>
			</li>
     
			<li class="sort-option">
				<a href="<?= @route('layout=list&sort=top') ?>">
				<?= @text('LIB-AN-SORT-TOP') ?>
				</a>
			</li>
	
			<li class="sort-option">
				<a href="<?= @route('layout=list&sort=updated') ?>">
				<?= @text('LIB-AN-SORT-UPDATED') ?>
				</a>
			</li>
		</ul>
	</div>
</div>