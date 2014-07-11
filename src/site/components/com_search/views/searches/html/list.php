<?php defined('KOOWA') or die; ?>

<?php
$url = array(); 
if(!empty($sort))
	$url['sort'] = $sort;
	
if(!empty($scope))
	$url['scope'] = $scope;	
?>

<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route($url) ?>'}" class="an-entities" id="an-entities-main">
	<?= @helper('ui.nodes', $items) ?>
</div>