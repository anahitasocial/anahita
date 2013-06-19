<?php defined('KOOWA') or die('Restricted access'); ?>

<?php 
$current_time = new KDate();
$highlight = ($milestone->endDate->getDate(DATE_FORMAT_UNIXTIME) >= $current_time->getDate(DATE_FORMAT_UNIXTIME)) ? 'an-highlight' : ''; 
?>

<div class="an-entity <?= $highlight ?>">
	<div class="entity-portrait-square">
		<?= @avatar($milestone->author) ?>
	</div>

	<div class="entity-container">
		<h4 class="entity-title">
			<a href="<?= @route($milestone->getURL()) ?>"><?= @escape($milestone->title) ?></a>
		</h4>
		
		<?php if($milestone->description): ?>
		<div class="entity-description">
			<?= @helper('text.truncate', strip_tags($milestone->description), array('length'=>100)); ?>
		</div>
		<?php endif; ?>
		
		<div class="entity-meta">
			<div class="an-meta">
			<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($milestone->creationTime), @name($milestone->author)) ?>
			</div>
			
			<div class="an-meta">
			<?= sprintf(@text('COM-TODOS-MILESTONE-META-END-DATE'), @date($milestone->endDate)) ?>
			</div>
		</div>
	</div>
</div>