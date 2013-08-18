<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Anahita
 * @subpackage Schema
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Anahita Schema Migration
 *
 * @category   Anahita
 * @package    Com_Anahita
 * @subpackage Schema
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAnahitaSchemaMigration extends ComMigratorMigrationAbstract
{        
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'tables' => array(
                    'anahita_edges','anahita_nodes',
                    'components',
                    'core_acl_aro','core_acl_aro_groups',
                    'core_acl_aro_map','core_acl_aro_sections','core_acl_groups_aro_map',
                    'groups','menu','menu_types','migrator_versions','modules','modules_menu',
                    'plugins','session','templates_menu','users'
            )
        ));
    
        parent::_initialize($config);
    } 
}