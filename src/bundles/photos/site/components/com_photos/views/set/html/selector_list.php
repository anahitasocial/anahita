<?php defined('KOOWA') or die ?>

<?php 
if(!isset($assigned_sets))
{
	$assigned_sets = array();
	if(count($photo->top()->sets))
		foreach($photo->top()->sets as $set)
			$assigned_sets[] = $set->id;
}
?>

<div class="an-entity an-entity-select-option <?= (in_array($set->id, $assigned_sets)) ? 'an-highlight' : '' ?>" set_id="<?= $set->id ?>" data-trigger="<?= (in_array($set->id, $assigned_sets)) ? 'RemovePhoto' : 'AddPhoto' ?>">
	<?php if($set->hasCover()): ?>
	<div class="entity-portrait-square">
		<img src="<?= $set->getCoverSource('square') ?>" />
	</div>
	<?php endif; ?>
	
	<div class="entity-container">
		<h4 class="entity-title">
			<?= @helper('text.truncate',  @escape($set->title), array('length'=>25, 'omission'=>'...') ) ?>
		</h4>
		
		<div class="entity-meta">
			<?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $set->getPhotoCount()) ?>
		</div>
	</div>
</div>