<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
        <?= @render('style') ?>                
    </head>
    <body>
        <?= @template('tmpl/js') ?>
        <?= @template('tmpl/navbar') ?>
      
        <?= @render('component') ?>
        
        <div class="container">
            <?= @render('copyright') ?>
        </div>
        
        <?= @render('analytics') ?>
    </body>
</html>
