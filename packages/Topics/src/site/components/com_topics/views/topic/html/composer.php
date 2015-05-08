<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $topic = @service('repos:topics.topic')->getEntity()->reset(); ?>

<form class="composer-form" method="post" action="<?= @route() ?>">
	<fieldset>
	    <legend><?= @text('COM-TOPICS-TOPIC-ADD') ?></legend>	
	    	
		<div class="control-group">
			<label class="control-label" for="topic-title">
			    <?= @text('COM-TOPICS-TOPIC-TITLE') ?>
			</label> 
			
			<div class="controls">
				<input id="topic-title" class="input-block-level" name="title" value="" size="50" maxlength="255" type="text" required autofocus />
			</div>
		</div>
		
		<div class="control-group">
            <label class="control-label" for="topic-body">
                <?= @text('LIB-AN-MEDIUM-BODY') ?>
            </label>
            <div class="controls">
                <?= @editor(array(
                    'name'=>'body',
                    'content'=> '', 
                    'html' => array(    
                        'maxlength'=>'20000', 
                        'cols'=>'5',
                        'rows'=>'5', 
                        'class'=>'input-block-level', 
                        'id'=>'topic-body' 
                        )
                )); ?>
            </div>
        </div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" >
			    <?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?>
			</label>
			
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$topic, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
			
		<div class="form-actions">
			<button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
			    <?= @text('LIB-AN-ACTION-POST') ?>
			</button>
		</div>
	</fieldset>
</form>