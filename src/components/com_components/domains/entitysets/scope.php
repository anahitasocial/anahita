<?php

/**
 * Search Scopes.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComComponentsDomainEntitysetScope extends AnObjectArray implements AnServiceInstantiatable
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
        if (! $container->has($config->service_identifier)) {
            $registry = $container->get('application.registry', array('key' => $config->service_identifier));

            if (! $registry->offsetExists('scopes')) {
                $components = $container->get('repos:components.component')->fetchSet();

                $dispatcher = $container->get('anahita:event.dispatcher');
                $components->registerEventDispatcher($dispatcher);

                $event = new AnEvent(array('scope' => array()));
                $dispatcher->dispatchEvent('onBeforeFetch', $event);

                $scopes = new self();

                foreach ($event->scope as $scope) {
                    $scope = AnConfig::unbox($scope);

                    if (is_array($scope)) {
                        $scope = $container->get('com:components.domain.entity.scope', $scope);
                    }

                    $scopes[$scope->getKey()] = $scope;
                }

                $registry->offsetSet('scopes', $scopes);
            }

            $container->set($config->service_identifier, $registry->offsetGet('scopes'));
        }

        return $container->get($config->service_identifier);
    }

    /**
     * @var int
     */
    protected $_total;

    /**
     * Return total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->_total;
    }

    /**
     * Set the total of the scope.
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->_total = $total;
    }

    /**
     * Return a scope using a key or not if not found.
     *
     * @param string $scope
     *
     * @return ComSearchDomainScope
     */
    public function find($scope)
    {
        if (strpos($scope, '.') === false) {
            $scope = $scope.'.'.AnInflector::singularize($scope);
        }

        if (isset($this[$scope])) {
            return $this[$scope];
        }

        return;
    }
}
