<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?php if ( $type != 'notification' ) : ?>
<?=sprintf(@text('COM-ACTORS-STORY-LEADABLE-ADD'), @name($subject), @name($object), @name($target)) ?>
<?php else: ?>
<?=sprintf(@text('COM-ACTORS-NOTIFICATION-LEADABLE-ADD'), @name($subject), @name($target)) ?>
<?php endif; ?>
</data>

