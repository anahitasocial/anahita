<div id="an-entities-main" class="an-entities masonry" data-behavior="InfinitScroll" data-infinitscroll-options="{'numColumns':6,'url':'<?= @route('layout=list') ?>','limit':<?=$limit?>}">
    <?php foreach($users as $user): ?>
    <div class="an-entity">
    	<div class="entity-actions">
    		<a class="btn" href="#" data-trigger="Invite" data-invite-fbid="<?= $user->id ?>"><?= @text('COM-INVITES-ACTION-INVITE') ?></a>
    	</div>
    
    	<div class="entity-portrait-medium">
    		<img src="<?= $user->thumb_avatar ?>?type=large" >
    	</div>
    	
    	<h3 class="entity-title">
    		<?= $user->name ?>
    	</h3>
    </div>
    <?php endforeach; ?>
</div>
