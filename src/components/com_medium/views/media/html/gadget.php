<?php defined('KOOWA') or die('Restricted access');?>

<?php

$url = array('layout=gadget_list');

if (isset($filter)) {
    $url['filter'] = $filter;
} elseif (isset($actor)) {
    $url['oid'] = $actor->id;
}
?>

<?= @infinitescroll(null, array(
  'url' => $url,
  'id' => 'an-media'
)) ?>
