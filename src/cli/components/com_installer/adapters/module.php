<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita_Dev
 * @package    Com_Installer
 * @subpackage Adapter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Installer Controller
 *
 * @category   Anahita_Dev
 * @package    Com_Installer
 * @subpackage Adapter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComInstallerAdapterModule extends ComInstallerAdapterAbstract
{
    /**
     * The plugin group
     *
     * @var string
     */
    protected $_group;    
        
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_group = $this->_xml->attributes()->group;
    }
      
    /**
     * Installs the extension
     *
     * @return void
     */
    public function install()
    {
        $module = '';
        foreach($this->_xml->files->children() as $file)
        {
            if ( $module = $file->attributes()->module) {                
                break;
            }
        }
        
        if ( !$module ) return; 

        $this->_copy($this->_path, JPATH_BASE.'/modules/'.$module);
        
        $this->_copyLangs($this->_xml->languages, JPATH_BASE.'/language');
        $this->_copyMedia($this->_xml->media, JPATH_BASE.'/media/');
    }
}