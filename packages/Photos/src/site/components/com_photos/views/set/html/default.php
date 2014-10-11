<?php defined('KOOWA') or die ?>

<script src="lib_anahita/js/vendors/mediabox.js" />
<?php if($set->authorize('edit')) : ?>
<script src="com_photos/js/organizer.js" />
<?php endif; ?>

<script>
Delegator.register('click', {
	'Slideshow' : function(event, el, api){
		event.stop();
		
		var media = new Array();
		
		document.getElements('#set-mediums .media-grid img').each(function(item){
			media.push([item.get('src').replace('_square', '_medium'), item.get('caption')]);
		});
		
		Mediabox.open(media,0);
	}
});
</script>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header', array()) ?>
    <?= @template('set') ?>
    <?= @helper('ui.comments', $set) ?>
	</div>
	
	<div class="span4">
	
		<a href="#" class="btn btn-block btn-large btn-primary" data-trigger="Slideshow" title="<?= @escape($set->title) ?>">
	    <?= @text('COM-PHOTOS-ACTION-SLIDESHOW') ?>
		</a>
				
    	<div class="an-entity an-photos-set">		
    		<?php if($set->hasCover()): ?>
    		<div id="set-cover-wrapper">
    			<?= @template('cover') ?>
    		</div>
    		<?php endif; ?>
    		
    		<div class="entity-title-wrapper">
    			<h3 data-behavior="<?= $set->authorize('edit') ? 'Editable' : ''; ?>" class="entity-title <?= ($set->authorize('edit')) ? 'editable' : ''; ?>" data-editable-options="{'url':'<?= @route($set->getURL()) ?>','name':'title', 'dataValidators':'required', 'prompt':'<?= @text('COM-PHOTOS-MEDIUM-TITLE-PROMPT') ?>'}">
    			<?= @escape($set->title) ?>
    			</h3>
    		</div>
    		
    		<div class="entity-description-wrapper">
    			<div data-behavior="<?= $set->authorize('edit') ? 'Editable' : ''; ?>" class="entity-description <?= ($set->authorize('edit')) ? 'editable' : ''; ?>" data-editable-options="{'url':'<?= @route($set->getURL()) ?>','name':'description', 'input-type':'textarea', 'prompt':'<?= @text('COM-PHOTOS-MEDIUM-DESCRIPTION-PROMPT') ?>'}">
    				<?= @content($set->description) ?>
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


