<?php defined('KOOWA') or die ?>

<?php if($set->authorize('edit')) : ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_photos/js/organizer.js" />
<?php else: ?>
<script src="com_photos/js/min/organizer.min.js" />
<?php endif; ?>

<?php endif; ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header', array()) ?>
    <?= @template('set') ?>
    <?= @helper('ui.comments', $set) ?>
	</div>
	
	<div class="span4 visible-desktop">	
			
    	<div id="set-cover-wrapper">
                <?= @template('cover') ?>
            </div>
    	
    	<h4 class="block-title">
    	    <?= @text('LIB-AN-META') ?>
    	</h4>
    	  
    	<div class="block-content">  
        	<ul class="an-meta">
        		<li><?= sprintf( @text('LIB-AN-ENTITY-AUTHOR'), @date($set->creationTime), @name($set->author)) ?></li>
        		<li><?= sprintf( @text('LIB-AN-ENTITY-EDITOR'), @date($set->updateTime), @name($set->editor)) ?></li>
        		<li><?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $set->getPhotoCount() ) ?></li>
        		<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $set->numOfComments) ?></li>
        	</ul>	
    	</div>		
	</div>
</div>


