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
class ComInstallerAdapterPlugin extends ComInstallerAdapterAbstract
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
                'table_name' => 'plugins'
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
        foreach($this->_xml->files->children() as $file) 
        {
            if ( $name = (string)$file->attributes()->plugin )
            {
                $exists = $this->_extExists(array('folder'=>$this->_group,'element'=>$name));                
                if ( !$exists )
                {
                    $this->_extInsert(array(
                            'name'      => (string)$this->_xml->name,
                            'element'   => $name,
                            'folder'    => (string)$this->_group,
                            'published' => 1                            
                    ));
                }
            }
            break;
        }
        
        $files   = array();
        foreach($this->_xml->files->children() as $file) {
            $files[] = (string)$file;
        }
        $files[] = basename($this->_xml_file);
        
        foreach($files as $file)
        {
            $source = $this->_path.'/'.$file;
            $dest   = JPATH_SITE.'/plugins/'.$this->_group.'/'.$file;
            $this->_copy($source, $dest);
        }
        
        if ( isset($this->_xml->languages) )
            $this->_copyLangs($this->_xml->languages, JPATH_SITE.'/administrator/language');
    }    
}