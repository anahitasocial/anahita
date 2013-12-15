<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?= @render('style') ?>                
    </head>
    <body>
        <?= @template('tmpl/js') ?>
        <?= @template('tmpl/navbar') ?>
      
        <?= @render('component') ?>

        <?= @render('analytics') ?>
    </body>
</html>
