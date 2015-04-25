<?php defined('KOOWA') or die('Restricted access');?>

<div class="an-entity an-entity-portraiable an-entity-tiled">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($set->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($set->author) ?></h4>
			<div class="an-meta">
				<?= @date( $set->creationTime ) ?>
			</div>
		</div>
	</div>
	
	<div class="entity-portrait-medium">
		<a href="<?= @route($set->getURL()) ?>">
			<img src="<?= $set->getCoverSource('medium') ?>" alt="<?= @escape($set->title) ?>" />
		</a>
	</div>
	
	<h3 class="entity-title">
		<a href="<?= @route($set->getURL()) ?>">
			<?= @escape($set->title) ?>
		</a>
	</h3>
	
	<?php if(!empty($set->description)): ?>
	<div class="entity-description">
		<?= @helper('text.truncate',  @content( nl2br( $set->description ), array( 'exclude' => array('gist','video') ) ), array('length'=>150, 'consider_html'=>true, 'omission'=>'...') ) ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $set->getPhotoCount()) ?>
	</div>
</div>