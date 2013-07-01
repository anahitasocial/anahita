<?php defined('KOOWA') or die ?>

<div id="post-composer">
	<form id="post-composer-form" action="<?= @route() ?>" method="POST" data-formvalidator-options="'evaluateFieldsOnBlur':true">
		<textarea class="input-block-level" data-validators="minLength:1 maxLength:5000" id="composer-textarea"  name="body" overtext="<?= @text('COM-NOTES-SHARE-PROMPT') ?>"></textarea>
	    <input type="hidden" name="composed" value="1" />
		<div class="post-actions">
		    <?php $app = @service('repos://site/components.component')->find(array('component'=>'com_connect')); ?>
			<?php if ( $app && $app->authorize('echo', array('actor'=>$actor)) ) : ?>
                <?php               
                $services  = ComConnectHelperApi::getServices();
                @service('repos://site/connect.session'); 
                $sessions  = $actor->sessions->toArray();
                foreach($sessions as $key => $session) 
                {
                    if ( $session->getApi()->isReadOnly() ) {
                        unset($sessions[$key]);
                    }
                }                
                ?>    	        
    			<?php if ( count($sessions) > 0 ) : ?>
    			<div class="post-action">
    			    <div class="connect-service-share">
    			    <?php foreach($sessions as $session) : ?>
    			        <a class="btn" data-trigger="Checkbox" data-checkbox-toggle-element="i" data-checkbox-name="channels[]" data-checkbox-value="<?=$session->getName() ?>" data-behavior="BS.Twipsy" title="<?= sprintf(@text('COM-CONNECT-SHARE-POST'), ucfirst($session->api->getName()))?>">
    			            <?= @helper('com://site/connect.template.helper.service.icon', $session->api->getName())?>    			            			         
    			        </a>
    			    <?php endforeach;?>
    			    </div>
    			</div>
    			<?php elseif (count($services) > 0 ) : ?>
    			<a href="<?= @route($actor->getURL().'&get=settings&edit=connect') ?>" class="btn">
    			    <?= @text('COM-CONNECT-ENABLE-SHARE')?>
    			</a>
    			<?php endif; ?>
			<?php endif; ?>
			
			<div class="post-action pull-right">
				<button data-trigger="Share" data-request-options="{inject:'an-stories'}" class="btn btn-primary" >
					<?= @text('LIB-AN-ACTION-SHARE') ?>
				</button>
			</div>
			
			<?php if ( is_person($actor) && !is_viewer($actor) ) : ?>			
			<div class="post-action pull-right">
				<label class="checkbox" for="private-message">
					<input id="private-flag" type="checkbox" name="private"> 
					<?=@text('COM-NOTES-COMPOSER-PRIVATE-FLAG')?>
				</label>
			</div>
			<?php endif; ?>
			
			<div class="post-action right">
				<span class="counter"></span>
			</div>
		</div>
	</form>
</div>