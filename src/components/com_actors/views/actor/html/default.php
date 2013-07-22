<?php defined('KOOWA') or die; ?>

<div id="actor-profile">

<?php if($item->isEnableable() && !$item->enabled): ?>
<?= @message(@text('COM-ACTORS-PROFILE-DISABLED-PROMPT'), array('type'=>'warning')) ?>
<?php endif; ?>

<module position="sidebar-b" style="simple">
	<?= @helper('ui.gadget', $gadgets->extract('socialgraph')) ?>	
</module>

<module position="sidebar-a" style="simple">
<div id="actor-avatar">
	<?= @avatar($item, 'medium', false) ?>
</div>
</module>

<?php if ( count($gadgets) > 1 ) : ?>
<module position="sidebar-a" style="simple">	
	<ul class="nav nav-pills nav-stacked sidelinks" data-behavior="BS.Tabs" data-bs-tabs-options="{'smooth':true,'tabs-selector':'.profile-tab-selector a','sections-selector':'! * .profile-tab-content'}">
		<?php foreach($gadgets as $index=>$gadget) : ?>
			<li class="profile-tab-selector <?= ($index == 'stories') ? 'active' : ''; ?>">
				<a href="#"><?= $gadget->title ?></a>
			</li>
		<?php endforeach;?>
	</ul>
</module>
<?php endif; ?>

<h2 id="actor-name"><?= @name($item, false) ?></h2>

<?php if(!empty($item->body)): ?>
<div id="actor-description">
	<?= @helper('text.truncate', @escape($item->body), array('length'=>250, 'read_more'=>true)); ?>
</div>
<?php endif; ?>
<?= @helper('com://site/composer.template.helper.ui.composers', $composers) ?>
<?php foreach($gadgets as $gadget) : ?>
<div class="profile-tab-content">		
	<?= @helper('ui.gadget', $gadget) ?>
</div>		
<?php endforeach;?>

</div>