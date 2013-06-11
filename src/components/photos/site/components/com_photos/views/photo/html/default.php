<?php defined('KOOWA') or die; ?>

<?php if( $photo->authorize('edit') ) : ?>
<script src="com_photos/js/photoset.js" />
<?php endif; ?>


<module position="sidebar-b" title="<?= @text('COM-PHOTOS-PHOTO-RELATED-SETS') ?>">
<div id="sets-wrapper" oid="<?= $photo->owner->id ?>" photo_id="<?= $photo->id ?>">
<?= @view('sets')->layout('module')->set('sets', $photo->sets) ?>
</div>		
</module>		

<module position="sidebar-b" title="<?= @text('LIB-AN-META') ?>">	
	<ul class="an-meta">
		<li><?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($photo->creationTime), @name($photo->author)) ?></li>
		<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($photo->updateTime), @name($photo->editor)) ?></li>
		<li><?= sprintf( @text('COM-PHOTOS-PHOTO-META-SETS'), $photo->sets->getTotal()) ?></li>
		<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?></li>
	</ul>
</module>	
	
<?php if ( $actor->authorize('administration') ) : ?>
<module position="sidebar-b" title="<?= @text('COM-PHOTOS-PHOTO-PRIVACY') ?>">		
	<?= @helper('ui.privacy',$photo) ?>
</module>	
<?php endif; ?>

<?= @template('photo') ?>

<?= @helper('ui.comments', $photo) ?>
