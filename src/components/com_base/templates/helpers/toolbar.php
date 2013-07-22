<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Toolbar Helper
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseTemplateHelperToolbar extends KTemplateHelperAbstract
{        
    /**
     * Return a container of commands by calling add[Name]Commands on the toolbar
     * object. If the toolbar is not set then 
     *
     * @param string $name   The command set name
     * @param array  $data   Data pass to the controller toolbar 
     * 
     * @return LibBaseTemplateObjectContainer
     */
    public function commands($name, $data = array())
    {
        $toolbar = $this->_template->getHelper('controller')->getToolbar();
        
        if ( isset($data['clone']) ) {
            $toolbar = clone $toolbar;
        }
        
        if ( $toolbar instanceof KControllerToolbarAbstract )
        {
            //reset the toolbar
            $toolbar->reset();
                        
            $method  = 'add'.ucfirst($name).'Commands';
            
            if ( method_exists($toolbar, $method) ) {
                $toolbar->$method();
            }
        
            return $toolbar->getCommands();
        }
    }
}