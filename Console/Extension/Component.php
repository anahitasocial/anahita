<?php 

namespace Console\Extension;

/**
 * Component 
 * 
 *
 */
class Component 
{
    /**
     * Component name
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * The path to the component
     * 
     * @var string
     */
    protected $_path;
    
    /**
     * Component manifest
     * 
     * @var SimpleXMLElement
     */
    protected $_manifest;
    
    /**
     * Constuctor
     */
    public function __construct($path)
    {           
         //find a the manifest file
        $manifest = array_pop($this->_findManifests($path.'/admin','component'));
        $name     = basename($path);
        if ( $manifest ) 
        {
            $install   = array_pop($manifest->xpath('/install'));
            $name  = preg_replace('/[^a-zA-Z]/', '', strtolower((string)@$install->name[0]));
        }
                
        $this->_name = $name;
        $this->_path = $path;
    }
    
    /**
     * Return the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Retunr the path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Uses a linker to link
     * 
     * @param string $site
     * 
     * @return void
     */
    public function install($site, $output, $options)
    {
        $linker = new \Console\Linker\ComponentLinker($site, $this->getPath(), $this->getName());
        $linker->link();        
        $options   = array_merge(array('schema'=>false), $options);
        $manifests = $this->_findManifests($this->_path);
        foreach($manifests as $manifest) 
        {
            $type     = $manifest['type'];
            $method   = '_install'.ucfirst($type);
            $name     = (string)$manifest->name[0].' '.$type;
            if ( method_exists($this, $method) ) {
                $this->$method($manifest, $output, $options['schema']);
            }                        
        }
    }
    
    /**
     * Searches through a directory and return any manifest file
     * 
     * @param $path   The path to search
     * @param $types  Type of manifests files
     * 
     * @return array
     */
    protected function _findManifests($path, $types = array('component','plugin'))
    {
        settype($types, 'array');
        if ( !file_exists($path) ) {
            return array();
        }
        $files     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $manifests = array();
        foreach($files as $file)
        {
            if ( $file->isFile() &&
                    $file->getExtension() == 'xml' )
            {
                $xml     = new \SimpleXMLElement(file_get_contents($file));
                $install = array_pop($xml->xpath('/install'));
                if ( $install &&
                        in_array($install['type'], $types) )
                {
                    $manifests[dirname($file)] = $install;
                }
            }
        }
        return $manifests;                
    }
    
    protected function _installPlugin($manifest, $output)
    {
        $plugins = \KService::get('repos:cli.plugin',
                array('resources'=>'plugins'));
    
        $group   = (string)$manifest->attributes()->group;
    
        foreach($manifest->files->children() as $file)
        {
            if ( $name = (string)$file->attributes()->plugin )
            {
                $plugin = $plugins->findOrAddNew(array(
                        'element' => $name,
                        'folder'  => $group
                ), array('data'=>array('params'=>'','published'=>true)));
                $plugin->name = (string)$manifest->name;
                $plugin->saveEntity();
                $output->writeLn("<info>...installing $group plugin $name</info>");
                return;
            }
        }
    }
    
    /**
     * Adds a component into the database
     * 
     * @param SimpleXMLElement $manifest
     * @param IO $output
     * @param string $path
     * @param bool $schema
     */
    protected function _installComponent($manifest, $output, $schema)
    {
        $name       = \KService::get('koowa:filter.cmd')->sanitize($manifest->name[0]);
        $name       = 'com_'.strtolower($name);
    
    
        $components = \KService::get('repos:cli.component',
                array('resources'=>'components'));
    
        //find or create a component
        $component  = $components->findOrAddNew(array('option'=>$name,'parent'=>0),
                array('data'=>array('params'=>'')));
    
        //remove any child component
        $components->getQuery()
        ->option($name)
        ->parent('0','>')->destroy();
    
        $admin_menu = $manifest->administration->menu;
        $site_menu  = $manifest->menu;
    
        $component->setData(array(
                'name'      => (string)$manifest->name[0],
                'enabled'   => 1,
                'link'      => '',
                'adminMenuLink' => '',
                'adminMenuAlt'  => '',
                'adminMenuImg'  => ''
        ));
    
        if ( $site_menu )
        {
            $component->setData(array(
                    'link'      => 'option='.$name,
                    'adminMenuLink' => 'option='.$name
            ));
        }
        elseif ( $admin_menu )
        {
            $component->setData(array(
                    'link'      => 'option='.$name,
                    'adminMenuLink' => 'option='.$name,
                    'adminMenuAlt'  => (string)$admin_menu,
                    'adminMenuImg'  => 'js/ThemeOffice/component.png'
            ));
        }
        //first time installing the component then
        //run the schema
        if ( $component->isNew() ) {
            $schema = true;
        }
        $output->writeLn('<info>...installing '.str_replace('com_','',$name).' component</info>');
        $component->saveEntity();
        if ( $schema &&
                file_exists($this->_path.'/admin/schemas/schema.sql') )
        {
            $output->writeLn('<info>...running schema for '.str_replace('com_','',$name).' component</info>');
            \KService::get('koowa:loader')->loadIdentifier('com://admin/migrator.helper');            
            $queries = dbparse(file_get_contents($this->_path.'/admin/schemas/schema.sql'));
            foreach($queries as $query) {
                \KService::get('koowa:database.adapter.mysqli')
                ->execute($query);
            }
        }
    
    }    
}