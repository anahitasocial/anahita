<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Sharer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Connect abstract sharer. It uses a oauth session to share an object across a remote
 * service
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Sharer
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComConnectSharerAbstract extends KObject implements ComSharesSharerInterface
{
    /**
     * An authenticated oauth session
     * 
     * @var ComConnectOauthServiceAbstract
     */
    protected $_session;
    
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
        
        $this->_session = $config->session;
        
        if ( !$this->_session instanceof ComConnectOauthServiceAbstract) {
            throw new InvalidArgumentException('Session must be an intance of ComConnectOauthServiceAbstract');    
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
    
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Return whether a boolean 
     * 
     * @param ComSharesSharerRequest $request The share request
     * 
     * @return boolean Return whether the share was succesful or not
     */
    public function canShareRequest(ComSharesSharerRequest $request)
    {
        $object = $request->object;
        return $object->access == 'public' && 
            !$this->_session->isReadOnly();
    }
    
    /**
     * Shares a request
     * 
     * @param ComSharesSharerRequest $request The share request
     * 
     * @return boolean Return whether the share was succesful or not
     */
    public function shareRequest(ComSharesSharerRequest $request)
    {
        $ret = false;
        
        if ( $this->canShareRequest($request) ) {
            $this->_session->postUpdate($request->object->body);    
        }
    }
}