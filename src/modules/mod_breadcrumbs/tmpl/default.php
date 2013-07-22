<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   3.1.4 November 12, 2010
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('_JEXEC') or die('Restricted access'); ?>
<?php 

$show_home = $params->get('showHome', false);
$show_home = false;
$crumbs = array();

for ($i = 0; $i < $count; $i ++) 
{
    $item = $list[$i];
    if( !empty($item->link) && $i < $count - 1 ) 
    {
        $crumbs[] = '<a href="'.$item->link.'">'.$item->name.'</a>';
    } 
    elseif ( !empty($item->name) )
    {
        $crumbs[] = '<li class="active">'.$item->name.'</li>';
    }
}

if ( !$show_home ) {
    array_shift($crumbs);
    //if home is not shown
    //we won't show the first breadcrum since 
    //we are already in the context
    if ( count($crumbs) == 1 ) {
        $crumbs = array();
    }
}

$trail = implode('<span class="divider">/</span>', $crumbs);

?>

<?php if($trail) : ?>
    <ul class="breadcrumb">
        <?php print $trail ?>
    </ul>
<?php endif;?>