<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

$config = new JConfig();

$config->cache_prefix  = md5($config->secret).'-cache-system';
$config->cache_enabled = $config->caching;

KService::setAlias('application.registry', 'com://admin/application.registry');
KService::setConfig('application.registry', array('cache_prefix'=>$config->cache_prefix,'cache_enabled'=>$config->cache_enabled));

?>