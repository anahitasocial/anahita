<?php defined('KOOWA') or die; ?>

<div id="actor-profile">

<?php if($item->isEnableable() && !$item->enabled): ?>
<?= @message(@text('COM-ACTORS-PROFILE-DISABLED-PROMPT'), array('type'=>'warning')) ?>
<?php endif; ?>

<module position="sidebar-b">
	<?= @helper('ui.gadget', $gadgets->extract('socialgraph')) ?>	
</module>

<module position="sidebar-a">
<div id="actor-avatar">
	<?= @avatar($item, 'medium', false) ?>
</div>
</module>

<?php if(count($gadgets) > 1) : ?>
<module position="sidebar-a">	
	<ul class="nav nav-pills nav-stacked sidelinks" data-behavior="BS.Tabs" data-bs-tabs-options="{'smooth':true,'tabs-selector':'.profile-tab-selector a','sections-selector':'! * .profile-tab-content'}">
		<?php foreach($gadgets as $index=>$gadget) : ?>
			<li class="profile-tab-selector <?= ($index == 'stories') ? 'active' : ''; ?>">
				<a href="#"><?= $gadget->title ?></a>
			</li>
		<?php endforeach;?>
	</ul>
</module>
<?php endif; ?>

<h2 id="actor-name">
	<?= @name($item, false) ?>
	<?php if(is_person($item)): ?> 
	<small>@<?= $item->username ?></small>
	<?php endif; ?>
</h2>

<?php if(!empty($item->body)): ?>
<div id="actor-description">
	<?= @helper('text.truncate', @content($item->body, array('exclude'=>array('syntax', 'video'))), array('length'=>250, 'read_more'=>true, 'consider_html'=>true)); ?>
</div>
<?php endif; ?>

<?php if(!$viewer->blocking($item)): ?>
<?= @helper('com://site/composer.template.helper.ui.composers', $composers) ?>
<?php endif; ?>

<?php foreach($gadgets as $gadget) : ?>
<div class="profile-tab-content">		
	<?= @helper('ui.gadget', $gadget) ?>
</div>		
<?php endforeach;?>

</div>