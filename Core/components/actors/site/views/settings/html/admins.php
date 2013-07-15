<?php defined('KOOWA') or die('Restricted access');?>
<script src="lib_anahita/js/groupadmin.js" />

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-ADMINS') ?></h3>

<form id="an-entities-search" class="well" action="<?= @route($item->getURL()) ?>" method="post">
	<input type="hidden" name="action" value="addadmin" />
	<input type="hidden" name="adminid" value="" />
	<p><?= @text('COM-ACTORS-MANAGE-ADMINSTRATORS-DESCIRPTION')?></p>
	<input class="search-query input-large" type="text" data-behavior="FollowerAutoComplete"  data-autocomplete-url="<?= @route($item->getURL().'&get=candidates') ?>"/>
</form>

<div id="an-actors" class="an-entities">
	<?php foreach($item->administrators as $actor ) : ?>
	<div class="an-entity an-record">
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
				<button data-trigger="Submit" data-submit-options="{'promptMsg':'Are you sure you want to remove admin'}" href="<?= @route($item->getURL().'&action=removeadmin&adminid='.$actor->id) ?>" class="btn btn-danger"><?= @text('LIB-AN-ACTION-REMOVE') ?></button>
                <?php endif; ?>
			</div>
		</div>
	</div>		
	<?php endforeach; ?>
</div>