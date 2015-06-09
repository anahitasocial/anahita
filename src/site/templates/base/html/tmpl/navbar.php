<?php defined('KOOWA') or die;?>

<div class="navbar <?= ($this->getView()->getParams()->navbarInverse) ? 'navbar-inverse' : '' ?> navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
        	<button type="button" class="btn btn-navbar collapsed" data-toggle="collapse" data-target=".menu-mobile">
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        	</button>
            
			<?= @render('logo') ?>
            
            <div id="desktop-main-menu" class="nav-collapse collapse">
            	<?= @searchbox('searchbox') ?>
	            <?= @template('menus/main') ?>
	            <span class="viewer pull-right">
	            	<?php if(get_viewer()->guest()): ?>
	            	<?php $return = base64UrlEncode( KRequest::url() ); ?>    
					<a data-trigger="OpenModal" class="btn btn-primary" href="#" data-url="<?= @route('option=people&view=session&layout=modal&connect=1&return='.$return)?>" >
    				    <?= @text('LIB-AN-ACTION-LOGIN') ?>                                               
					</a>
	            	<?php else: ?>
	            	<?= @template('menus/viewer') ?>
	            	<?php endif; ?>
	            </span>
            </div>
            
            <div id="mobile-main-menu" class="nav-collapse collapse menu-mobile hidden-desktop">
            <?= @template('menus/mobile') ?>
            </div>
        </div>
    </div>            
</div>