<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?php 
$paginationUrl = $item->getURL(); 
if(!empty($sort))
	$paginationUrl .= '&sort='.$sort;
	
if(!empty($scope))
	$paginationUrl .= '&scope='.$scope;	
?>

<div class="an-entities-wrapper">
	<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route($paginationUrl) ?>'}" class="an-entities masonry">
		<?= @helper('ui.nodes', $item->tagables) ?>
	</div>
</div>