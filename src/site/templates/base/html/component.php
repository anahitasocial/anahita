<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
	<head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">	
        <?= @render('favicon') ?>	
		<?= @render('style') ?>  
        <?= @template('tmpl/js') ?> 
        <?= @render('analytics') ?>     
    </head>
    <body id="tmpl-component">           
        <div id="container-system-message" class="container">       
    		<?= @render('messages') ?>
    	</div>
        
        <div class="container">
        <?= $this->getView()->content; ?>
        </div>
    </body>
</html>