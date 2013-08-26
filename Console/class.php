<?php 

namespace Installer;

class Mapper
{
    protected $_maps = array();
    protected $_src_root;
    protected $_target_root;
    
    public function __construct($src_root, $target_root)
    {
        $this->_src_root    = rtrim($src_root,'/');        
        $this->_target_root = rtrim($target_root,'/'); 
    }
    
    public function getMap($src, $target = null)
    {
        if ( !$target ) {
            $target = $src;
        }
        
        $src     =  $this->_src_root.'/'.ltrim($src, '/');
        $target  =  $this->_target_root.'/'.ltrim($target, '/');
        $map     = new Map($src, $target);        
        return $map;
    }
    
    public function addMap($src, $target = null)
    {        
        $this->_maps[] = $this->getMap($src,$target);
    }
    
    public function addCrawlMap($src, $patterns)
    {        
        if ( !empty($src) ) {
            $root = $this->_src_root.'/'.$src;
        } else {
            $root = $this->_src_root;
        }        
        $crawler = new Crawler($root, $patterns);
        $paths   = $crawler->getPaths();

        foreach($paths as $path) {                        
            $this->addMap($src.'/'.$path, str_replace('site/','',$path));
        }
    }
    
    public function unlink()
    {
        foreach($this->_maps as $map) {
            $map->unlink();
        }
        $files     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->_target_root));
        //deleting deadlinks
        foreach($files as $file) 
        {
            if ( is_link($file) && !file_exists(realpath($file))) {
                unlink($file);
            }
        }
    }
        
    public function symlink()
    {
        foreach($this->_maps as $map) {            
            $map->symlink();
        }
        $files     = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->_target_root));
        //deleting deadlinks
        foreach($files as $file) 
        {
            if ( is_link($file) && !file_exists(realpath($file))) {
                unlink($file);
            }
        }        
    }
}

class Map
{
    protected $_src;
    protected $_target;
    
    public function __construct($src, $target)
    {
        $this->_src     = $src;
        $this->_target  = $target;
    }
    
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
    
    public function unlink()
    {        
         unlink($this->_target);   
    }
    
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
        
        if (strpos(strtoupper(PHP_OS), 'WIN') === 0) {
        	// Windows doesn't support relative symlinking so use absolute ones
        	@symlink($this->_src, $this->_target);
        } else {
        	@symlink($this->_findRelativePath($this->_target, $this->_src), $this->_target);
        }
    }
    
    /**
     * Find from to to
     * 
     * @param string $from
     * @param string $to
     */
    protected function _findRelativePath($from, $to)
    {
        $path  = dirname($from);
        $to    = str_replace(COMPOSER_ROOT.DIRECTORY_SEPARATOR, '', $to);
        while($path != COMPOSER_ROOT) {
            $path = dirname($path);
            $to   = '..'.DIRECTORY_SEPARATOR.$to;
        };
        
        return $to;
    }
    
    public function __get($key)
    {
        return $this->{'_'.$key};
    }
}

class Crawler
{
    protected $_root;
    protected $_paths;    
    
    public function __construct($root, $patterns = array())
    {
        $this->_root = rtrim($root,'/');
              
        if ( !(file_exists($this->_root)) ) {
            throw new \RuntimeException("can't open the directory ".$this->_root);
        }
        
        $paths = array();    
            
        foreach($this->_crawl($this->_root) as $path) 
        {
            foreach($patterns as $pattern => $replacement) {
                $path  = preg_replace($pattern, $replacement, $path);
            }
            if ( !empty($path) ) {
                $paths[] = $path;
            }
        }

        $this->_paths = array_unique($paths);
    }

    public function getRoot()
    {
        return $this->_root;    
    }
    
    public function getPaths()
    {
        return $this->_paths;
    }
    
    protected function _crawl($root)
    {
        $root  = rtrim($root, '/');
        $dh    = opendir($root);
        $paths = array();
        
        while( false !== ( $file = readdir( $dh ) ) )
        {
            if ( strpos($file,'.') === 0   || 
                    $file == 'index.html'  ||
                    $file == 'robots.txt'
                    ) {
                continue;
            }
            $path    = $root.'/'.$file;
            if ( is_dir($path) ) {
                $paths = array_merge($paths, $this->_crawl($path));
            }
            else {
                $paths[] = str_replace($this->_root.'/', '', $path);
            }
        }
        
        return $paths;
    }
}

namespace Console;

class DirectoryFilter implements \Countable, \IteratorAggregate
{
    protected $_found = array();
    
    public function __construct($directories, $search_paths)
    {
        $search_paths = array_reverse($search_paths);
        foreach($search_paths as $path) 
        {
            $iterator = new \DirectoryIterator($path);
            
            foreach($iterator as $file) 
            {
                if ( $file->isDot() || !$file->isDir() ) 
                     continue;
                if ( in_array((string)$file, $directories) ) {
                    $this->_found[$file->getFilename()] = $file->getPathName();
                }
            }
        }
        $this->_found = array_values($this->_found);
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->_found);
    }
    
    public function count()
    {
        return count($this->_found);
    }
}

    

?>