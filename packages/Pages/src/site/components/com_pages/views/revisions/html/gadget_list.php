<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php $revisions->order('revisionNum','DESC')->limit(10) ?>

<?php if( count($revisions) ): ?>
	<?php foreach($revisions as $revision ): ?>
	<?= @view('revision')->layout('gadget')->revision($revision) ?>
	<?php endforeach; ?>
<?php else: ?>
	<?= @message( @text('COM-PAGES-PAGE-REVISIONS-EMPTY-LIST') ) ?>
<?php endif; ?>