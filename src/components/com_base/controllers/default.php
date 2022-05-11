<?php

/**
 * Service Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
final class ComBaseControllerDefault extends ComBaseControllerService implements AnServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param AnConfigInterface  $config    An optional AnConfig object with configuration options
     * @param AnServiceInterface $container A AnServiceInterface object
     *
     * @return AnServiceInstantiatable
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        $strIdentifier = (string) $config->service_identifier;
        $registery = $container->get('application.registry', array('key' => $strIdentifier.'_default_class'));

        if (!$registery->offsetExists($strIdentifier)) {
            try {
                $identifier = clone $config->service_identifier;
                $identifier->type = 'repos';
                $identifier->path = array('domain','entity');
                $default = array(
                    'prefix' => $container->get($identifier)->getClone(),
                    'fallback' => 'ComBaseControllerDefault',
                    );
            } catch (Exception $e) {
                $default = 'Com'.ucfirst($config->service_identifier->package).'ControllerDefault';
                $default = array(
                  'default' => array(
                      $default,
                      'ComBaseControllerResource',
                      ), );
            }

            $default['identifier'] = $config->service_identifier;
            register_default($default);
            $classname = AnServiceClass::findDefaultClass($config->service_identifier);
            $config->service_identifier->classname = $classname;
            $registery->offsetSet($strIdentifier, $classname);
        }

        $classname = $registery->offsetGet($strIdentifier);
        $instance = new $classname($config);

        return $instance;
    }
}
