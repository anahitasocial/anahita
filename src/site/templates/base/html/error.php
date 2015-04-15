<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
  		<meta name="viewport" content="width=device-width, initial-scale=1.0">
  		<?= @render('favicon') ?>
  		<?= @render('style') ?>
        <?= @template('tmpl/js') ?>
        <?= @template('tmpl/analytics') ?>
  	</head>
    <body>
        <?= @template('tmpl/navbar') ?>
        
        <div class="container">
    	<?= $this->getView()->content; ?>
    	</div>
    </body>
</html>
