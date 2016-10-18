<? defined('KOOWA') or die('Restricted access');?>	

<? if (count($articles)) :?>
	<? foreach ($articles as $article) : ?>
	<?= @view('article')->layout('list')->article($article)->filter($filter) ?>
	<? endforeach; ?>
<? else: ?>
<?= @message(@text('COM-ARTICLES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
