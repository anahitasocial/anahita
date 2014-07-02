<?php defined('KOOWA') or die; ?>

<script src="lib_anahita/js/vendors/mediabox.js" />

<module position="sidebar-b" style="none"></module>

<h1 id="entity-name"><?= sprintf(@text('COM-HASHTAG-TERM'), $item->name) ?></h1>

<?php if(!empty($item->body)): ?>
<div id="entity-description">
	<?= @helper('text.truncate', @escape($item->body), array('length'=>250, 'read_more'=>true)); ?>
</div>
<?php endif; ?>

<?php 

$paginationUrl = $item->getURL(); 
if(!empty($sort))
	$paginationUrl .= '&sort='.$sort;
?>

<div class="an-entities-wrapper">
	<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route($paginationUrl) ?>'}" class="an-entities">
		<?= @view('nodes')->layout('list')->items($item->hashtagables) ?>
	</div>
</div>