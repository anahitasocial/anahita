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
    <body>	  	
        <div id="container-system-message" class="container">
        	<?= @render('messages') ?>
        </div>    		
        
        <?= @template('tmpl/navbar') ?>
        
        <div class="container">
        <?= $this->getView()->content; ?>
        </div>
        
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
    </body>
</html>