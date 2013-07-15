<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
    <?php
        $label[] = 'COM-STORIES-STORY-ACTOR-ADD';
        $label[] = str_replace('_','-',strtoupper($target->component)).'-STORY-ACTOR-ADD';
    ?>
    <?= sprintf(translate($label), @name($subject), @name($target) ); ?>
</data>
<data name="body">
    <?= @escape($target->body) ?>
</data>