<? defined('KOOWA') or die ?>

<div class="an-entities" id="an-entities-main">
<? if (count($articles)): ?>
	<? foreach ($articles as $article) : ?>
		<?= @view('article')->layout('list')->article($article)->filter($filter) ?>
	<? endforeach; ?>
<? else : ?>
<?= @message(@text('COM-ARTICLES-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
</div>

<?= @pagination($articles, array('url' => @route('layout=list&order='.$order))) ?>
