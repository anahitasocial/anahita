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
	
	<div class="span4">	
			
    	<div class="an-entity an-photos-set editable" data-url="<?= @route($set->getURL()) ?>">		
    		<div id="set-cover-wrapper">
    			<?= @template('cover') ?>
    		</div>
    		
    		<div class="entity-description-wrapper">
        		<h3 class="entity-title">
        			<?= @escape( $set->title ) ?>
        		</h3>
        		
        		<div class="entity-description">
        			<?= @content( nl2br( $set->description ), array( 'exclude' => array('gist','video') ) ) ?>
        		</div>
			</div>
    		
    		<div class="entity-meta">
    			<div class="an-meta" id="vote-count-wrapper-<?= $set->id ?>">
    			<?= @helper('ui.voters', $set); ?>
    			</div>
    		</div>
    	</div>
    	
    	<h4 class="block-title">
    	    <?= @text('LIB-AN-META') ?>
    	</h4>
    	  
    	<div class="block-content">  
        	<ul class="an-meta">
        		<li><?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($set->creationTime), @name($set->author)) ?></li>
        		<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($set->updateTime), @name($set->editor)) ?></li>
        		<li><?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $set->getPhotoCount() ) ?></li>
        		<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $set->numOfComments) ?></li>
        	</ul>	
    	</div>		
	</div>
</div>


