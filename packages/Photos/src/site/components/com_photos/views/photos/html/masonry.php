<?php defined('KOOWA') or die('Restricted access');?>

<?= @helper('ui.header', array()) ?>

<?php if(count($photos)) : ?>
<div id="an-photos" class="an-entities masonry">
<?= @template('masonry_list') ?>
</div>

<script>
$('#an-photos').infinitscroll({
	url: '<?= @route('layout=masonry_list') ?>'
});
</script>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
