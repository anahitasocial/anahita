<?php defined('KOOWA') or die('Restricted access') ?>

<?php @commands('toolbar')?>

<?php 
$current_time = new KDate();
$highlight = ($milestone->endDate->getDate(DATE_FORMAT_UNIXTIME) >= $current_time->getDate(DATE_FORMAT_UNIXTIME)) ? 'an-highlight' : ''; 
?>
<div class="an-entity <?= $highlight ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($milestone->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($milestone->author) ?></h4>
			<div class="an-meta">
				<?= @date($milestone->creationTime) ?> - 
				<?= sprintf(@text('COM-TODOS-MILESTONE-META-END-DATE'), @date($milestone->endDate)) ?>
			</div>
		</div>
	</div>
	
	<h3 class="entity-title">
		<?= @escape($milestone->title) ?>
	</h3>
	
	<?php if($milestone->description): ?>
	<div class="entity-description"><?= @content($milestone->body) ?></div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<ul class="an-meta inline">
			<li><?= sprintf( @text('COM-TODOS-MILESTONE-NUMBER-OF-TODOS'), $milestone->numOfTodolists) ?></li>
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $milestone->numOfComments) ?></li>
			<?php if(isset($milestone->editor)) : ?>
			<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($milestone->updateTime), @name($milestone->editor)) ?></li>
			<?php endif; ?>
		</ul>
		
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $milestone->id ?>">
			<?= @helper('ui.voters', $milestone); ?>
		</div>
	</div>
</div>