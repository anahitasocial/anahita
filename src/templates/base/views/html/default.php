<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= @render('favicon') ?>
  	<?= @render('style') ?>
  </head>

  <body>	  	
	<?= @template('tmpl/js') ?>	
    <?= @template('tmpl/navbar') ?>
    
    <div id="container-system-message" class="container">       
    	<?= @render('messages') ?>
    </div>
    
    <?= @render('modules', 'header') ?> 
	<?= @render('modules', 'showcase', array('style'=>'none')) ?>
	<?= @render('modules', 'feature', array('style'=>'simple')) ?>
    <?= @render('modules', 'utility', array('style'=>'none')) ?>
    <?= @render('modules', 'maintop', array('style'=>'simple')) ?>
    <?= @render('component') ?>
    <?= @render('modules', 'mainbottom', array('style'=>'simple')) ?>
    
    <?php if ( $bottom = @render('modules', 'bottom', array('style'=>'simple')) ) : ?>
    <div id="bottom-wrapper" class="visible-desktop">
    <?= $bottom ?>
    </div>
    <?php endif; ?>

	<?php if ( $footer = @render('modules', 'footer', array('style'=>'simple')) ) : ?>
    <div id="footer-wrapper" class="visible-desktop">
    <?= $footer ?>
    </div>
    <?php endif; ?>
    
    <div id="copyright-wrapper" class="visible-desktop">
    	<div class="container">
    	<?= @render('copyright') ?>
    	</div>
    </div>

    <?= @render('analytics') ?> 
  </body>
</html>