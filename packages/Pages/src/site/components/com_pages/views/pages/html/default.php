<?php defined('KOOWA') or die; ?>

<module position="sidebar-b">
<ul class="nav nav-pills nav-stacked">
	<li class="nav-header">
       <?= @text('LIB-AN-SORT-TITLE') ?>
    </li>
    
    <li class="sort-option active">
		<a data-trigger="Request" data-request-options="SortOption" href="<?= @route('layout=list&sort=newest') ?>">
		<?= @text('LIB-AN-SORT-NEWEST') ?>
		</a>
	</li>
     
	<li class="sort-option">
		<a data-trigger="Request" data-request-options="SortOption" href="<?= @route('layout=list&sort=top') ?>">
		<?= @text('LIB-AN-SORT-TOP') ?>
		</a>
	</li>
	
	<li class="sort-option">
		<a data-trigger="Request" data-request-options="SortOption" href="<?= @route('layout=list&sort=updated') ?>">
		<?= @text('LIB-AN-SORT-UPDATED') ?>
		</a>
	</li>
</ul>
</module>

<div class="an-entities-wrapper" id="an-entities-main-wrapper">
<?= @template('list') ?>
</div>