<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Plugins
 * @subpackage Installer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * System Adpater
 *
 * @category   Anahita
 * @package    Anahita_Plugins
 * @subpackage Installer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgInstallerSystem extends KObject
{
    /**
     * JInstaller
     * 
     * @var JInstaller
     */
    protected $_installer;
    
    /**
     * 
     * @param JInstaller $installer The current JInstaller
     * 
     * @return void
     */
    public function __construct($installer)
    {
        $this->_installer = $installer;
    }
    
    /**
     * Installs
     *
     * @return boolean
     */
    public function install()
    {
        jimport('joomla.filesystem.file');
        $files  = array();
        $source = $this->_installer->getPath('source');
        $dest   = JPATH_ROOT;
        build_tree($source,0,$files);
        foreach($files as $file)
        {
          $dest = str_replace($source,'',$file);
          if ( strpos($dest,'/installation') === 0 )
              continue;
          
          $dest = JPATH_ROOT.$dest;
          $dir  = dirname($dest);
          if ( !JFolder::exists($dir) ) {
              JFolder::create($dir);
          }          
          $ret  = JFile::copy($file, $dest);
        } 
        
        //delete the delete.txt
        if ( file_exists(JPATH_ROOT.'/delete.txt') )
            JFile::delete(JPATH_ROOT.'/delete.txt');
        
        return true;
    }
}

/**
 * Build Tree
 *
 */
function build_tree($path, $level = 0, &$files = array())
{
    $ignore = array( 'cgi-bin', '.', '..', '.svn' );
    // Directories to ignore when listing output. Many hosts
    // will deny PHP access to the cgi-bin.

    $dh = @opendir( $path );
    // Open the directory to the handle $dh

    while( false !== ( $file = readdir( $dh ) ) ){
        // Loop through the directory

        if( !in_array( $file, $ignore ) ){
            // Check that this file is not to be ignored

            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) );
            // Just to add spacing to the list, to better
            // show the directory tree.

            if( is_dir( "$path/$file" ) ){
                // Its a directory, so we need to keep reading down...

                //echo "<strong>$spaces $file</strong><br />";
                build_tree( "$path/$file", ($level+1), $files );
                // Re-call this same function but on a new directory.
                // this is what makes function recursive.

            } else {
                $files[] = "$path/$file";
                //echo "$spaces $file<br />";
                // Just print out the filename

            }

        }

    }

    closedir( $dh );
    // Close the directory handle

}