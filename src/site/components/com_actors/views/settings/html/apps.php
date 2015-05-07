<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-APPS') ?></h3>

<div class="an-entities">
	<?php foreach($enablable_apps as $component ) : ?>
	<div class="an-entity">
		<h4 class="entity-title">
			<?= $component->getProfileName() ?>
		</h4>
		
		<div class="entity-description">
		    <?= $component->getProfileDescription() ?>
		</div>
		
		<div class="entity-actions">
    		<?php if ( !$component->enabledForActor($item) ) : ?>
    		<a  class="btn btn-primary" data-action="addapp" data-app="<?= $component->component ?>" href="<?= @route($item->getURL()) ?>">
    			<?= @text('COM-ACTORS-APP-ACTION-INSTALL') ?>
    		</a>						
    		<?php else : ?>
    		<a class="btn" data-action="removeapp" data-app="<?= $component->component ?>" href="<?= @route($item->getURL()) ?>">
    			<?= @text('COM-ACTORS-APP-ACTION-UNINSTALL') ?>
    		</a>
    		<?php endif;?>	
		</div>
	</div>
	<?php endforeach;?>
</div>
