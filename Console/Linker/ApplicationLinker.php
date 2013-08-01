<?php 

namespace Console\Linker;

/**
 * Site Application
 * 
 *
 */
class ApplicationLinker extends AbstractLinker
{
    /**
     * The target
     * 
     * @var string
     */
    protected $_target;

    /**
     * Anahtia ROOT
     * 
     * @var string
     */
    protected $_anahita_root;
    
    /**
    * Creates a application
    * 
    * @param string $destination The path where the site is being created
    * @param string $mirror      Mirror path. If set it will symlink the target ot the mirror folder
    * 
    * @return void
    */
    public function __construct($target, $anahita_root)
    { 
        parent::__construct($target);                
        $this->_anahtia_root = $anahita_root;
    }
    
    /**
     * Links a component. A component has to follow the component file stucture
     * 
     * @param string $path A path to the component
     * 
     * @return void
     */
    public function linkComponent($path)
    {
              

    }
    
    /**
     * Treat all subdirectories as component and link them into the target path
     * 
     * @param string $path
     * 
     * @return void
     */
    public function linkComponents($path)
    {
        $dirs = new \DirectoryIterator($path);
        foreach($dirs as $dir) 
        {
            if ( $dir->isDir() && !$dir->isDot() ) {
               $linker = new ComponentLinker($this->getRoot(), $dir->getPathname());
               $linker->link();
            }        
        }
        die;
    }
    
    /**
     * Links a path into the target folder by mirroring 
     * 
     * @param stirng $path
     * 
     * @return void
     */
    public function linkMirror($path)
    {        
        $paths = Helper::getSymlinkPaths($path, array(
                '#^(site|administrator)/(includes)/.+#' => '\1/\2',                
                '#^(site|administrator)/(components|modules|templates|media)/([^/]+)/.+#' => '\1/\2/\3',                
                '#^(media|libraries)/([^/]+)/.+#' => '\1/\2',
                '#^(site|administrator)/(images)/.+#' => '',                
                '#^plugins/([^/]+)/([^/]+)/.+#' => 'plugins/\1/\2',
                '#^(vendors|migration|installation)/.+#'    => '',
                '#^configuration\.php-dist#'   => '',
                '#^htaccess.txt#'   => '',
                '#^robots.txt#'   => '',
                '#^administrator/index.php#'    => '',
                '#^index.php#'    => '',                
        ));                       
        foreach($paths as $source => $target) {
            $this->addLink($source, $target, true);
        }   
           
    }
    
    /**
     * (non-PHPdoc)
     * @see \Console\Linker\AbstractLinker::link()
     */
    public function link()
    {
        $files     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getRoot()));
        //deleting deadlinks
        foreach($files as $file)
        {
            if ( is_link($file) && !file_exists(realpath($file))) {
                unlink($file);
            }
        }
                
        @mkdir($this->getRoot().'/tmp',   0755);
        @mkdir($this->getRoot().'/log',   0755);
        @mkdir($this->getRoot().'/site/cache', 0755, true);
        @mkdir($this->getRoot().'/administrator/cache',   0755, true);
        $this->linkMirror($this->_anahtia_root.'/vendor/joomla');
        $this->linkMirror($this->_anahtia_root.'/vendor/nooku');
        $this->linkMirror($this->_anahtia_root.'/Core/application');
        parent::link();
        $this->getPathLinker($this->_anahtia_root.'/vendor/mc/rt_missioncontrol_j15',
                'administrator/templates/rt_missioncontrol_j15'
        )->symlink();
        $this->getPathLinker($this->_anahtia_root.'/vendor/joomla/administrator/index.php','administrator/index.php')->copy();
        $this->getPathLinker($this->_anahtia_root.'/vendor/joomla/administrator/images','/administrator/images')->copy();
        $this->getPathLinker($this->_anahtia_root.'/vendor/joomla/site/images','/site/images')->copy();
        $this->getPathLinker($this->_anahtia_root.'/vendor/joomla/index.php','index.php')->copy();
        $this->getPathLinker($this->_anahtia_root.'/vendor/joomla/htaccess.txt','.htaccess')->copy();
        $this->getPathLinker($this->_anahtia_root.'/vendor/joomla/robots.txt','.htaccess')->copy();
        $this->linkComponents($this->_anahtia_root.'/Core/components');        
    }
    
    /**
     * Return the target
     * 
     * @return string
     */
    public function getTarget()
    {
        return $this->_target;
    }
}

?>