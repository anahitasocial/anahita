<? defined('KOOWA') or die('Restricted access');?>

<div class="an-entities">

	<? foreach ($topics as $topic) : ?>
	<?= @view('topic')->layout('list')->topic($topic)->filter($filter) ?>
	<? endforeach; ?>

    <? if (count($topics) == 0): ?>
    <?= @message(@text('COM-TOPICS-TOPICS-EMPTY-LIST-MESSAGE')) ?>
    <? endif; ?>

</div>

<?= @pagination($topics, array('url' => @route('layout=list'))) ?>
