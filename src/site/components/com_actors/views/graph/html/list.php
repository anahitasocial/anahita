<?php defined('KOOWA') or die; ?>

<?php 
//set the actor as state
if($item->isAdministrable())
{
    @listItemView()->getState()->actor = $item;        
}
?>

<?= @previous() ?>

