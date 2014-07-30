<?php defined('KOOWA') or die; ?>

<div class="an-entity">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($item->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name">
				<?= @name($item->author) ?>
			</h4>
			
			<ul class="an-meta inline">
				<li><?= @date($item->creationTime) ?></li>
				<?php if($item->isOwnable() && !$item->owner->eql($item->author)): ?>
				<li><?= @name($item->owner) ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	
	<?php if($item->isPortraitable()): ?>
	<div class="entity-portrait-medium">
		<a title="<?= @escape($item->title) ?>" href="<?= @route($item->getURL()) ?>">			
			<img alt="<?= @escape($item->title) ?>" src="<?= $item->getPortraitURL('medium') ?>" />
		</a>
	</div>
	<?php endif; ?>
	
	<?php if(!empty($item->title)): ?>
	<h3 class="entity-title">
		<a href="<?= @route($item->getURL()) ?>">
			<?= @escape($item->title) ?>
		</a>
	</h3>
	<?php endif; ?>
	
	<div class="entity-description">
		<?= @helper('text.truncate',  @content($item->body, array('exclude'=>'syntax')), array('length'=>400, 'consider_html'=>true)) ?>
	</div>
	
	<div class="entity-meta">
		<ul class="an-meta inline">
			<?php if($item->isCommentable()): ?>
			<li>
				<a href="<?= @route($item->getURL()) ?>">
					<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $item->numOfComments) ?>
				</a>
			</li>
			<?php endif; ?>
			
			<?php if($item->isVotable()): ?>
			<li>
				<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $item->id ?>">
				<?= @helper('ui.voters', $item); ?>
				</div>
			</li>	
			<?php endif; ?>
		</ul>
	</div>
</div>