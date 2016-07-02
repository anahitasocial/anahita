<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php if (count($articles)) :?>
	<?php foreach ($articles as $article) : ?>
	<?= @view('article')->layout('list')->article($article)->filter($filter) ?>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('COM-ARTICLES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>