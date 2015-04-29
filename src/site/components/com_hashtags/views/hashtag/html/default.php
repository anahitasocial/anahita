<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?php 
$paginationUrl = $item->getURL().'&layout=list'; 

if(!empty($sort))
	$paginationUrl .= '&sort='.$sort;
	
if(!empty($scope))
	$paginationUrl .= '&scope='.$scope;	
?>


<div id="an-hashtags" class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route($paginationUrl) ?>">
	<?= @template('list') ?>
</div>
