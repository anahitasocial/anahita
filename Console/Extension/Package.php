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
     * Creates a package using the data
     * 
     * @param array $data
     * 
     * @return Package
     */
    public function __construct(array $data)
    {     
        $this->_name        = $data['name'];
        $this->_vendor      = $data['vendor'];
        $this->_source_path = $data['source'];
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