<?php defined('KOOWA') or die ?>

<div class="popover-title">
    <?= @text('COM-NOTIFICATIONS-POPOVER-TITLE') ?>
	<span class="pull-right">
		<a href="<?= @route('oid='.$actor->id.'&layout=default') ?>">
		    <?= @text('COM-NOTIFICATIONS-POPOVER-VIEW-ALL') ?>
		</a>
	</span>
</div>

<div class="popover-content">
	<div id="an-notifications" class="an-entities">
        <?php foreach($notifications as $notification) : ?>
        <?php $class = $actor->notificationViewed($notification) ? '' : 'an-highlight'; ?>    
        <div class="an-entity <?= $class ?>">
        	<div class="entity-portrait-square">
        		<?= @avatar($notification->subject) ?>
        	</div>
        	
        	<div class="entity-container">
                <div class="entity-meta">
        		    <?php $data = @helper('parser.parse', $notification, $actor) ?>
                    <p><?= $data['title']?> <?= @date($notification->creationTime)?></p>
                </div>
           	</div>
        </div>
        <?php endforeach;?>
    </div>
    
    <?php if (count($notifications) == 0) : ?>
    <?= @message(@text('COM-NOTIFICATIONS-EMPTY-LIST-MESSAGE')) ?>
    <?php endif; ?>
</div>