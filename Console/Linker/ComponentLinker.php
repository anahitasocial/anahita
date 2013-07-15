<?php 

namespace Console\Linker;

/**
 * Component Link
 *
 */
class ComponentLinker
{
    /**
     * Links
     * 
     * @var array
     */
    protected $_links = array();
    
    /**
     * Links a component into a site directory
     * 
     * @param string $site_path
     * @param string $component_path
     */
    public function __construct($site_path, $component_path)
    {    
        //find a the manifest file
        $manifest = $component_path.'/admin/manifest.xml';
        if ( file_exists($manifest) ) 
        {
            $xml       = new \SimpleXMLElement(file_get_contents($manifest));
            $install   = array_pop($xml->xpath('/install'));
            if ( @$install['type'] == 'component') {
                $name  = preg_replace('/[^a-zA-Z]/', '', strtolower((string)@$install->name[0]));
            }
        }
        if ( empty($name) ) {
            $name = basename($component_path);
        }
        $com_name  = 'com_'.$name;
        $paths     = Helper::getSymlinkPaths($component_path, array(
                '#^(component|admin|site)/.+#' => '\1',
                '#^plugins/([^/]+)/([^/]+)/.+#' => 'plugins/\1/\2',
        ),array(
                '#^admin#' => $site_path.'/administrator/components/'.$com_name,
                '#^site#'  => $site_path.'/components/'.$com_name,
                '#^component#'  => $site_path.'/libraries/default/'.$name,
                '#^plugins#'    => $site_path.'/plugins'
        ));
        
        foreach($paths as $source => $target)
        {
            $this->_links[] = new PathLinker($source, $target);            
        }
        
        foreach(array('site', 'admin') as $app)
        {
            $target = $site_path;
            if ( $app == 'admin' ) {
                $target = $site_path.'/administrator';
            }
            $paths = Helper::getSymlinkPaths($component_path.'/'.$app.'/resources/language',array(),array(
                    '#^([a-zA-Z-]+)\.ini#' => $target."/language/$1/$1.$com_name.ini",
                    '#^([a-zA-Z-]+)\.(\w+)\.ini#' => $target."/language/$1/$1.$2.ini"
            ));
            foreach($paths as $source => $target)
            {
                $this->_links[] = new PathLinker($source, $target);
            }
        }
        foreach(array('site', 'admin') as $app)
        {
            $target = $site_path;
            if ( $app == 'admin' ) {
                $target = $site_path.'/administrator';
            }
            $paths = Helper::getSymlinkPaths($component_path.'/'.$app.'/modules',array(
                    '#(.*?)/(.*)#' => '\1',
            ),array(
                    '#(\w+)#' => $target.'/modules/mod_\1'
            ));
            foreach($paths as $source => $target)
            {
                $this->_links[]= new PathLinker($source, $target);                
            }
        }
        
        foreach(array('site', 'admin') as $app)
        {
            $target = $site_path;
            if ( $app == 'admin' ) {
                $target = $site_path.'/administrator';
            }
            $source   = $component_path.'/'.$app.'/resources/media';
            $target   = $target.'/media/'.$com_name;
            if ( file_exists($source) )
            {
                $this->_links[] = new PathLinker($source, $target);                
            }
        }
        foreach(array('site', 'admin') as $app)
        {
            $target = $site_path;
            if ( $app == 'admin' ) {
                $target = $site_path.'/administrator';
            }
            $target   = $target.'/templates/'.$name;
            $source   = $component_path.'/'.$app.'/theme';
            if ( file_exists($source) ) {
                $this->_links[] = new PathLinker($source, $target);
            }
        }        
    }
    
    /**
     * Performs a link
     * 
     * @return void
     */
    public function link()
    {
        foreach($this->_links as $link) {
            $link->symlink();
        }
    }
}