<?php defined('KOOWA') or die('Restricted access'); ?>

<h4><?= @text('COM-PHOTOS-SELECTOR-TITLE') ?></h4>

<?= @message(@text('COM-PHOTOS-SELECTOR-INSTRUCTIONS')) ?>

<?php if(!empty($exclude_set)): ?>
<div class="form-actions">
	<a data-trigger="Close" href="#" class="btn"><?= @text('LIB-AN-ACTION-CANCEL') ?></a> 
	<a data-trigger="Update" href="<?= @route() ?>" class="btn btn-primary"><?= @text('LIB-AN-ACTION-SAVE') ?></a>
</div>
<?php endif; ?>

<?php
$url = array('view'=>'photos', 'layout'=>'selector', 'oid'=>$actor->id);

if(!empty($exclude_set))
	$url['exclude_set'] = $exclude_set;
?>
<div id="photo-selector" data-behavior="InfinitScroll" data-infinitscroll-options="{'record':'.thumbnail-wrapper','scrollable':'photo-selector', 'url':'<?= @route($url) ?>', 'fixedheight':true}" class="an-entities media-grid">
<?= @template('selector_list') ?>	
</div>