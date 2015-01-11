<?php defined('KOOWA') or die('Restricted access');?>

<?php if(count($topics)) : ?>
<div id="an-topics" class="an-entities">
<?= @template('gadget_list') ?>
</div>

<?php
$url = array('layout'=>'gadget_list');

if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>

<script>
$('#an-topics').infinitscroll({
	url: '<?= @route($url) ?>'
});
</script>

<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>