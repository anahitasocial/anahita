<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php if(count($topics)) :?>
	<?php foreach( $topics as $topic) : ?>
	<?= @view('topic')->layout('list')->topic($topic)->filter($filter) ?>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('COM-TOPICS-PROFILE-NO-TOPICS-HAVE-BEEN-STARTED')) ?>
<?php endif; ?>