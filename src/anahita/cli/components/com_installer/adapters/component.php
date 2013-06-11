<?php

/** 
 * LICENSE: ##LICENSE##
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
class ComInstallerAdapterComponent extends ComInstallerAdapterAbstract
{    
    /**
     * The extension name
     *
     * @var string
     */
    protected $_name;
        
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
        
        $this->_name = strtolower('com_'.$this->_xml->name);
    }
      
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
            'table_name' => 'components'
        ));
    
        parent::_initialize($config);
    }
        
    /**
     * Installs the extension
     *
     * @return void
     */
    public function install()
    {
        if ( isset($this->_xml->administration->files) )
        {
            $folder = $this->_path.'/'.$this->_xml->administration->files->attributes()->folder;
            $this->_copy($folder, JPATH_ADMINISTRATOR.'/components/'.$this->_name);
        }
        
        if ( isset($this->_xml->files) )
        {
            $folder = $this->_path.'/'.$this->_xml->files->attributes()->folder;
            $this->_copy($folder, JPATH_SITE.'/components/'.$this->_name);
        }
        
        if ( isset($this->_xml->administration->languages) )
            $this->_copyLangs($this->_xml->administration->languages, JPATH_ADMINISTRATOR.'/language');
        if ( isset($this->_xml->languages) )
            $this->_copyLangs($this->_xml->languages, JPATH_SITE.'/language');
        if ( isset($this->_xml->libraries) )        
            $this->_copyLibs($this->_xml->libraries, JPATH_SITE.'/libraries');

        $components = KService::get('repos:component.component', array('resources'=>'components'));
        
        //find or create a component
        $component  = $components->findOrAddNew(array('option'=>$this->_name,'parent'=>0), array('data'=>array('params'=>'')));
        
        //remove any child component
        $components->getQuery()->option($this->_name)->parent('0','>')->destroy();
        
        //save the component
        //$component->save();
        
        $admin_menu = $this->_xml->administration->menu;
        
        $site_menu  = @$this->_xml->menu;
        
        $component->setData(array(
            'name'      => (string)$this->_xml->name,
            'enabled'   => 1,
            'link'      => '',
            'adminMenuLink' => '',
            'adminMenuAlt'  => '',            
            'adminMenuImg'  => ''
        ));
        
        if ( $site_menu ) 
        {
            $component->setData(array(
                'link'      => 'option='.$this->_name,
                'adminMenuLink' => 'option='.$this->_name                
            ));
        }        
        elseif ( $admin_menu ) 
        {         
            $component->setData(array(
                'link'      => 'option='.$this->_name,
                'adminMenuLink' => 'option='.$this->_name,
                'adminMenuAlt'  => (string)$admin_menu,            
                'adminMenuImg'  => 'js/ThemeOffice/component.png'
            ));
        }         
        
        $component->save();        
        
        parent::install();        
    }    
}