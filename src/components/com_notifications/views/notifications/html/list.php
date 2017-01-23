<? defined('KOOWA') or die ?>

<div class="an-entities">
    <? foreach ($notifications as $notification) : ?>
    <? $data = @helper('parser.parse', $notification, $actor); ?>
    <? $class = $actor->notificationViewed($notification) ? '' : 'an-highlight'; ?>
    <div class="an-entity <?= $class ?>">
	    <div class="entity-portrait-square">
	    	<?= @avatar($notification->subject) ?>
	    </div>

	    <div class="entity-container">
	    	<div class="entity-description">
               	<?= $data['title'] ?>
        	</div>

            <div class="entity-meta">
                <?= @date($notification->creationTime)?>
            </div>
	    </div>
    </div>
    <? endforeach;?>
</div>
<? // endforeach; ?>

<? if (count($notifications) == 0) : ?>
<?= @message(@text('COM-NOTIFICATIONS-EMPTY-LIST-MESSAGE')) ?>
<? endif; ?>
