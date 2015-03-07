<?php defined('KOOWA') or die ?>

<?php 
if(!isset($assigned_sets))
{
	$assigned_sets = array();
	if(count($photo->top()->sets))
		foreach($photo->top()->sets as $set)
			$assigned_sets[] = $set->id;
}

$highlight = (in_array($set->id, $assigned_sets)) ? true : false;
$action = (in_array($set->id, $assigned_sets)) ? 'removephoto' : 'addphoto';
?>

<div class="an-entity an-entity-select-option <?= ($highlight) ? 'an-highlight' : '' ?>" data-action="<?= $action ?>" data-url="<?= @route($set->getURL()) ?>" >
	<div class="entity-portrait-square">
		<img src="<?= $set->getCoverSource('square') ?>" />
	</div>
	
	<div class="entity-container">
		<h4 class="entity-title">
			<?= @helper('text.truncate',  @escape($set->title), array('length'=>25, 'omission'=>'...') ) ?>
		</h4>
		
		<div class="entity-meta">
			<?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $set->getPhotoCount()) ?>
		</div>
	</div>
</div>