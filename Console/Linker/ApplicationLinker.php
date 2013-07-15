<?php 

namespace Console\Linker;

/**
 * Site Application
 * 
 *
 */
class ApplicationLinker
{
    /**
     * The target
     * 
     * @var string
     */
    protected  $_target;
    
   /**
    * Creates a application
    * 
    * @param string $destination The path where the site is being created
    * @param string $mirror      Mirror path. If set it will symlink the target ot the mirror folder
    * 
    * @return void
    */
    public function __construct($destination, $anahita_root = null)
    { 
        $this->_target = $destination;
        
        @mkdir($this->_target.'/tmp',   0755);
        @mkdir($this->_target.'/cache', 0755);
        @mkdir($this->_target.'/log',   0755);
        @mkdir($this->_target.'/administrator/cache',   0755);      
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
        $name  = 'com_'.basename($path);
        $paths = Helper::getSymlinkPaths($path, array(
            '#^(component|admin|site)/.+#' => '\1',
            '#^plugins/([^/]+)/([^/]+)/.+#' => 'plugins/\1/\2',                
        ),array(
            '#^admin#' => $this->_target.'/administrator/components/'.$name,
            '#^site#'  => $this->_target.'/components/'.$name,
            '#^component#'  => $this->_target.'/libraries/default/'.basename($path),
            '#^plugins#'    => $this->_target.'/plugins'                
        ));          
        
        foreach($paths as $source => $target)
        {
            $linker = new PathLinker($source, $target);
            $linker->symlink();
        }
        
        foreach(array('site', 'admin') as $app)
        {   
            $target = $this->_target;
            if ( $app == 'admin' ) {
                $target = $this->_target.'/administrator';
            }
            $paths = Helper::getSymlinkPaths($path.'/'.$app.'/resources/language',array(),array(
                    '#^([a-zA-Z-]+)\.ini#' => $target."/language/$1/$1.$name.ini",
                    '#^([a-zA-Z-]+)\.(\w+)\.ini#' => $target."/language/$1/$1.$2.ini"
            ));
            foreach($paths as $source => $target) 
            {
                $linker = new PathLinker($source, $target);
                $linker->symlink();
            }
        }
        foreach(array('site', 'admin') as $app)
        {
            $target = $this->_target;
            if ( $app == 'admin' ) {
                $target = $this->_target.'/administrator';
            }
            $paths = Helper::getSymlinkPaths($path.'/'.$app.'/modules',array(
                    '#(.*?)/(.*)#' => '\1',
                    ),array(
                    '#(\w+)#' => $target.'/modules/mod_\1'
            ));
            foreach($paths as $source => $target)
            {
                $linker = new PathLinker($source, $target);
                $linker->symlink();
            }
        }
                
        foreach(array('site', 'admin') as $app)
        {
            $target = $this->_target;
            if ( $app == 'admin' ) {
                $target = $this->_target.'/administrator';
            }
            $source   = $path.'/'.$app.'/resources/media';
            $target   = $target.'/media/'.$name;
            if ( file_exists($source) ) 
            {
                $linker = new PathLinker($source, $target);
                $linker->symlink();
            }            
        }
        foreach(array('site', 'admin') as $app)
        {
            $target = $this->_target;
            if ( $app == 'admin' ) {
                $target = $this->_target.'/administrator';
            }
            $target   = $target.'/templates/'.str_replace('com_','',$name);
            $source   = $path.'/'.$app.'/theme';
            if ( file_exists($source) )
            {
                $linker = new PathLinker($source, $target);
                $linker->symlink();
            }
        }        

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
                $this->linkComponent($dir->getPathname());
            }        
        }
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
                '#^(site|administrator)/includes/.+#' => '\1/includes',                
                '#^(administrator)/([^/]+)/([^/]+)/.+#' => '\1/\2/\3',
                '#^(components|modules|templates|libraries|media)/([^/]+)/.+#' => '\1/\2',
                '#^(site)/(.*)#' => '\2',
                '#^plugins/([^/]+)/([^/]+)/.+#' => 'plugins/\1/\2',
                '#^(administrator/)?(images)/.+#' => '\1\2',
                '#^(vendors|migration|installation)/.+#'    => '',
                '#^configuration\.php-dist#'   => '',
                '#^htaccess.txt#'   => '',
                '#^robots.txt#'   => '',
                '#^administrator/index.php#'    => '',
                '#^index.php#'    => '',                
        ));
                        
        foreach($paths as $source => $target)
        {
            $target = $this->_target.'/'.$target;
            $linker = new PathLinker($source, $target);
            $linker->symlink();
        }   
           
    }
    
    /**
     * Reutnr path linker for a single file or directory
     * 
     * @param string $source
     * @param string $path
     * 
     * @return PathLinker
     */
    public function getPathLinker($source, $path)
    {
        $target = $this->_target.'/'.$path;
        $linker = new PathLinker($source, $target);
        return $linker;
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