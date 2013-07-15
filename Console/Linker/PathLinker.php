<?php 

namespace Console\Linker;

/**
 * Path linker. Provides linking options between two paths
 *
 */
class PathLinker
{
    /**
     * The source
     * 
     * @var string
     */
    protected $_src;
    
    /**
     * Target path
     * 
     * @var target
     */
    protected $_target;

    /**
     * Constructor
     * 
     * @param string $src
     * @param string $target
     */
    public function __construct($src, $target)
    {
        $this->_src     = $src;
        $this->_target  = $target;
    }

    /**
     * Copies insteas of linking
     */
    public function copy()
    {
        if ( file_exists($this->_target) ) {
            if ( is_link($this->_target) ) {
                unlink($this->_target);
            }
            else {
                exec("rm -rf {$this->_target}");
            }
        }
        exec("cp -r {$this->_src} {$this->_target}");
    }

    /**
     * Unlink
     */
    public function unlink()
    {
        unlink($this->_target);
    }

    /**
     * Symlinks
     */
    public function symlink()
    {
        //check if the parent directory exits
        $path = dirname($this->_target);
        if ( !file_exists($path) ) {
            mkdir($path, 0755, true);
        }
        if ( file_exists($this->_target) )
        {
            if ( is_link($this->_target) ) {

            }
            elseif (is_dir($this->_target)) {
                exec("rm -rf {$this->_target}");
            }
        }
        @symlink($this->_src, $this->_target);
    }

    /**
     * Getter
     * 
     */
    public function __get($key)
    {
        return $this->{'_'.$key};
    }
}

?>