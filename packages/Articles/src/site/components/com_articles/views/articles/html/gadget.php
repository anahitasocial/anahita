<?php defined('KOOWA') or die ?>

<?php if(count($articles)) : ?>

<?php
$url = array('layout'=>'gadget_list');

if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>

<div id="an-articles" class="an-entities" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">
<?= @template('gadget_list') ?>
</div>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>