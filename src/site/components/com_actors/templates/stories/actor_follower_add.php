<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?php if ($type != 'notification') : ?>
<?=sprintf(@text('COM-ACTORS-STORY-FOLLOWER-ADD'), @name($subject), @name($object), @name($target)) ?>
<?php else: ?>
<?=sprintf(@text('COM-ACTORS-NOTIFICATION-FOLLOWER-ADD'), @name($subject), @name($object), @name($target)) ?>
<?php endif; ?>
</data>

