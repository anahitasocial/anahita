<?php defined('KOOWA') or die ?>

<?php if ( $actor->authorize('action', 'com_stories:story:add') ) : ?>
<div id="story-composer">
	<form id="story-composer-form" action="<?= @route('option=com_stories&view=story&oid='.$actor->id) ?>" method="POST" data-formvalidator-options="'evaluateFieldsOnBlur':true">
		<textarea class="input-block-level" data-validators="minLength:1 maxLength:<?=STORY_MAX_LIMIT?>" id="composer-textarea"  name="body" overtext="<?= @text('COM-STORIES-SHARE-PROMPT') ?>"></textarea>
	
		<div class="story-actions">
		    <?php 
		        $app = @service('repos:apps.app')->fetch(array('component'=>'com_connect'));		    
		    ?>
			<?php if ( $app && $app->authorize('echo', array('actor'=>$actor)) ) : ?>
                <?php               
                $services  = ComConnectHelperApi::getServices();
                @service('repos:connect.session'); 
                $sessions  = $actor->sessions->toArray();
                foreach($sessions as $key => $session) 
                {
                    if ( $session->getApi()->isReadOnly() ) {
                        unset($sessions[$key]);
                    }
                }                
                ?>    	        
    			<?php if ( count($sessions) > 0 ) : ?>
    			<div class="story-action">
    			    <div class="connect-service-share">
    			    <?php foreach($sessions as $session) : ?>
    			        <a data-behavior="BS.Twipsy" title="<?= sprintf(@text('COM-CONNECT-SHARE-STORY'), ucfirst($session->api->getName()))?>">
    			            <?= @helper('com://site/connect.template.helper.service.icon', $session->api->getName())?>
    			            <input type="checkbox" name="channels[]" value="<?= $session->getName() ?>" class="hide"/>			         
    			        </a>
    			    <?php endforeach;?>
    			    </div>
    			</div>
    			<?php elseif (count($services) > 0 ) : ?>
    			<a href="<?= @route($actor->getURL().'&get=settings&edit=connect') ?>" class="btn">
    			    <?= @text('COM-CONNECT-ADD-STORY-CHANNEL')?>
    			</a>
    			<?php endif; ?>
			<?php endif; ?>
			
			<div class="story-action pull-right">
				<button data-trigger="Share" data-request-options="{inject:'an-stories'}" class="btn btn-primary" >
					<?= @text('LIB-AN-ACTION-SHARE') ?>
				</button> 
			</div>
			
			<?php if ( is_person($actor) && !is_viewer($actor) ) : ?>			
			<div class="story-action pull-right"> 
				<label class="checkbox" for="private-message"> 
					<input id="private-flag" type="checkbox" name="private_message"> 
					<?=@text('COM-STORIES-COMPOSER-PRIVATE-MESSAGE-FLAG') ?> 
				</label>
			</div>
			<?php endif; ?>
		</div>
	</form>
</div>
<?php endif; ?>