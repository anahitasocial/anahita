<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php if(count($pages)) :?>
	<?php foreach( $pages as $page) : ?>
	<?= @view('page')->layout('list')->page($page)->filter($filter) ?>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('COM-PAGES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>