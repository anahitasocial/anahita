<?php defined('KOOWA') or die; ?>

<h1 id="entity-name"><?= sprintf(@text('COM-HASHTAG-TERM'), $item->name) ?></h1>

<?php 

$paginationUrl = $item->getURL(); 
if(!empty($sort))
	$paginationUrl .= '&sort='.$sort;
	
if(!empty($scope))
	$paginationUrl .= '&scope='.$scope;	
?>

<div class="an-entities-wrapper">
	<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route($paginationUrl) ?>'}" class="an-entities masonry">
		<?= @view('nodes')->layout('list')->items($item->hashtagables) ?>
	</div>
</div>