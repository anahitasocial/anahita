<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
	<div class="span8">
		<?= @helper('ui.header', array()) ?>
		<?= @template('list') ?>
	</div>
	
	<?php if($actor && $actor->id > 0 && empty($filter)): ?>
	<?php $sets = $actor->sets->order('updateTime', 'DESC')->limit(20); ?>
	<?php if(count($sets)): ?>
	<div class="span4 visible-desktop">
		<h4 class="block-title">
		<?= @text('COM-PHOTOS-MODULE-HEADER-SETS') ?>
		</h4>
		<div class="block-content">
		    <?= @controller('sets')->view('sets')->oid($actor->id)->layout('sidebar') ?>
		</div>	
	</div>
	<?php endif; ?>
	<?php endif; ?>
</div>

