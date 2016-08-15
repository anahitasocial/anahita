<? defined('KOOWA') or die ?>

<? $dates = @helper('notifications.group', $notifications); ?>

<? foreach ($dates as $date => $notifications) : ?>

<div class="an-meta">
    <?= $date ?>
</div>

<div class="an-entities">
    <? foreach ($notifications as $notification) : ?>
    <? $data = @helper('parser.parse', $notification, $actor); ?>
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
    <? endforeach;?>
</div>
<? endforeach; ?>

<? if (count($dates) == 0) : ?>
<?= @message(@text('COM-NOTIFICATIONS-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
