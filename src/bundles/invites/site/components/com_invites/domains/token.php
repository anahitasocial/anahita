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
class ComInvitesDomainToken extends KObject implements KServiceInstantiatable
{		
	/**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {	
		$config->value = hash('sha256', str_shuffle(JFactory::getConfig()->getValue('secret').((string) (int) microtime(true))));		
		$instance = new ComInvitesDomainToken($config);
		return $instance;    	
    }
	
	/**
	 * Token value
	 * 	 
	 * @var string
	 */
	protected $_value;
	
	/** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
	public function __construct(KConfig $config)
	{
		$this->_value = $config->value;
	}
	
	/**
	 * Return the URL
	 * 
	 * @return string
	 */
	public function getURL()
	{
		return 'option=com_invites&view=token&token='.$this->_value;
	}
	
	/**
	 * Stringify a token 
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->_value;
	}
}