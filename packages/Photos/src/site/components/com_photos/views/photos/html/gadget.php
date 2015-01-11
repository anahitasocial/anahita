<?php defined('KOOWA') or die('Restricted access');?>

<?php if(count($photos)) : ?>
<div id="an-photos" class="an-entities">
<?= @template('masonry_list') ?>
</div>

<?php
$url = array('layout'=>'masonry_list');

if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>

<script>
$('#an-photos').infinitscroll({
	url: '<?= @route($url) ?>'
});
</script>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>