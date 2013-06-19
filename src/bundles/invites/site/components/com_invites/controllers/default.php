<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Invite Default Contorller
 *
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */

class ComInvitesControllerDefault extends ComBaseControllerResource
{
	
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
            'viewer'        => get_viewer(),
            'language'      => 'com_'.$this->getIdentifier()->package ,
            'toolbars'      => array($this->getIdentifier()->name,'menubar','actorbar'),
            'request'       => array(
                'limit'     => 100000,
                'offset'    => 0                
            ) 
		));     

		$config->append(array(
            'behaviors' => array(
                'ownable' => array('default'=>get_viewer()),  
        )));
        
        parent::_initialize($config);
	}

	/**
	 * If the user is not logged in then don't allow
	 * read
	 * 
	 * @return boolean
	 */
	public function canRead()
	{
	    return !$this->getService('com:people.viewer')->guest();
	}
	
	/**
	 * Read
	 * 
	 * @param KCommandContext $contxt
	 * 
	 * @return void
	 */
	protected function _actionRead($context)
	{
		$this->adapter = $this->getAdapter();
	}
	
	/**
	 * Read
	 * 
	 * @param KCommandContext $contxt
	 * 
	 * @return void
	 */	
	protected function _actionPost($context)
	{
		$this->execute('invite', $context);		
	}
	
	/**
	 * Return the email adapter
	 * 
	 * @return mixed
	 */	
	protected function getAdapter()
	{
		if ( !isset($this->_adapter) ) 
		{
			$session = $this->getService('repos://site/connect.session')
						->fetch(array('owner'=>$this->viewer, 'api'=>$this->getIdentifier()->name));
			
			$this->_adapter = $this->getService('com://site/invites.adapter.'.$this->getIdentifier()->name, array('session'=>$session));			
		}
		
		return $this->_adapter;
	}
}