<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?= @render('favicon') ?>
		<?= @render('style') ?>        
    </head>
    <body id="tmpl-component">        
  		<?= @template('tmpl/js') ?>        
        <div id="container-system-message" class="container">       
    		<?= @render('messages') ?>
    	</div>
        
        <?= @render('component') ?>
        <?= @render('analytics') ?>
    </body>
</html>