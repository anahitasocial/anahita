<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?php 
$paginationUrl = $item->getURL().'&layout=list'; 

if(!empty($sort))
	$paginationUrl .= '&sort='.$sort;
	
if(!empty($scope))
	$paginationUrl .= '&scope='.$scope;	
?>


<div id="an-hashtags" class="an-entities masonry">
	<?= @template('list') ?>
</div>

<script>
$('#an-hashtags').infinitscroll({
	url: '<?= @route($paginationUrl) ?>'
});
</script>
