<?php defined('KOOWA') or die('Restricted access');?>

<div class="an-entities" id="an-entities-main">
<?php if(count($milestones)): ?>
	<?php foreach($milestones as $milestone) : ?>
		<?= @view('milestone')->layout('list')->milestone($milestone) ?>
	<?php endforeach; ?>
<?php else : ?>
<?= @message(@text('COM-TODOS-MILESTONES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>

<?= @pagination($milestones, array('url'=>@route('layout=list'))) ?>