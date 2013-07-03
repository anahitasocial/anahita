<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Paginator
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComBaseTemplateHelperPaginator extends KTemplateHelperAbstract
 {
 
     /**
      * Creates a pagination using the passed in values
      *
      * @param array $config Configuration Options 
      * total => list total, limit => list limit, offset => list start offset 
      * 
      * @return string
      */
     public function pagination(array $config)
     { 
         $config = new KConfig($config);
         jimport('joomla.html.pagination');
         $pagination =  new JPagination( $config->total, $config->offset, $config->limit );
         return $pagination->getListFooter();
     }
 }