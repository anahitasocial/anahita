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
        <?= @template('tmpl/navbar') ?>
        <div class="container" id="container-main">
    	<?= $this->getView()->content; ?>
    	</div>
        <?= @render('analytics') ?>
    </body>
</html>
