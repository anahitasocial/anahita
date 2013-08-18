<?php defined('KOOWA') or die('Restricted access');?>

<div class="an-entities" id="an-entities-main">
<?php if(count($topics)): ?>
	<?php foreach($topics as $topic) : ?>
	<?= @view('topic')->layout('list')->topic($topic)->filter($filter) ?>
	<?php endforeach; ?>
<?php else : ?>
	<?= @message(@text('COM-TOPICS-TOPICS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($topics, array('url'=>@route('layout=list'))) ?>