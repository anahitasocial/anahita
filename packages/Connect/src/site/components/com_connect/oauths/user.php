<?php 
 
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */


/**
 * Service User
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage OAuth
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectOauthUser implements KObjectHandlable
{
	/** 
	 * Profile URL
	 * 
	 * @var string
	 */
	public $profile_url;
				
	/** 
	 * Id
	 * 
	 * @var string
	 */
	public $id;
		
	/** 
	 * Username
	 * 
	 * @var string
	 */
	public $username;
	
	/** 
	 * Email
	 * 
	 * @var string
	 */
	public $email;
	
	/** 
	 * Description
	 * 
	 * @var string
	 */
	public $description;	
		
	/** 
	 * Name
	 * 
	 * @var string
	 */
	public $name;
	
	/** 
	 * Large Avatar
	 * 
	 * @var string
	 */
	public $large_avatar;

	/** 
	 * Thumb Avatar
	 * 
	 * @var string
	 */
	public $thumb_avatar;

	/**
	 * (non-PHPdoc)
	 * @see KObjectHandlable::getHandle()
	 */
	public function getHandle()
	{
	    return spl_object_hash($this);
	}
}