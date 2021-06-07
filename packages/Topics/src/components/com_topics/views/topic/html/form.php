<? defined('ANAHITA') or die('Restricted access');?>

<? $topic = empty($topic) ? @service('repos:topics.topic')->getEntity()->reset() : $topic; ?>

<form method="post" action="<?= @route($topic->getURL().'&oid='.$actor->id)?>">
	<fieldset>
		<legend><?= ($topic->persisted()) ? @text('COM-TOPICS-TOPIC-EDIT') : @text('COM-TOPICS-TOPIC-ADD') ?></legend>
		<div class="control-group">
			<label class="control-label" for="topic-title">
			    <?= @text('LIB-AN-MEDIUM-TITLE') ?>
			</label>
			<div class="controls">
				<input 
					required 
					class="input-block-level" 
					id="topic-title" 
					name="title" 
					value="<?= @escape($topic->title) ?>" 
					maxlength="100" 
					type="text" 
				/>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="topic-body">
			    <?= @text('LIB-AN-MEDIUM-BODY') ?>
			</label>
			<div class="controls">
				<textarea 
					maxlength="40000" 
					class="input-block-level" 
					name="body" 
					cols="10" 
					rows="5" 
					id="topic-body"
				></textarea>
            </div>
		</div>

		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy', array('entity' => $topic, 'auto_submit' => false, 'options' => $actor)) ?>
			</div>
		</div>

		<div class="form-actions">
			<? $cancelURL = ($topic->persisted()) ? $topic->getURL() : 'view=topics&oid='.$actor->id ?>
			<a class="btn" href="<?= @route($cancelURL) ?>">
			    <?= @text('LIB-AN-ACTION-CANCEL') ?>
			</a>

			<? $action = ($topic->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-POST' ?>
			<? $actionLoading = ($topic->persisted()) ? 'LIB-AN-MEDIUM-UPDATING' : 'LIB-AN-MEDIUM-POSTING' ?>
			<button class="btn btn-primary" data-loading-text="<?= @text($actionLoading) ?>">
			    <?= @text($action) ?>
			</button>
		</div>
	</fieldset>
</form>
