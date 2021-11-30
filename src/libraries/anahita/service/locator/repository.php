<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Repositroy locator is AnService locator to return repository objects of any entities.
 *
 * The format of the identifier must be repos:[//application/]<Component Name>.<Entity Name>. This will translate
 * to AnService::get(com:[//application/]<Component Name>.domain.entity.<Entity Name>
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnServiceLocatorRepository extends AnServiceLocatorAbstract implements AnServiceInstantiatable
{
    /**
     * Return.
     *
     * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
     * @param AnServiceInterface $container A AnServiceInterface object
     *
     * @return AnServiceInstantiatable
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $identifier = self::_identifier($config->service_identifier);
            $instance = AnDomain::getRepository($identifier, $config->toArray());
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * The type.
     *
     * @var string
     */
    protected $_type = 'repos';

    /**
     * Get the classname based on an identifier.
     *
     * @param 	mixed  		 An identifier object - anahita:[path].name
     *
     * @return string|false Return object on success, returns FALSE on failure
     */
    public function findClass(AnServiceIdentifier $identifier)
    {
        return __CLASS__;
    }

    /**
     * Get the path based on an identifier.
     *
     * @param  object  	An identifier object - anahita:[path].name
     *
     * @return string Returns the path
     */
    public function findPath(AnServiceIdentifier $identifier)
    {
        return self::_identifier($identifier)->filepath;
    }

    /**
     * Converts a repos locator identifier repos:[//application/]<Component>.<Name> to a
     * component identifier.
     *
     * @param AnServiceIdentifier $identifier
     *
     * @return AnServiceIdentifier
     */
    protected static function _identifier(AnServiceIdentifier $identifier)
    {
        $identifier = clone $identifier;

        if (!$identifier->name) {
            $identifier->name = AnInflector::singularize($identifier->package);
        }

        $identifier->type = 'com';
        $identifier->path = array('domain','entity');

        return $identifier;
    }
}
