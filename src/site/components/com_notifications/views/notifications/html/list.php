<?php defined('KOOWA') or die ?>

<?php $dates = @helper('notifications.group', $notifications); ?>

<?php foreach($dates as $date => $notifications) : ?>

<div class="an-meta">
    <?= $date ?>
</div>

<div class="an-entities">
    <?php foreach($notifications as $notification) : ?>
    <?php $data = @helper('parser.parse', $notification, $actor); ?>
    <div class="an-entity">
	    <div class="entity-portrait-square">
	    	<?= @avatar($notification->subject) ?>
	    </div>
	    	
	    <div class="entity-container">	 
	    	<div class="entity-description">
               	<?= $data['title'] ?> <?= $notification->creationTime->format('%l:%M %p') ?>
        	</div>
	    </div>
    </div>
    <?php endforeach;?>
</div>
<?php endforeach; ?>

<?php if (count($dates) == 0) : ?>
<?= @message(@text('COM-NOTIFICATIONS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>