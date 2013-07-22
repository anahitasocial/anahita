<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
	<head>
		<jdoc:include type="head" />

		<link href="../templates/shiraz/css/style1/style.css" rel="stylesheet" type="text/css" />
		<link href="template/css/template.css" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="../media/lib_anahita/js/production/mootools.js"></script>
		<script type="text/javascript" src="includes/js/installation.js"></script>
		<script type="text/javascript" src="template/js/validation.js"></script>

		<script type="text/javascript">
			Window.onDomReady = function(func) {
				window.addEvent('domready', func);
			}
			window.addEvent('domready', function(){
				new Accordion($$('h3.moofx-toggler'), $$('div.moofx-slider'), {onActive: function(toggler, i) { toggler.addClass('moofx-toggler-down'); },onBackground: function(toggler, i) { toggler.removeClass('moofx-toggler-down'); },duration: 300,opacity: false, alwaysHide:true, show: 1});
			});
  		</script>
	</head>
	<body>		
		<div class="navbar">
        	<div class="navbar-inner">
        		<div class="container">
	        		<a class="brand brand-logo" style="background: url(../templates/base/css/images/logo/logo.png) no-repeat 10px 10px transparent">
	        			Anahita® <?php print Anahita::getVersion() ?> - Social Networking Platform & Framework
	        		</a>
        		</div>
        	</div>
        </div>
		
		<div id="content-box">
			<div id="content-pad">
				<jdoc:include type="installation" />
			</div>
		</div>
		<div id="copyright"><a href="http://www.anahitapolis.com" target="_blank">Anahita®</a>
			<?php echo JText::_('ISFREESOFTWARE') ?>
		</div>
	</body>
</html>
