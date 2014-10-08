<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
	<div class="span8">
		<?= @helper('ui.header', array()) ?>
	
		<div class="an-entities-wrapper" id="an-entities-main-wrapper">
		<?= @template('list') ?>
		</div>
	</div>
</div>