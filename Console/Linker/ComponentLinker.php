<?php 

namespace Console\Linker;

/**
 * Component Link
 *
 */
class ComponentLinker extends AbstractLinker
{
    /**
     * Links a component into a site directory
     * 
     * @param string $site_path
     * @param string $name
     */
    public function __construct($site_path, $component_path, $name = null)
    {    
        parent::__construct($site_path);
        
        if ( empty($name) ) {
            $name = basename($component_path);
        }
        
        $com_name  = 'com_'.$name;
        $paths     = Helper::getSymlinkPaths($component_path, array(
                '#^(component|admin|site)/.+#' => '\1',
                '#^plugins/([^/]+)/([^/]+)/.+#' => 'plugins/\1/\2',
        ),array(
                '#^admin#' => '/administrator/components/'.$com_name,
                '#^site#'  => '/site/components/'.$com_name,
                '#^component#'  => '/libraries/default/'.$name,
                '#^plugins#'    => '/plugins'
        ));
        
        foreach($paths as $source => $target)
        {
            $this->addLink($source, $target);                        
        }
        
        foreach(array('site', 'admin') as $app)
        {
            $target = $app;
            if ( $app == 'admin' ) {
                $target = 'administrator';
            }
            $paths = Helper::getSymlinkPaths($component_path.'/'.$app.'/resources/language',array(),array(
                    '#^([a-zA-Z-]+)\.ini#' => $target."/language/$1/$1.$com_name.ini",
                    '#^([a-zA-Z-]+)\.(\w+)\.ini#' => $target."/language/$1/$1.$2.ini"
            ));
            foreach($paths as $source => $target)
            {
                $this->addLink($source, $target);                
            }
        }
        foreach(array('site', 'admin') as $app)
        {
            $target = $app;
            if ( $app == 'admin' ) {
                $target = 'administrator';
            }
            $paths = Helper::getSymlinkPaths($component_path.'/'.$app.'/modules',array(
                    '#(.*?)/(.*)#' => '\1',
            ),array(
                    '#(\w+)#' => $target.'/modules/mod_\1'
            ));
            foreach($paths as $source => $target)
            {
                $this->addLink($source, $target);                
            }
        }
        
        foreach(array('site', 'admin','component') as $app)
        {
            $target = $app;
            if ( $app == 'admin' ) {
                $target = 'administrator';
            }
            elseif ( $app == 'component' ) {
                $target = '';
            }
            $source   = $component_path.'/'.$app.'/resources/media';
            $target   = $target.'/media/'.str_replace('com_','', $com_name);            
            if ( file_exists($source) )
            {
                $this->addLink($source, $target);                
            }
        }
        foreach(array('site', 'admin') as $app)
        {
            $target = $app;
            if ( $app == 'admin' ) {
                $target = 'administrator';
            }
            $target   = $target.'/templates/'.$name;
            $source   = $component_path.'/'.$app.'/theme';
            if ( file_exists($source) ) {
                $this->addLink($source, $target);
            }
        }        
    }    
}