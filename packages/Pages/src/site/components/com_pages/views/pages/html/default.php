<?php defined('KOOWA') or die; ?>

<module position="sidebar-b">
<ul class="nav nav-pills nav-stacked">
	<li class="nav-header">
       <?= @text('COM-PAGES-ORDERING') ?>
    </li> 
	<li class="page-ordering active">
		<a data-trigger="Request" data-request-options="OrderPagesOption" href="<?= @route('layout=list&order=creationTime') ?>">
		<?= @text('COM-PAGES-ORDERING-CREATION-TIME') ?>
		</a>
	</li>
	<li class="page-ordering">
		<a data-trigger="Request" data-request-options="OrderPagesOption" href="<?= @route('layout=list&order=updateTime') ?>">
		<?= @text('COM-PAGES-ORDERING-LAST-UPDATE-TIME') ?>
		</a>
	</li>
</ul>
</module>

<div class="an-entities-wrapper" id="an-entities-main-wrapper">
<?= @template('list') ?>
</div>