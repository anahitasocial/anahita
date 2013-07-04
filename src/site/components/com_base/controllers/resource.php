<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Restful Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerResource extends LibBaseControllerResource
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

        //load the language
        JFactory::getLanguage()->load( $config->language );
        
        $this->_state->viewer = $config->viewer;
    }
        
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
            'viewer'        => $this->getService('com:people.viewer'),
		    'language'      => 'com_'.$this->getIdentifier()->package,
		    'behaviors'     => to_hash('com://site/application.controller.behavior.message')
		));
				
		parent::_initialize($config);
		
	}
    
    /**
     * Get a toolbar by identifier
     *
     * @return KControllerToolbarAbstract
     */
    public function getToolbar($toolbar, $config = array())
    {
        if ( is_string($toolbar) )
        {
            //if actorbar or menu alawys default to the base
            if ( in_array($toolbar, array('actorbar','menubar')) )
            {
                $identifier       = clone $this->getIdentifier();
                $identifier->path = array('controller','toolbar');
                $identifier->name = $toolbar;               
                register_default(array('identifier'=>$identifier, 'default'=>'ComBaseControllerToolbar'.ucfirst($toolbar)));                
                $toolbar = $identifier;
            }
        }
    
        return parent::getToolbar($toolbar, $config);
    }  

    /**
     * Sets the context response
     *
     * @return KCommandContext
     */
    public function getCommandContext()
    {
        $context = parent::getCommandContext();
        $context->viewer = $this->_state->viewer;
        return $context;
    }
}