<?php defined('KOOWA') or die; ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_actors/js/cover.js" />
<?php else: ?>
<script src="com_actors/js/min/cover.min.js" />
<?php endif; ?>

<?php $socialgraphGadget = $gadgets->extract('socialgraph') ?>   

<?php if( $item->coverSet() ): ?>
<div class="profile-cover" data-src-large="<?= $item->getCoverURL('large'); ?>" data-src-medium="<?= $item->getCoverURL('medium'); ?>"></div>
<?php endif; ?>

<div class="row<?= ($item->coverSet()) ? ' has-cover' : '' ?>" id="actor-profile">
	<div class="span2">
		<div id="actor-avatar">
		<?= @avatar($item, 'medium', false) ?>
		</div>
		
		<?php if(count($gadgets) > 1) : ?>	
		<ul class="nav nav-pills nav-stacked streams">
			<li class="nav-header">
            <?=  @text('LIB-AN-STREAMS') ?>
        	</li>
			<?php foreach($gadgets as $index=>$gadget) : ?>
			<li data-stream="<?= $index ?>" class="<?= ($index == 'stories') ? 'active' : ''; ?>">
				<a href="#<?= $index ?>" data-toggle="tab"><?= $gadget->title ?></a>
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

		<div class="tab-content">
            <?php foreach($gadgets as $index=>$gadget) : ?>
            <div class="tab-pane fade <?= ($index == 'stories') ? 'active in' : ''; ?>" id="<?= $index ?>">	
            	<?= @helper('ui.gadget', $gadget) ?>
            </div>
            <?php endforeach;?>
        </div>
	</div>
	
	<div class="span4 visible-desktop">
		  <?= @helper('ui.gadget', $socialgraphGadget) ?>	
	</div>
</div>