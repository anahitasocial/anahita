<? defined('KOOWA') or die('Restricted access');?>	

<? $revisions->order('revisionNum', 'DESC')->limit(10) ?>

<? if (count($revisions)): ?>
	<? foreach ($revisions as $revision): ?>
	<?= @view('revision')->layout('gadget')->revision($revision) ?>
	<? endforeach; ?>
<? else: ?>
	<?= @message(@text('COM-ARTICLES-ARTICLE-REVISIONS-EMPTY-LIST')) ?>
<? endif; ?>
