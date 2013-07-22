<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// load and init the MissioControl Class
require_once('lib/missioncontrol.class.php');
$mctrl = MissionControl::getInstance();
$mctrl->initRenderer();
$mctrl->addStyle("core.css");
$mctrl->addStyle("menu.css");
$mctrl->addStyle("colors.css.php");
$mctrl->addStyle("http://fonts.googleapis.com/css?family=Josefin+Sans+Std+Light&subset=latin");
$mctrl->addOverrideStyles();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $mctrl->language; ?>" lang="<?php echo $mctrl->language; ?>" dir="<?php echo $mctrl->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body id="mc-standalone" class="<?php $mctrl->displayBodyTags(); ?>">
		<div id="mc-body">
			<jdoc:include type="message" />
			<div id="mc-component2">
				<jdoc:include type="component" />
			</div>
		</div>	
	</body>
</html>