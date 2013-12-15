<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
  <head>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<?= @render('favicon') ?>
  	<?= @render('style') ?>
  </head>
  <body>	  	
	<?= @template('tmpl/js') ?>
    
    <div id="container-system-message" class="container">
    	<?= @helper('modules.render','messages', array('style'=>'none')) ?>
    </div>
    		
    <?= @template('tmpl/navbar') ?>
    
    <?= @render('modules', '1', array('style'=>'none')) ?>
	<?= @render('modules', '2', array('style'=>'simple')) ?>
	<?= @render('modules', '3', array('style'=>'simple')) ?>
    <?= @render('modules', '4', array('style'=>'simple')) ?>
    <?= @render('modules', '5', array('style'=>'simple')) ?>
    
    <?= @render('component') ?>

    <?php if($this->getView()->getParams()->poweredby): ?>
    <div class="container">
    	<div class="row">
    		<div class="span12">
    			<p class="muted">
    				<em>Powered by <a href="http://www.anahitapolis.com" target="_blank">Anahita</a></em>
    			</p>
    		</div>
    	</div>
    </div>
    <?php endif; ?>

    <?= @render('analytics') ?> 
  </body>
</html>