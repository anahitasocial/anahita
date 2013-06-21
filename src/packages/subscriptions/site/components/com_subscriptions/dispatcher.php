<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

require_once(JPATH_LIBRARIES.'/merchant/merchant.php');

/**
 * Subscription Dispatcher
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDispatcher extends ComBaseDispatcherDefault
{
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        if ( $this->getController()->getIdentifier()->name 
                == 'signup' && $config->use_ssl
                ) 
        {
            $this->registerCallback('before.dispatch', array($this, 'redirectHttps'));
        }
    }

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
            'use_ssl' => get_config_value('subscriptions.use_ssl', true)
        ));
    
        parent::_initialize($config);
    }
        
    /**
     * (non-PHPdoc)
     * @see ComBaseDispatcher::_actionDispatch()
     */
    protected function _actionDispatch(KCommandContext $context)
    {
       if ( $this->action == 'confirm' && $this->token ) 
       {
           $context->data->append(array(
               '_action' => 'confirm',
               'token'   => $this->token    
            ));
           
           return $this->execute('post', $context);
       }
       
       return parent::_actionDispatch($context);
    }

    /**
     * Redirects to HTTPs 
     * 
     * @param KCommandContext $context
     * 
     * @return void
     */
    public function redirectHttps(KCommandContext $context)
    {        
        if ( KRequest::url()->scheme == 'http' )
        {
            $url = clone KRequest::url();
            $url->scheme = 'https';
            $context->response->setRedirect($url);
            return false;            
        }
    }
}