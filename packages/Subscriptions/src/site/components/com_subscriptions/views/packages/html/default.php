<?php defined('KOOWA') or die('Restricted access'); ?>

<div class="row">
	<div class="span6">
	    
	    <?= @helper('ui.header', array()); ?>
	    
        <div class="an-entities">
        <?php foreach($packages as $package) : ?>
            <?= @view('package')->layout('list')->package($package) ?>
        <?php endforeach; ?>
        </div>
	</div>
</div>
