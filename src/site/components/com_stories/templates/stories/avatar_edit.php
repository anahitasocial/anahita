<?php defined('KOOWA') or die('Restricted access');?>

<?php 
$targets = $target;

if ( !is_array($targets) )
{
    $targets = array( $targets ); 
} 
?>

<data name="title">
<?php if(count($targets) == 1): ?>
<?=sprintf(@text('COM-STORIES-TITLE-UPDATE-AVATAR'), @name($subject)) ?>
<?php else: ?>
<?= sprintf(@text('COM-STORIES-TITLE-UPDATE-AVATARS'), @name($subject)) ?>
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
