<? defined('KOOWA') or die;?>

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
	            	<? if (get_viewer()->guest()): ?>
	            	<? $return = base64UrlEncode(KRequest::url()); ?>
					      <a class="btn btn-primary" href="<?= @route('option=people&view=session&return='.$return) ?>" >
    				         <?= @text('LIB-AN-ACTION-LOGIN') ?>
					      </a>
	            	<? else: ?>
	            	<?= @template('menus/viewer') ?>
	            	<? endif; ?>
	            </span>
            </div>

            <div id="mobile-main-menu" class="nav-collapse collapse menu-mobile hidden-desktop">
            <?= @template('menus/mobile') ?>
            </div>
        </div>
    </div>
</div>
