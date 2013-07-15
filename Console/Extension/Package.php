<?php 

namespace Console\Extension;

/**
 * Package class. Represents an anahita extension as a composer package.
 * 
 * A package can contain multiple related components
 *
 */
class Package 
{           
    /**
     * Package vendor name
     * 
     * @var string
     */
    protected $_vendor;
    
    /**
     * Packaeg name
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * Array of components
     * 
     * @var array
     */
    protected $_components;
    
    /**
     * Creates a package using the data
     * 
     * @param array $data
     * 
     * @return Package
     */
    public function __construct(array $data)
    {     
        $this->_name          = $data['name'];
        $this->_vendor        = $data['vendor'];
        $this->_composer_file = $data['composer_file'];

        $package_root = $this->getRoot();
        $components   = array();
        if ( file_exists($package_root.'/src') ) {
            $components[] = $package_root.'/src';
        }
        if ( file_exists($package_root.'/components') )
        {
            foreach(new \DirectoryIterator($package_root.'/components') as $dir)
            {
                if ( $dir->isDir() && !$dir->isDot() ) {
                    $components[] = $dir->getPathname();
                }
            }
        }
        $this->_components    = $components;
    }
    
    /**
     * Return the components
     * 
     * @return array
     */
    public function getComponents()
    {
        return $this->_components;
    }
    
    /**
     * Return the root
     * 
     * @return string
     */
    public function getRoot()
    {
        return dirname($this->_composer_file);    
    }
    
    /**
     * Return the composer file
     * 
     * @return string
     */
    public function getComposerFile()
    {
         return $this->_composer_file;   
    }
    
    /**
     * Return the full composer qualified name vendor/name
     * 
     * @return string
     */
    public function getFullName()
    {
        return $this->getVendor().'/'.$this->getName();    
    }
    
    /**
     * Return the package vendor name
     * 
     * @return string
     */
    public function getVendor()
    {
        return $this->_vendor;
    }
    
    /**
     * Return the package name
     * 
     * @return name
     */
    public function getName()
    {
        return $this->_name;
    }
}

?>