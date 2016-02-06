<?php defined('KOOWA') or die; ?>

<?php $view = @view('location')->layout('list'); ?>
<?php $i = 0; ?>

<div class="span6">
<?php foreach ($locations as $location) : ?>
		<?php if(($i % 2) == 0) : ?>
		<?= $view->location($location); ?>
		<?php endif; ?>
		<?php $i++; ?>
<?php endforeach; ?>
</div>

<div class="span6">
<?php foreach ($locations as $location) : ?>
		<?php if(($i % 2) == 1) : ?>
		<?= $view->location($location); ?>
		<?php endif; ?>
		<?php $i++; ?>
<?php endforeach; ?>
</div>
