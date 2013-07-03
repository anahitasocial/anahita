<?php 

namespace Console\Extension;

/**
 * Extension Helper Class
 * 
 *
 */
class Helper 
{
    /**
     * Searches a path to find composer.json files
     * 
     * @param string $path
     * 
     * @return multitype:string
     */
    public static function getComposerFiles($path)
    {
        $dirs      = new \DirectoryIterator($path);
        $composers = array();
        foreach($dirs as $dir)
        {
            if ( $dir->isDot() || $dir->isFile() )
                continue;
            $composer_file = $dir->getPathName().'/composer.json';
            if ( is_readable($composer_file) ) {
                $composers[] = $composer_file;
            }
        }
        return $composers;        
    }
}