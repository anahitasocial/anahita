<?php defined('KOOWA') or die('Restricted access');?>

<div class="an-entities">
	
	<?php foreach( $topics as $topic ) : ?>
	<?= @view('topic')->layout('list')->topic( $topic )->filter( $filter ) ?>
	<?php endforeach; ?>
	
    <?php if(count($topics) == 0): ?>
    <?= @message(@text('COM-TOPICS-TOPICS-EMPTY-LIST-MESSAGE')) ?>
    <?php endif; ?>
    
</div>

<?= @pagination( $topics, array( 'url' => @route('layout=list') ) ) ?>