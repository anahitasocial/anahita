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
        elseif ( is_link($this->_target) ) {
            unlink($this->_target);
        }
        elseif (is_dir($this->_target)) {
            exec("rm -rf {$this->_target}");
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

/**
 * 
 * Find the relative file system path between two file system paths
 *
 * @param  string  $frompath  Path to start from
 * @param  string  $topath    Path we want to end up in
 *
 * @return string             Path leading from $frompath to $topath
 */
function find_relative_path ( $frompath, $topath ) {
    $from = explode( DIRECTORY_SEPARATOR, $frompath ); // Folders/File
    $to = explode( DIRECTORY_SEPARATOR, $topath ); // Folders/File
    $relpath = '';

    $i = 0;
    // Find how far the path is the same
    while ( isset($from[$i]) && isset($to[$i]) ) {
        if ( $from[$i] != $to[$i] ) break;
        $i++;
    }
    $j = count( $from ) - 1;
    // Add '..' until the path is the same
    while ( $i <= $j ) {
        if ( !empty($from[$j]) ) $relpath .= '..'.DIRECTORY_SEPARATOR;
        $j--;
    }
    // Go to folder from where it starts differing
    while ( isset($to[$i]) ) {
        if ( !empty($to[$i]) ) $relpath .= $to[$i].DIRECTORY_SEPARATOR;
        $i++;
    }
    
    // Strip last separator
    return substr($relpath, 0, -1);
}

?>