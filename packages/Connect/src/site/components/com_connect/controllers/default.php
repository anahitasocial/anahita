<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Default controller handles calls to the oauth handlers (facebook,twitter)
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectControllerDefault extends ComBaseControllerResource
{
    /**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array('ownable')
        ));
                
        parent::_initialize($config);
    }
    
    /**
     * Authorize a GET request
     * 
     * @return boolean
     */
    public function canGet()
    {
        if ( !$this->actor ) {
            $this->actor = get_viewer();    
        }
        
        if ( !$this->actor ) {
            return false;   
        }
        
        if ( !$this->actor->authorize('administration') ) {
            return false;   
        }
        
        $this->getService('repos://site/connect.session');
        
        $api = $this->actor->sessions->{$this->getIdentifier()->name};        
        
        if ( !$api ) 
            return false;
            
        $this->api = $api;
    }

    /**
     * Return whether can delete or not
     * 
     * @return boolean
     */
    public function canDelete()
    {
        return $this->canGet();
    }
    
    /**
     * Deletes a session
     * 
     * @param KCommandContext $context
     */
    protected function _actionDelete(KCommandContext $context)
    {        
        $session = $this->actor->sessions->find(array('api'=>$this->getIdentifier()->name));
        if ( $session ) {
            $session->delete()->save();
        }
    }
    
    /**
     * Dispatches a call to the oauth handler
     * 
     * @return 
     */
    protected function _actionGet(KCommandContext $context)
    {
        if ( $context->request->getFormat() == 'html' ) {
            $context->response->setRedirect(JRoute::_('format=json&option=com_connect&view='.$this->view));
            return;    
        }
        
        if ( $this->get ) 
        {        
          $url  = ltrim($this->get,'/');
          $data = KConfig::unbox($this->api->get($url));
          $data = json_encode($data);          
        } 
        
        else {
          $data = (array)$this->api->getUser();
        }
 
        $this->getView()->data($data);
        
        return parent::_actionGet($context);        
    }
}