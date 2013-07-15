<?php 

namespace Console\Linker;

/**
 * Helper
 *
 */
class Helper
{
    /**
     * Return an array of paths within a directory
     * 
     * @param array $filters Array of regex filers 
     * 
     * @return array
     */
    static public function getPaths($dir, $filters = array())
    {
        if ( !is_readable($dir) ) {
            return array();
        }
        $dir   = rtrim($dir,'/');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $paths = array();
        foreach($files as $file) 
        {
            if ( strpos($file->getBasename(), '.') === 0 )
                continue;
            if ( $file->isFile() && $file->getBasename() == 'index.html') {
                continue;
            }
            //remove the prefix
            $path    = str_replace($dir.'/', '', $file);
            //filter the path
            foreach($filters as $regx => $replace) 
            {
                if ( preg_match($regx, $path) ) 
                {
                    if ( is_string($replace) ) {
                        $path = preg_replace($regx, $replace, $path);
                    } else 
                        $path = preg_replace_callback($regx, $replace, $path);         
                }
            }
            if ( !empty($path) ) {
                $paths[] = $dir.'/'.$path;
            }
        }
        return array_unique($paths);
    }
    
    /**
     * Return an array of paths within a directory
     *
     * @param array $filters Array of regex filers
     *
     * @return array
     */
    static public function getSymlinkPaths($dir, $filters = array(), $symlinks = array())
    {
        $paths   = self::getPaths($dir, $filters);
        $targets = array();
        foreach($paths as $path) 
        {
            $target = str_replace($dir.'/', '', $path);
            foreach($symlinks as $regx => $replace)
            {
                if ( preg_match($regx, $target) )
                {
                    if ( is_string($replace) ) {
                        $target = preg_replace($regx, $replace, $target);
                    } else
                        $target = preg_replace_callback($regx, $replace, $target);
                }
            }
            $targets[$path] = $target;            
        }
        return $targets;
    }
}