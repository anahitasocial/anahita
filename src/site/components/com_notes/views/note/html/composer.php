<?php defined('KOOWA') or die ?>

<form class="composer-form" action="<?= @route() ?>" method="post">
	<input type="hidden" name="composed" value="1" />
	
	<div class="control-group">			
		<div class="controls">
			<textarea class="input-block-level" id="note-body" name="body" cols="5" rows="3" required maxlength="5000"></textarea>
    	</div>
    </div>
    
	<?php if (is_person($actor) && !is_viewer($actor)) : ?>			
	<div class="control-group">			
		<div class="controls">
			<label class="checkbox" for="private-flag">
				<input id="private-flag" type="checkbox" name="private"> 
				<?=@text('COM-NOTES-COMPOSER-PRIVATE-FLAG')?>
			</label>
		</div>
    </div>
	<?php endif; ?>
	
	<div class="clearfix">
		<span class="connect">
    	<?php $app = @service('repos://site/components.component')->find(array('component'=>'com_connect')); ?>
		<?php if ($app && $app->authorize('echo', array('actor'=>$actor))) : ?>
            <?php               
            $services = ComConnectHelperApi::getServices();
            @service('repos://site/connect.session'); 
            $sessions  = $actor->sessions->toArray();
            foreach($sessions as $key => $session) 
            {
                if($session->getApi()->isReadOnly()) 
                {
                    unset($sessions[$key]);
                }
            }                
            ?>    	        
			<?php if ( count($sessions) > 0 ) : ?>
				<?php foreach($sessions as $session) : ?>
				<span>
    				<a class="btn btn-<?= $session->api->getName() ?> connect-link" data-behavior="Checkbox" data-checkbox-name="channels[]" data-checkbox-value="<?= $session->getName() ?>" title="<?= sprintf(@text('COM-CONNECT-SHARE-POST'), ucfirst($session->api->getName()))?>">
    					<?= @helper('com://site/connect.template.helper.service.icon', $session->api->getName())?>    			            			         
    			    </a>
			    </span> 
				<?php endforeach;?>
			<?php elseif (count($services) > 0 ) : ?>
			<a href="<?= @route($actor->getURL().'&get=settings&edit=connect') ?>" class="btn">
			    <?= @text('COM-CONNECT-ENABLE-SHARE')?>
			</a>
			<?php endif; ?>
		<?php endif; ?>
		</span>
		
		<button type="submit" class="btn btn-primary pull-right" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>" >
			<?= @text('LIB-AN-ACTION-SHARE') ?>
		</button>
	</div>
</form>