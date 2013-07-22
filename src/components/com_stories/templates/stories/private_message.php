<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?php if ( $type != 'notification' && ($target->eql($actor))) : ?>
    <?=sprintf(@text('COM-STORIES-TITLE-POST-PRIVATE-MESSAGE-WITHOUT-TARGET'), @name($subject))?>
<?php else :?>  
    <?=sprintf(@text('COM-STORIES-TITLE-POST-PRIVATE-MESSAGE'), @name($subject), @possessive($target))?>
<?php endif ?>
</data>

<?php if ( $type == 'story') : ?>
<data name="body">
<div class="an-highlight"><?= @content($story->body) ?></div>
</data>
<?php else : ?>
<data name="email_body">
<div><?= @content($object->body) ?></div>
<?php $commands->insert('viewstory', array('label'=>@text('COM-STORIES-VIEW-STORY')))->href($object->getURL())?>
</data>
<?php endif;?>
