<?php defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<?php if(count($articles)): ?>
	<?php foreach($articles as $article) : ?>
		<?= @view('article')->layout('list')->article($article)->filter($filter) ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('COM-ARTICLES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($articles, array('url'=>@route('layout=list&order='.$order))) ?>