<?php defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header', array()) ?>
	<?= @template('article') ?>
	<?= @helper('ui.comments', $article) ?>
	</div>
	
	<div class="span4 visible-desktop">
		<h4 class="block-title">
		<?= @text('COM-ARTICLES-META-ADDITIONAL-INFORMATION') ?>
		</h4>
		
		<div class="block-content">
    		<ul class="an-meta">
    			<li><?= sprintf( @text('LIB-AN-ENTITY-EDITOR'), @date($article->updateTime), @name($article->editor)) ?></li>
    			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $article->numOfComments) ?></li>
    		</ul>
		</div>
		
		<?php if($actor->authorize('administration')): ?>
		<h4 class="block-title">
		<?= @text('COM-ARTICLES-ARTICLE-PRIVACY') ?>
		</h4>
		
		<div class="block-content">
		<?= @helper('ui.privacy', $article) ?>
		</div>
		<?php endif; ?>
	</div>
</div>