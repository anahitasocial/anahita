<?php 

namespace Console\Extension;

/**
 * Package class. Represents an anahita extension as a composer package.
 * 
 * At the time, an extension package can contains multiple components but later
 * this a package <==> component
 *
 */
class Package 
{       
    /**
     * Package source path
     * 
     * @var string
     */
    protected $_source_path;
    
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
     * Return the composer file
     * 
     * @var string
     */
    protected $_composer_file;
    
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
        $this->_source_path   = $data['source'];
        $this->_composer_file = $data['composer_file'];
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
     * Return the package source path
     * 
     * @return string
     */
    public function getSourcePath()
    {
        return $this->_source_path;
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