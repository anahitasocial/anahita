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
 * Token
 *
 * @category   Anahita
 * @package    Com_Invites
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComInvitesDomainEntityToken extends AnDomainEntityAbstract
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
			'attributes' => array(
			    'id'          => array('key'=>true), 
				'serviceName' => array('required'=>true, 'column'=>'service'),
				'value'   => array('required'=>true,'unique'=>true,'column'=>'token'),
				'used'	  => array('default'=>'0')
			),
			'relationships' => array(
				'inviter' => array('parent'=>'com:people.domain.entity.person') 
			)
		));
		
		parent::_initialize($config);	
	}
	
	/**
	 * Return the URL
	 * 
	 * @return string
	 */
	public function getURL()
	{
		return 'option=com_invites&view=token&token='.$this->value;
	}
	
	/**
	 * Increment the number of usage
	 * 
	 * @return void
	 */
	public function incrementUsed()
	{
		$this->used = $this->used + 1;
		return $this;
	}
	
	/**
	 * Generate a default token
	 * 
	 * @param KConfig $config
	 */
	protected function _afterEntityInstantiate(KConfig $config)
	{
		$config->append(array(
			'data' => array(
				'value' =>  hash('sha256', str_shuffle(JFactory::getConfig()->getValue('secret').((string) (int) microtime(true))))
			)
		));	
	}
}