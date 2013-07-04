<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Mod_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Module Controller
 *
 * @category   Anahita
 * @package    Mod_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ModBaseModule extends LibBaseControllerAbstract
{
	/**
	 * Module View
	 * 
	 * @var ModBaseHtml
	 */
	protected $_view;
	
	/**
	 * Module Parameter
	 * 
	 * @var KConfig
	 */
	protected $_request;
	
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
		
		$this->_view = $config->view;
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
			'request'	=> array(),
			'view' 		=> 'html'
		));
	
		parent::_initialize($config);
	}
	
	/**
	 * Displays the module
	 * 
	 * @return string
	 */
	protected function _actionDisplay()
	{
		return $this->getView()->display();		
	}
		
	/**
	 * Get the view object attached to the controller
	 *
	 * @return LibBaseViewAbstract
	 */
	public function getView()
	{
		if(!$this->_view instanceof LibBaseViewAbstract)
		{
			//Make sure we have a view identifier
			if(!($this->_view instanceof KServiceIdentifier)) {
				$this->setView($this->_view);
			}
	
			//Create the view
			$config = array(
					'media_url' => KRequest::root().'/media',
					'base_url'  => KRequest::url()->getUrl(KHttpUrl::BASE),
					'state'     => $this->getState()
			);
			
			$this->_view = $this->getService($this->_view, $config);
	
			//Set the layout
			if(isset($this->_state->layout)) {
				$this->_view->setLayout($this->_state->layout);
			}
		}
	
		return $this->_view;
	}
	
	/**
	 * Method to set a view object attached to the controller
	 *
	 * @param mixed $view An object that implements KObjectIdentifiable, an object that
	 * implements KIndentifierInterface or valid identifier string
	 *
	 * @throws KDatabaseRowsetException If the identifier is not a view identifier
	 *
	 * @return KControllerAbstract
	 */
	public function setView($view)
	{
		if(!($view instanceof ComBaseViewAbstract))
		{
			if(is_string($view) && strpos($view, '.') === false )
			{
				$identifier          = clone $this->getIdentifier();				
				$identifier->name    = $view;
			}
			else $identifier = $this->getIdentifier($view);				
	
			$view = $identifier;
		}
	
		$this->_view = $view;
	
		return $this;
	}

	/**
	 * Renders a module
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->display();
	}
}