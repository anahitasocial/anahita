<?php defined('KOOWA') or die ?>

<?php
$url = array();

if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>

<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route($url) ?>'}" class="an-entities">
<?= @template('gadget_list') ?>
</div>

<div class="an-loading-prompt hide">
	<?= @message(@text('LIB-AN-LOADING-PROMPT')) ?>
</div>