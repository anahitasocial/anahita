<?php defined('KOOWA') or die ?>

<?php 
$url = array('layout'=>'list');
        
if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>
<div id="an-stories" class="an-entities" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">
	<?= @template('list') ?>
</div>
