<?php defined('KOOWA') or die;?>

<?php $mobile_nav = @helper('modules.render','mobile', array('style'=>'none')); ?>

<div class="navbar <?= ($this->getView()->getParams()->navbarInverse) ? 'navbar-inverse' : '' ?> navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
        	<?php if($mobile_nav): ?>
        	<a type="button" class="btn btn-navbar" data-trigger="ShowMainmenu">
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        		<span class="icon-bar"></span>
        	</a>
        	<?php endif; ?>
        	
            <?= @render('logo') ?>
            <div id="desktop-main-menu" class="nav-collapse collapse">
	            <?= @helper('modules.render','navigation', array('style'=>'none')) ?>
	            <?php if( $viewer_module = @helper('modules.render','viewer', array('style'=>'none'))): ?>
	            <span class="viewer"><?= $viewer_module ?></span>
	            <?php endif; ?>
            </div>
            
            <?php if($mobile_nav): ?>
            <div id="mobile-main-menu" class="hidden-desktop">
            <?= $mobile_nav ?>
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
            <?php endif; ?>
        </div>
    </div>            
</div>