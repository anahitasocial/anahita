<?php defined('KOOWA') or die;?>

<div class="navbar <?= ($this->getView()->getParams()->navbarInverse) ? 'navbar-inverse' : '' ?> navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
        	<a type="button" class="btn btn-navbar" data-trigger="ShowMainmenu">
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        	</a>
            
			<?= @render('logo') ?>
            
            <div id="desktop-main-menu" class="nav-collapse collapse">
            	<?= @searchbox('searchbox') ?>
	            <?= @template('menus/main') ?>
	            <span class="viewer pull-right">
	            	<?php if(get_viewer()->guest()): ?>
					<a class="btn btn-primary" data-trigger="BS.showPopup" data-bs-showpopup-url="<?= @route('option=people&view=session&layout=modal&return='.base64_encode(KRequest::url()))?>" >
    				<?= @text('LIB-AN-ACTION-LOGIN') ?>                                               
					</a>
	            	<?php else: ?>
	            	<?= @template('menus/viewer') ?>
	            	<?php endif; ?>
	            </span>
            </div>
            
            <div id="mobile-main-menu" class="hidden-desktop">
            <?= @template('menus/mobile') ?>
            </div>
            
            <script>
            document.getElement('#mobile-main-menu ul').hide();
			Delegator.register('click', {
				'ShowMainmenu' : function(event, el, api) {
					event.stop();
					document.getElement('#mobile-main-menu ul').toggle();
				},
			});
			</script>
        </div>
    </div>            
</div>