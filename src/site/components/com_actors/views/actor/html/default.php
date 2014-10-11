<?php defined('KOOWA') or die; ?>

<?php $socialgraphGadget = $gadgets->extract('socialgraph') ?>

<div class="row" id="actor-profile">
	<div class="span2">
		<div id="actor-avatar">
		<?= @avatar($item, 'medium', false) ?>
		</div>
		
		<?php if(count($gadgets) > 1) : ?>	
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
		<?php endif; ?>
	</div>
	
	<div class="span6" id="container-main">
		<?php if($item->isEnableable() && !$item->enabled): ?>
		<?= @message(@text('COM-ACTORS-PROFILE-DISABLED-PROMPT'), array('type'=>'warning')) ?>
		<?php endif; ?>

		<?= @helper('ui.toolbar', array()) ?>

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
	
	<div class="span4">
		<?= @helper('ui.gadget', $socialgraphGadget) ?>	
	</div>
</div>