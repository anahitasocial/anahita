<?php defined('KOOWA') or die ?>

<?php if(count($pages)) : ?>
<div id="an-pages" class="an-entities">
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
$('#an-pages').infinitscroll({
	url: '<?= @route($url) ?>'
});
</script>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>