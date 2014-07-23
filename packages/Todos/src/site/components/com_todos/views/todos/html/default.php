<?php defined('KOOWA') or die; ?>

<module position="sidebar-b">
<ul class="nav nav-pills nav-stacked">
	<li class="nav-header">
       <?= @text('LIB-AN-SORT-TITLE') ?>
    </li>
    
    <?php $active = ($sort == 'newest') ? 'active' : '' ?>
    <li class="sort-option <?= $active ?>">
		<a data-trigger="Request" data-request-options="SortEntities" href="<?= @route('layout=list&sort=newest') ?>">
		<?= @text('LIB-AN-SORT-NEWEST') ?>
		</a>
	</li>
     
    <?php $active = ($sort == 'priority') ? 'active' : '' ?>
	<li class="sort-option <?= $active ?>">
		<a data-trigger="Request" data-request-options="SortEntities" href="<?= @route('layout=list&sort=priority') ?>">
		<?= @text('COM-TODOS-TODO-SORT-PRIORITY') ?>
		</a>
	</li>
	
	<?php $active = ($sort == 'updated') ? 'active' : '' ?>
	<li class="sort-option <?= $active ?>">
		<a data-trigger="Request" data-request-options="SortEntities" href="<?= @route('layout=list&sort=updated') ?>">
		<?= @text('LIB-AN-SORT-UPDATED') ?>
		</a>
	</li>
</ul>
</module>

<div id="entity-add-wrapper" class="hide">
<?= @view('todo')->layout('form')->actor($actor) ?>
</div>

<?= @helper('ui.filterbox', @route('layout=list')) ?>

<div class="an-entities-wrapper" id="an-entities-main-wrapper">
	<?= @template('list') ?>
</div>