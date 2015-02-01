<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
	<head>
  		<meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1.0, user-scalable=0">
  	    <?= @render('favicon') ?>
  	    <?= @render('style') ?>
  	</head>
    <body>	  	
        <div id="container-system-message" class="container">
        	<?= @render('messages') ?>
        </div>    		
        
        <?= @template('tmpl/navbar') ?>
        
        <div class="container">
        <?= $this->getView()->content; ?>
        </div>
        
        <?= @template('tmpl/js') ?>
		<?= @render('analytics') ?>
    </body>
</html>