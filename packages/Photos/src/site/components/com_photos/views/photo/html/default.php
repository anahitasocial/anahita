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
    	<h4><?= @text('COM-PHOTOS-PHOTO-RELATED-SETS') ?></h4>
    	<div id="sets-wrapper" oid="<?= $photo->owner->id ?>" photo_id="<?= $photo->id ?>">
        <?= @view('sets')->layout('module')->set('sets', $photo->sets) ?>
    	</div>
 
		<hr/>
		
		<h4><?= @text('LIB-AN-META') ?></h4>
    
    	<ul class="an-meta">
    		<li><?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($photo->creationTime), @name($photo->author)) ?></li>
    		<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($photo->updateTime), @name($photo->editor)) ?></li>
    		<li><?= sprintf( @text('COM-PHOTOS-PHOTO-META-SETS'), $photo->sets->getTotal()) ?></li>
    		<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?></li>
    	</ul>
    	
    	<?php if($actor->authorize('administration')) : ?>
    	<hr/>
    	
    	<h4><?= @text('COM-PHOTOS-PHOTO-PRIVACY') ?></h4>
    	<?= @helper('ui.privacy',$photo) ?>
    	<?php endif; ?>		
	</div>
</div>