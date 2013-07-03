<?php defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?php 	
	$view = @view('story')->layout('list');
	if ( isset($actor) ) {
		$view->actor($actor);
	}
?>

<?php if(count($stories)) :?>
<?php foreach($stories as $story) : ?>
	<?= $view->item($story) ?>
<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('LIB-AN-PROMPT-NO-MORE-RECORDS-AVAILABLE')) ?>
<?php endif; ?>
