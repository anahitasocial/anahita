<?php
/**
 * @version     $Id: default.php 3024 2011-10-09 01:44:30Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Articles
 * @copyright   Copyright (C) 2007 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

defined('KOOWA') or die('Restricted access') ?>

<module position="title" content="replace">
	<?= @helper('toolbar.title', array('toolbar' => $toolbar))?>
</module>

<module position="toolbar" content="replace">
	<?= @helper('toolbar.render', array('toolbar' => $toolbar))?>
</module>

<module position="submenu" content="replace">
	<?= @helper('menubar.render', array('menubar' => $menubar))?>
</module>