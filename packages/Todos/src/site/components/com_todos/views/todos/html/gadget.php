<?php defined('KOOWA') or die ?>

<?php if(count($todos)) : ?>
<div id="an-todos" class="an-entities">
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
$('#an-todos').infinitscroll({
	url: '<?= @route($url) ?>'
});
</script>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>