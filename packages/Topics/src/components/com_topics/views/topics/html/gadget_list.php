<? defined('KOOWA') or die('Restricted access');?>	

<? if (count($topics)) :?>
	<? foreach ($topics as $topic) : ?>
	<?= @view('topic')->layout('list')->topic($topic)->filter($filter) ?>
	<? endforeach; ?>
<? else: ?>
<?= @message(@text('COM-TOPICS-PROFILE-NO-TOPICS-HAVE-BEEN-STARTED')) ?>
<? endif; ?>
