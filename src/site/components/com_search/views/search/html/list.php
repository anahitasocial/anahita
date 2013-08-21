<?php defined('KOOWA') or die; ?>

<?php if($item->isDescribable()): ?>
<div class="an-entity">

	<div class="clearfix">
		<div class="entity-portrait-square">
			<?php if($item->inherits('ComActorsDomainEntityActor')): ?>
			<?= @avatar($item) ?>
			<?php elseif($item->isModifiable()): ?>
			<?= @avatar($item->author) ?>
			<?php endif; ?>
		</div>
		
		<div class="entity-container">
			<?php if($item->inherits('ComMediumDomainEntityMedium')): ?>
			<h4 class="author-name">
				<?= @name($item->author) ?>
			</h4>
			<?php elseif($item->inherits('ComActorsDomainEntityActor')): ?>
			<h4 class="entity-name">
				<?= @name($item) ?>
			</h4>
			<?php endif; ?>
			
			<ul class="an-meta inline">
				<?php if($item->inherits('ComMediumDomainEntityMedium')): ?>
				<li><?= @date($item->creationTime) ?></li>
					<?php if(!$item->owner->eql($item->author)): ?>
					<li><?= @name($item->owner) ?></li>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if($item->inherits('ComActorsDomainEntityActor')): ?>
				<li>
					<?= $item->followerCount ?>
					<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
					<?php if($item->isLeadable()): ?>
					/ <?= $item->leaderCount ?>
					<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
					<?php endif; ?>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	
	<?php if($item->isPortraitable() && !$item->inherits('ComActorsDomainEntityActor')): ?>
	<div class="entity-portrait-medium">
		<a title="<?= @escape($item->title) ?>" href="<?= @route($item->getURL()) ?>">			
			<img alt="<?= @escape($item->title) ?>" src="<?= $item->getPortraitURL('medium') ?>" />
		</a>
	</div>
	<?php endif; ?>
	
	<?php if(!empty($item->title) && $item->inherits('ComMediumDomainEntityMedium')): ?>
	<h3 class="entity-title">
		<a href="<?= @route($item->getURL()) ?>">
			<?= @escape($item->title) ?>
		</a>
	</h3>
	<?php endif; ?>
	
	<div class="entity-description">
		<?php $text = @helper('text.highlight', strip_tags($item->body), $keywords) ?>
		<?= @helper('text.truncate', $text, array('length'=>400, 'consider_html'=>true))?>
	</div>
	
	<?php if($item->inherits('ComMediumDomainEntityMedium')): ?>
	<div class="entity-meta">
		<ul class="an-meta inline">
			<li>
				<a href="<?= @route($item->getURL()) ?>">
					<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $item->numOfComments) ?>
				</a>
			</li>
		</ul>
		
		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $item->id ?>">
			<?= @helper('ui.voters', $item); ?>
		</div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>