<?php defined('KOOWA') or die; ?>

<module position="sidebar-b" style="none"></module>

<?php if(count($gadgets) >= 1): ?>
<module position="sidebar-a">   
<ul class="nav nav-pills nav-stacked sidelinks" data-behavior="BS.Tabs" data-bs-tabs-options="{'smooth':true,'tabs-selector':'.profile-tab-selector a','sections-selector':'! * .profile-tab-content'}">   
<?php $i=0; ?>
<?php foreach($gadgets as $index=>$gadget) : ?>
	<li class="profile-tab-selector <?= ($i == 0) ? 'active' : ''; ?>">
		<a href="#"><?= $gadget->title ?></a>
    </li>
<?php $i++; ?>    
<?php endforeach;?>
</ul>
</module>

<?php endif; ?>

<h1 id="entity-name"><?= sprintf(@text('COM-HASHTAG-TERM'), $item->name) ?></h1>

<?php if(!empty($item->body)): ?>
<div id="entity-description">
	<?= @helper('text.truncate', @escape($item->body), array('length'=>250, 'read_more'=>true)); ?>
</div>
<?php endif; ?>

<?php foreach($gadgets as $gadget) : ?>
<div class="profile-tab-content">		
	<?= @helper('ui.gadget', $gadget) ?>
</div>		
<?php endforeach;?>