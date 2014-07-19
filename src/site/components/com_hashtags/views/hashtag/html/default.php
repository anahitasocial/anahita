<?php defined('KOOWA') or die; ?>

<?php 
$paginationUrl = $item->getURL(); 
if(!empty($sort))
	$paginationUrl .= '&sort='.$sort;
	
if(!empty($scope))
	$paginationUrl .= '&scope='.$scope;	
?>

<div class="an-entities-wrapper">
	<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route($paginationUrl) ?>'}" class="an-entities masonry">
		<?= @helper('ui.nodes', $item->hashtagables) ?>
	</div>
</div>