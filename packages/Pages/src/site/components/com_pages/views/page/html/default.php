<?php defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">
		<?= @helper('ui.header', array()) ?>
		
		<?= @template('page') ?>
		<?= @helper('ui.comments', $page, array('pagination'=>true)) ?>	ee
	</div>
	
	<div class="span4">
	
		<h4><?= @text('COM-PAGES-META-ADDITIONAL-INFORMATION') ?></h4>
		<ul class="an-meta" >
			<li><span class="label label-info"><?= @text('COM-PAGES-PAGE-REVISION-META-CURRENT') ?></span></li>
			<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($page->updateTime), @name($page->editor)) ?></li>
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $page->numOfComments) ?></li>
		</ul>
		
		<hr/>
		
		<h4><?= @text('COM-PAGES-PAGE-PRIVACY') ?></h4>
		<?= @helper('ui.privacy',$page) ?>
	</div>
</div>