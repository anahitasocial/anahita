<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?=sprintf(@text('COM-STORIES-TITLE-UPDATE-AVATAR'),@name($subject),@possessive($target))?>
</data>

<data name="body">
<?php 
$targets = $target;
if ( !is_array($targets) ) 
    $targets = array($targets);
?>
<?php foreach($targets as $target) : ?>  
<?= @avatar($target, 'square') ?>
<?php endforeach; ?>
</data>
