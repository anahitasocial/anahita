<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Payment
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Express method
 *
 * @category   Anahita
 * @package    Com_Subscriptions
 * @subpackage Domain_Payment
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSubscriptionsDomainPaymentMethodToken implements ComSubscriptionsDomainPaymentMethodInterface
{
    /**
     * Options
     * 
     * @var array
     */
    public $options;
    
    /**
     * Service name
     * 
     * @var string
     */
    public $service;
    
    /**
     * Constructor.
     *
     * @param string $token A token used to authenticate the user with a remote service
     *
     * @return void
     */
    public function __construct($service, $options = array())
    {
        $this->service  = $service;
        $this->options  = $options;
    }
    
    /**
     * (non-PHPdoc)
     * @see ComSubscriptionsDomainPaymentMethodInterface::__toString()
     */
    public function __toString()
    {
        return $this->service;
    }    
}