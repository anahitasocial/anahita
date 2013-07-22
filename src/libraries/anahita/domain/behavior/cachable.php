<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Cachable Behavior 
 *
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainBehaviorCachable extends AnDomainBehaviorAbstract
{   
    /**
     * Counter. Caching only works if the counter is 0
     * 
     * @var int
     */ 
    static $_counter = 0;
    
    /**
     * The repository cache
     * 
     * @var AnDomainRepositoryCache
     */
    protected $_cache;
    
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
        
        $this->_cache = $config->cache;
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
            'priority'   => KCommand::PRIORITY_LOWEST,
            'cache'      => new ArrayObject()
        ));
    
        parent::_initialize($config);
    }

    /**
     * @{inheritdoc}
     */
    public function getMixableMethods(KObject $mixer = null)
    {                        
        return parent::getMixableMethods($mixer);
    }
        
    /**
     * Command handler
     *
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Can return both true or false.
     */
    public function execute( $name, KCommandContext $context)
    {
        $operation = $context->operation;
        $cache	   = $this->_cache;
        $parts     = explode('.', $name);
        if ( $operation & AnDomain::OPERATION_FETCH && self::$_counter == 0 )
        {            
            $key	 	 = (string)$context->query;
            
            if ( $parts[0] == 'before' ) 
            {
                if ( $cache->offsetExists($key) ) 
                {
                    $context->data = $cache->offsetGet($key);
                    return false;
                }
            }
            else
            {
                $cache->offsetSet($key, $context->data);
            }
        } elseif ( $operation && count($parts) == 2 )
        {
             //empty cache first
            if ( count($this->_cache) ) 
            {
                $this->_cache->exchangeArray(array());
            }
            if ( $parts[0] == 'before' ) {
                self::$_counter++;
            } else {
                self::$_counter--;
            }
        }
    }
    
    /**
     * Return the cache object 
     *
     * @return ArrayObject
     */
    public function getCache()
    {
        return $this->_cache;
    }
    
    /**
     * Return the handle
     *
     * @return string
     */
    public function getHandle()
    {
         return KMixinAbstract::getHandle();   
    }
}
