<?php defined('KOOWA') or die ?>

<script src="com_stories/js/stories.js" />
<script src="lib_anahita/js/vendors/mediabox.js" />

<?php 
$url = array('layout'=>'list');
        
if(isset($filter))
	$url['filter'] = $filter;
elseif (isset($actor))
	$url['oid'] = $actor->id;
?>
<div id="an-stories" data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route($url) ?>', 'record':'.an-entity.an-story'} " class="an-entities an-stories">
	<?= @template('list') ?>
</div>
