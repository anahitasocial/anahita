<?php

/**
 * Application Registry. Instantiates a registry with prefix using the application
 * secret.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComApplicationRegistry extends KObject implements KServiceInstantiatable
{
    /**
     * clonable registry.
     *
     * @var AnRegistry
     */
    protected static $_clone = null;

    /**
     * Array of instances for registries.
     *
     * @var array
     */
    protected static $_instances = array();

    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (is_null(self::$_clone)) {
            self::$_clone = new AnRegistry();
        }

        if ($config->key) {
            $config->cache_prefix .= '-'.$config->key;
            unset($config->key);
        }

        if (! isset(self::$_instances[$config->cache_prefix])) {

            $instance = clone self::$_clone;
            self::$_instances[$config->cache_prefix] = $instance;

            $instance->setCachePrefix($config->cache_prefix);
            $instance->enableCache($config->cache_enabled);
        }

        return self::$_instances[$config->cache_prefix];
    }
}
