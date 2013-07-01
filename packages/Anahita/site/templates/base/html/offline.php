<?php defined('KOOWA') or die;?>
<!DOCTYPE html>
<html>
    <head>
        <?= @render('style') ?>                
    </head>
    <body>
        <?= @template('tmpl/js') ?>
        <div class="navbar <?= ($this->getView()->getParams()->navbarInverse) ? 'navbar-inverse' : '' ?> navbar-fixed-top">
        	<div class="navbar-inner">
        		<div class="container">
        			<?= @render('logo') ?>
        		</div>
        	</div>
        </div>
   
            <div class="container">
                <div class="row"> 
                    <div class="span12">               
                        <div class="hero-unit">
                        	<h1>Service Offline</h1>
                            <p><?= JFactory::getApplication()->getCfg('offline_message'); ?></p>
                        
                        <form data-behavior="FormValidator" class="well form-inline" action="<?= @route() ?>" method="post">
                        	<input type="hidden" name="remember" value="yes">
                            <input type="hidden" name="option" value="com_user">
                            <input type="hidden" name="task" value="login">
                            <input type="hidden" name="return" value="<?= base64_encode(JURI::base()) ?>" />
                            <?= JHTML::_( 'form.token' ); ?>
                            <input data-behavior="required" type="text" name="username" class="input-medium" placeholder="<?= @text('USERNAME OR EMAIL') ?>">
                            <input data-behavior="required" type="password" name="passwd" class="input-medium" placeholder="<?= @text('PASSWORD') ?>">
                            <button type="submit" class="btn btn-primary"><?=@text('LOGIN')?></button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
            
        <?= @render('analytics') ?>
    </body>
</html>