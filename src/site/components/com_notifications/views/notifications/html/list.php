<?php defined('KOOWA') or die ?>
<?php
$dates = @helper('notifications.group', $notifications);
?>
<?php foreach($dates as $date => $notifications) : ?>
<h3><?=$date?></h3>
<div id="com-notifications-list-" class="an-entities">
    <?php foreach($notifications as $notification) : ?>
    <?php 
    $data = @helper('parser.parse', $notification, $actor);
    ?>
    <div class="an-entity an-record an-removable">
    	<div class="clearfix">
	    	<div class="entity-portrait-square">
	    		<?= @avatar($notification->subject) ?>
	    	</div>
	    	
	    	<div class="entity-container">	
	    		<p class="entity-title">	    			
        			<?= $data['title']?>
	    		</p>
	    	    <div class="body">
	    	        <?= $data['body']?>
	    	    </div>
	    		<div class="entity-meta">
                	<?= $notification->creationTime->format('%l:%M %p')?>
        		</div>
	    	</div>
    	</div>
    </div>
    <?php endforeach;?>
</div>

<?php endforeach; ?>

<?php if (count($dates) == 0) : ?>
<?= @message(@text('COM-NOTIFICATIONS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>