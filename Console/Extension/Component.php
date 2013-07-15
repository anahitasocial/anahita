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
        $manifest = $path.'/admin/manifest.xml';
        if ( file_exists($manifest) ) 
        {
            $xml       = new \SimpleXMLElement(file_get_contents($manifest));
            $install   = array_pop($xml->xpath('/install'));
            if ( @$install['type'] == 'component') {
                $name  = preg_replace('/[^a-zA-Z]/', '', strtolower((string)@$install->name[0]));
            }
        }
        if ( empty($name) ) {
            $name = basename($path);
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
        $linker = new \Console\Linker\ComponentLinker($site, $this);
        //$linker->link();        
        $options = array_merge(array('schema'=>false), $options);
        print_r($options);
        die;
    }
}