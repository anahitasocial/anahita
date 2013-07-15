<?php defined('KOOWA') or die('Restricted access');

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Mod_Actor
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */
 
print KService::get('mod://site/viewer.module', array(
	'request' => $params->toArray()
))->display();