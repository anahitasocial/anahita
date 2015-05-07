<?php defined('KOOWA') or die('Restricted access');?>

<?php $topic = empty($topic) ? @service('repos:topics.topic')->getEntity()->reset() : $topic; ?>

<form method="post" action="<?= @route($topic->getURL().'&oid='.$actor->id )?>">
	<fieldset>
		<legend><?= ($topic->persisted()) ? @text('COM-TOPICS-TOPIC-EDIT') : @text('COM-TOPICS-TOPIC-ADD') ?></legend>
		<div class="control-group">
			<label class="control-label" for="topic-title">
			    <?= @text('LIB-AN-MEDIUM-TITLE') ?>
			</label>
			<div class="controls">
				<input required class="input-block-level" id="topic-title" name="title" value="<?= @escape( $topic->title ) ?>" size="50" maxlength="255" type="text" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="topic-body">
			    <?= @text('LIB-AN-MEDIUM-BODY') ?>
			</label>
			<div class="controls">
                <?= @editor(array(
                    'name'=>'body',
                    'content'=> @escape( $topic->body ), 
                    'html' => array(    
                        'maxlength'=>'20000', 
                        'cols'=>'10',
                        'rows'=>'5', 
                        'class'=>'input-block-level', 
                        'id'=>'topic-body' 
                        )
                )); ?>
            </div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$topic, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
			
		<div class="form-actions">
			<?php $cancelURL = ($topic->persisted()) ? $topic->getURL() : 'view=topics&oid='.$actor->id ?>
			<a class="btn" href="<?= @route($cancelURL) ?>">
			    <?= @text('LIB-AN-ACTION-CANCEL') ?>
			</a>  
			
			<?php $action = ($topic->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-POST' ?>
			<?php $actionLoading = ($topic->persisted()) ? 'LIB-AN-MEDIUM-UPDATING' : 'LIB-AN-MEDIUM-POSTING' ?>
			<button class="btn btn-primary" data-loading-text="<?= @text($actionLoading) ?>">
			    <?= @text($action) ?>
			</button>
		</div>
	</fieldset>
</form>
