<?php defined('KOOWA') or die; ?>

<?php if($photo->authorize('edit')) : ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_photos/js/photoset.js" />
<?php else: ?>
<script src="com_photos/js/min/photoset.min.js" />
<?php endif; ?>

<?php endif; ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header', array()) ?>
	<?= @template('photo') ?>
	<?= @helper('ui.comments', $photo) ?>
	</div>
	
	<div class="span4 visible-desktop">
    	<h4 class="block-title">
    	<?= @text('COM-PHOTOS-PHOTO-RELATED-SETS') ?>
    	</h4>
    	
    	<div class="block-content">
    		<div id="sets-wrapper" data-url="<?= @route('option=com_photos&view=sets&layout=sidebar&oid='.$actor->id) ?>" data-photo="<?= $photo->id ?>">
            <?= @view('sets')->layout('sidebar')->set('sets', $photo->sets) ?>
    		</div>
		</div>
		
		<h4 class="block-title">
		<?= @text('LIB-AN-META') ?>
		</h4>
        
        <div class="block-content">
        	<ul class="an-meta">
        		<li><?= sprintf( @text('LIB-AN-ENTITY-AUTHOR'), @date($photo->creationTime), @name($photo->author)) ?></li>
        		<li><?= sprintf( @text('LIB-AN-ENTITY-EDITOR'), @date($photo->updateTime), @name($photo->editor)) ?></li>
        		<li><?= sprintf( @text('COM-PHOTOS-PHOTO-META-SETS'), $photo->sets->getTotal()) ?></li>
        		<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?></li>
        	</ul>
    	</div>
    	
    	<?php if($actor->authorize('administration')) : ?>	
    	<h4 class="block-title">
    	<?= @text('COM-PHOTOS-PHOTO-PRIVACY') ?>
    	</h4>
    	
    	<div class="block-content">
    	<?= @helper('ui.privacy',$photo) ?>
    	</div>
    	<?php endif; ?>		
	</div>
</div>