<?php defined('KOOWA') or die('Restricted access'); ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-ADMINS') ?></h3>

<form id="an-actors-search" class="well" action="<?= @route($item->getURL()) ?>" method="post">
	<input type="hidden" name="action" value="addadmin" />
	<input type="hidden" name="adminid" />
	<p><?= @text('COM-ACTORS-MANAGE-ADMINSTRATORS-DESCIRPTION')?></p>
	
	<div class="input-append">
		<input autocomplete="off" data-url="<?= @route($item->getURL().'&get=candidates') ?>" class="input-large" type="text" />
		<button type="submit" class="btn btn-primary">+ <?= @text('LIB-AN-ACTION-ADD') ?></button>
	</div>
</form>

<div id="an-actors" class="an-entities">
	<?php foreach($item->administrators as $actor ) : ?>
	<div class="an-entity">
		<div class="entity-portrait-square">
			<?= @avatar($actor) ?>
		</div>
		
		<div class="entity-container">
			<h3 class="entity-name">
				<?= @name($actor) ?>
			</h3>
			
			<div class="entity-description">
			    <?= @helper('text.truncate',strip_tags($actor->description), array('length'=>200)); ?>
			</div>
			
			<div class="entity-meta">
				<?= $actor->followerCount ?> 
				<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
				/ <?= $item->leaderCount ?> 
				<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
			</div>
				
			<div class="entity-actions">
                <?php if ( $item->authorize('remove.admin', array('admin'=>$actor)) ) : ?>
                <a class="btn btn-danger" data-action="removeadmin" href="<?= @route($item->getURL()) ?>" data-adminid="<?= $actor->id ?>">
                	<?= @text('LIB-AN-ACTION-REMOVE') ?>
                </a>
                <?php endif; ?>
			</div>
		</div>
	</div>		
	<?php endforeach; ?>
</div>