<?php defined('KOOWA') or die ?>


<div class="an-entity">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($article->author) ?> 
		</div>
		
		<div class="entity-container">
		    <?php if( $article->owner->authorize('administration') && $article->pinned ): ?>
            <span class="label label-info pull-right"><?= @text('LIB-AN-PINNED') ?></span> 
            <?php endif; ?>
			<h4 class="author-name"><?= @name($article->author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($article->creationTime) ?></li>
				<?php if(!$article->owner->eql($article->author)): ?>
				<li><?= @name($article->owner) ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<h3 class="entity-title"> 
		<a href="<?= @route($article->getURL()) ?>">
			<?= @escape($article->title) ?> 
		</a>
	</h3>
	
	<?php if($article->excerpt): ?>
	<div class="entity-excerpt">
		<?= @escape( $article->excerpt ) ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<?php if(!$article->isPublished()): ?>
		<p class="label label-warning">
			<?= @text('COM-ARTICLES-ARTICLE-IS-UNPUBLISHED') ?>
		</p>
		<?php endif; ?>
		
		<ul class="an-meta inline">
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $article->numOfComments) ?></li>
			<?php if(isset($article->editor)) : ?>
			<li><?= sprintf( @text('LIB-AN-ENTITY-EDITOR'), @date($article->updateTime), @name($article->editor)) ?></li>
			<?php endif; ?>
		</ul>
	
		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $article->id ?>">
			<?= @helper('ui.voters', $article); ?>
		</div>
	</div>
	
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>