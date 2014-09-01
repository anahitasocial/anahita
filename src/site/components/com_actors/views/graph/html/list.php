<?php defined('KOOWA') or die; ?>

<?php 
$url = $item->getURL().'&layout=list&get=graph&type='.$type;
if ( !empty($q) ) {
    $url .= '&q='.$q;   
}
?>

<?php 
//set the actor as state
if($item->isAdministrable())
    @listItemView()->getState()->actor = $item;        
?>

<?= @previous(array('pagination_url'=>@route($url))) ?>

