<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'default')) ?>

<module position="sidebar-b" style="none"></module>

<div id="sub-tos" class="an-entity">
	<h2 class="entity-title"><?= @escape($tos->title) ?></h2>
	
	<div class="entity-description">
		<?= $tos->introtext ?>
	</div>
	<?php 
	    $class      = '';
	    $checkboxes = array(get_config_value('subscriptions.tos_confirmation_checkbox1'),get_config_value('subscriptions.tos_confirmation_checkbox2'));
	    foreach($checkboxes as $i => $checkbox) : 
	?>
	   <?php if ( !$checkbox ) continue ?>
	   <?php $class = 'disabled'; ?>
	   <div class="clearfix alert alert-box alert-warning">
	         <?= @html('checkbox','confirm'.$i)->class('confirm-tos')?>
             <?= $checkbox ?>
	   </div>
	<?php endforeach;?>
	<div class="well">
		<a href="index.php" class="btn"><?=@text('COM-SUB-TERM-CANCEL')?></a> 
		<a id="proceed" href="<?=@route('layout=payment&id='.$item->id)?>" class="btn btn-primary <?= $class ?>"><?=@text('COM-SUB-TERM-AGREE')?></a>
	</div>
</div>

