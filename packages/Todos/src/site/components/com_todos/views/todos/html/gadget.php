<?php defined('KOOWA') or die ?>

<?php if(count($todos)) : ?>

<?php
$url = array('layout'=>'gadget_list');

if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>

<div id="an-todos" class="an-entities" data-trigger="InfinitScroll" data-url="<?= @route($url) ?>">
<?= @template('gadget_list') ?>
</div>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>