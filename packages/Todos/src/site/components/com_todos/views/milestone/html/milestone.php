<?php defined('KOOWA') or die('Restricted access') ?>

<?php @commands('toolbar')?>

<?php 
$current_time = new KDate();
$highlight = ($milestone->endDate->getDate(DATE_FORMAT_UNIXTIME) >= $current_time->getDate(DATE_FORMAT_UNIXTIME)) ? 'an-highlight' : ''; 
?>
<div class="an-entity <?= $highlight ?>">
	<h3 class="entity-title">
		<?= @escape($milestone->title) ?>
	</h3>
	
	<?php if($milestone->description): ?>
	<div class="entity-description"><?= @content($milestone->body) ?></div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $milestone->id ?>">
			<?= @helper('ui.voters', $milestone); ?>
		</div>
	</div>
</div>