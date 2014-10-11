<?php defined('KOOWA') or die ?>

<?php $trendingHashtags = $gadgets->extract('hashtags-trending'); ?>

<div class="row">
    <?php if(count($gadgets) >= 1 ): ?>
    <div class="span2"> 
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
    </div>
    <?php endif; ?>

    <div class="span6" id="container-main">
    
        <?= @helper('com:composer.template.helper.ui.composers', $composers) ?>
        
        <?php if(count($gadgets)): ?>
        <?php foreach($gadgets as $gadget ) : ?>
        <div class="profile-tab-content">	
        	<?= @helper('ui.gadget', $gadget) ?>
        </div>
        <?php endforeach;?>
        <?php endif; ?>
    </div>

    <div class="span4">
    <?= @helper('ui.gadget', $trendingHashtags) ?>	
    </div>
</div>


