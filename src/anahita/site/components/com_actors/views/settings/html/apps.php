<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-APPS') ?></h3>

<div class="an-entities">
	<?php foreach($enablable_apps as $component ) : ?>
	<div class="an-entity" scroll-handle="<?=$component->id?>">
		<h4 class="entity-title">
			<?= $component->getProfileName() ?>
		</h4>
		<div class="entity-description"><?= $component->getProfileDescription() ?></div>
		<?php if ( false && count($component->getFeatures()) ) : ?>
		<div class="entity-meta">
		    <form>	
			    <div class="control-label">
			    	<label class="control-label" for="<?= $component->getProfileName() ?>"><?= @text('COM-ACTORS-APP-PROFILE-FEATURES') ?></label>
			    	<div class="controls">		    			        
    			    <?php foreach($component->getProfileFeatures() as $feature) : ?>
    			        <label class="checkbox">
    			        <?php $enabled = false && !$component->authorize('install', array('actor'=>$item)) ?>
    			            <input type="checkbox" disabled <?= $enabled ? 'checked' : ''?>/>
    			            <?= translate(array(strtoupper('COM-'.$component->getName().'-FEATURE-'.$feature),strtoupper('COM-ACTORS-APP-PROFILE-FEATURE-'.$feature))) ?>
    			        </label>
    			    <?php endforeach; ?>		
    			    </div>	
    			</div>    
		    </form>
		</div>
		<?php endif;?>
		
		<div class="entity-actions">
		<?php if ( !$component->enabledForActor($item) ) : ?>
		<a class="btn btn-primary" data-trigger="Submit" href="<?=@route($item->getURL().'&action=addapp&app='.$component->component)?>">
			<?= @text('COM-ACTORS-APP-ACTION-INSTALL') ?>
		</a>						
		<?php else : ?>
		<a class="btn" data-trigger="Submit" href="<?=@route($item->getURL().'&action=removeapp&app='.$component->component)?>">
			<?= @text('COM-ACTORS-APP-ACTION-UNINSTALL') ?>
		</a>
		<?php endif;?>	
		</div>
	</div>
	<?php endforeach;?>
</div>
