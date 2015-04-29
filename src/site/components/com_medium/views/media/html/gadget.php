<?php defined('KOOWA') or die('Restricted access');?>

<?php
$url = array();

if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>

<div data-behavior="InfiniteScroll" data-InfiniteScroll-options="{'url':'<?= @route($url) ?>'}" class="an-entities">
<?= @template('gadget_list') ?>
</div>

<div class="an-loading-prompt hide">
	<?= @message(@text('LIB-AN-LOADING-PROMPT')) ?>
</div>