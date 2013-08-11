<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php if(count($pages)): ?>
	<?php foreach($pages as $page) : ?>
		<?= @view('page')->layout('list')->page($page)->filter($filter) ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('COM-PAGES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($pages, array('url'=>@route('layout=list&order='.$order))) ?>