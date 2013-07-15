<?php 

namespace Console\Linker;

/**
 * Site Application
 * 
 *
 */
abstract class AbstractLinker
{
    /**
     * Array of path links
     * 
     * @var array
     */
    protected $_links = array();

    /**
     * Root path
     * 
     * @var string
     */
    protected $_root;
    
    /**
     * The path to the root of all destination links. Usually a site folder
     * 
     * @param string $root
     */
    public function __construct($root)
    {
        $this->_root = rtrim($root,'/');        
    }
    
    /**
     * Adds a link from a source to a target. If $relative_to_root is set then
     * $target is prefixed with the root
     * 
     * @param string $from
     * @param string $to
     * @param boolean $relative_to_root
     * 
     * @return PathLinker
     */
    public function addLink($source, $target, $relative_to_root = true)
    {
        $link = $this->getPathLinker($source, $target, $relative_to_root);
        $this->_links[] = $link;
        return $link;
    }
    
    /**
     * Return a path linker
     * 
     * @param string $from
     * @param string $to
     * @param boolean $relative_to_root
     * 
     * @return PathLinker
     */
    public function getPathLinker($source, $target, $relative_to_root = true)
    {
        if ( $relative_to_root ) {
            $target = $this->getPath($target);
        }
        $link = new PathLinker($source, $target);
        return $link;
    }
    
    /**
     * Return the path prfixed with the root
     * 
     * @param string $relative_path
     * 
     * @return string
     */
    public function getPath($relative_path)
    {
        $relative_path = $this->getRoot().'/'.ltrim($relative_path,'/');
        return $relative_path;
    }
    
    /**
     * Return the root path
     * 
     * @return string
     */
    public function getRoot()
    {
        return $this->_root;
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