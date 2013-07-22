<?php

/** 
 * LICENSE: 
 * 
 * @category   Anahita
 * @package    Com_Bazaar
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Bazaar Controller
 *
 * @category   Anahita
 * @package    Com_Bazaar
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBazaarControllerApp extends ComBaseControllerResource 
{
    /**
     * Downloads an app
     *
     * @param KCommandContext $context Context parameter
     *
     * @return void
     */    
    protected function _actionDownload(KCommandContext $context)
    {
        $installer = $this->getService('com:bazaar.domain.model.installer', array(
           'file'       => $this->file,
           'session'    => $this->session,
           'store'      => new ComBazaarDomainModelStore()
        ));
                
        $ret = $installer->install();
        
        $this->setRedirect('view=apps', $installer->getMessage(), $ret === false ? 'error' : null);
    }
    
    /**
     * Displays a bazaar
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _actionBrowse(KCommandContext $context)
    {   
        $this->getToolbar('app')->setTitle(JText::_('Bazaar'));
        
        $this->extensions = ComBazaarDomainModelExtension::getExtensions();
        $this->store      = new ComBazaarDomainModelStore();        
    }
}