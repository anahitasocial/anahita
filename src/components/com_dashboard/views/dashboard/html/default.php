<?php defined('KOOWA') or die ?>


<module position="sidebar-b" style="simple"></module>

<?php if ( count($gadgets) >= 1 ) : ?>
<module position="sidebar-a" style="simple">   
    <ul class="nav nav-pills nav-stacked sidelinks" data-behavior="BS.Tabs" data-bs-tabs-options="{'smooth':true,'tabs-selector':'.profile-tab-selector a','sections-selector':'! * .profile-tab-content'}">
        <li class="nav-header">
            <?=  @text('LIB-AN-STREAMS') ?>
        </li>    
        <?php foreach($gadgets as $index=>$gadget) : ?>
            <li class="profile-tab-selector <?= ($index == 'stories') ? 'active' : ''; ?>">
            	<a href="#"><?= $gadget->title ?></a>
            </li>
        <?php endforeach;?>
    </ul>
</module>

<?php endif; ?>

<?= @helper('com:composer.template.helper.ui.composers', $composers) ?>

<?php if(count($gadgets)): ?>
<?php foreach($gadgets as $gadget ) : ?>
<div class="profile-tab-content">	
	<?= @helper('ui.gadget', $gadget) ?>
</div>
<?php endforeach;?>

<?php endif; ?>
