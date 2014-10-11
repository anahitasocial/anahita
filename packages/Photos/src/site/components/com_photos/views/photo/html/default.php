<?php defined('KOOWA') or die; ?>

<?php if($photo->authorize('edit')) : ?>
<script src="com_photos/js/photoset.js" />
<?php endif; ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header', array()) ?>
	<?= @template('photo') ?>
	<?= @helper('ui.comments', $photo) ?>
	</div>
	
	<div class="span4">
    	<h4 class="block-title">
    	<?= @text('COM-PHOTOS-PHOTO-RELATED-SETS') ?>
    	</h4>
    	
    	<div class="block-content">
    		<div id="sets-wrapper" oid="<?= $photo->owner->id ?>" photo_id="<?= $photo->id ?>">
            <?= @view('sets')->layout('sidebar')->set('sets', $photo->sets) ?>
    		</div>
		</div>
		
		<h4 class="block-title">
		<?= @text('LIB-AN-META') ?>
		</h4>
        
        <div class="block-content">
        	<ul class="an-meta">
        		<li><?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($photo->creationTime), @name($photo->author)) ?></li>
        		<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($photo->updateTime), @name($photo->editor)) ?></li>
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