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
     * Adds a link from a source to a target 
     * 
     * @param string $from
     * @param string $to
     * 
     * @return PathLinker
     */
    public function addLink($from, $to)
    {
        $link = new PathLinker($from, $to);
        $this->_links[] = $link;
        return $link;
    }
    
    /**
     * Performs a link
     * 
     * @return void
     */
    public function link()
    {
        print_r($this->_links);
        die;
        foreach($this->_links as $link) {
            $link->symlink();
        }
    }
}