<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php if(count($pages)): ?>
	<?php foreach($pages as $page) : ?>
		<?php if($page->getRowData('search_result_preview')): ?>
		<?= @view('page')->layout('list_search_result')->page($page)->filter($filter)->keyword($q) ?>
		<?php else : ?>
		<?= @view('page')->layout('list')->page($page)->filter($filter) ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('COM-PAGES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($pages, array('url'=>@route('layout=list&order='.$order))) ?>