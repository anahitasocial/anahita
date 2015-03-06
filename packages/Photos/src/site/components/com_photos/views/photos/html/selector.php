<?php defined('KOOWA') or die('Restricted access'); ?>

<h4><?= @text('COM-PHOTOS-SELECTOR-TITLE') ?></h4>

<?= @message(@text('COM-PHOTOS-SELECTOR-INSTRUCTIONS')) ?>

<?php if(!empty($exclude_set)): ?>
<div class="form-actions">
	<a data-trigger="ClosePhotoSelector" href="#" class="btn"><?= @text('LIB-AN-ACTION-CLOSE') ?></a>
</div>
<?php endif; ?>

<?php
$url = array('view'=>'photos', 'layout'=>'selector_list', 'oid'=>$actor->id);

if(!empty($exclude_set))
	$url['exclude_set'] = $exclude_set;
?>
<div id="photo-selector-list" class="an-entities media-grid">
<?= @template('selector_list') ?>	
</div>

<script>
$('#photo-selector-list').infinitescroll({
	url : '<?= @route($url) ?>',
	record : '.thumbnail-wrapper',
	scrollable : '#photo-selector-list',
	window: '#photo-selector'
});
</script>