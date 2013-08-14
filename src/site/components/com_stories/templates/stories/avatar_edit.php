<?php defined('KOOWA') or die('Restricted access');?>

<?php 
$targets = $target;
if ( !is_array($targets) ) 
    $targets = array($targets);
?>

<data name="title">
<?php if(count($targets) == 1): ?>
<?=sprintf(@text('COM-STORIES-TITLE-UPDATE-AVATAR'), @name($subject), @possessive($target)) ?>
<?php else: ?>
<?= @text('COM-STORIES-TITLE-UPDATE-AVATARS') ?>
<?php endif; ?>
</data>

<data name="body">
	<?php if(count($targets) == 1): ?>
	<?= @avatar($target, 'medium') ?>
	<?php else: ?>
	<div class="media-grid">
		<?php foreach($targets as $target) : ?>  
		<div><?= @avatar($target, 'square') ?></div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</data>
