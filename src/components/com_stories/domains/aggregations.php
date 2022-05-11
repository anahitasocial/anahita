<?php

/**
 * Story Aggregation keys.
 *   
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComStoriesDomainAggregations extends AnObjectArray implements AnServiceInstantiatable
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
        if (!$container->has($config->service_identifier)) {
            $registry = $container->get('application.registry', array('key' => $config->service_identifier));

            if (!$registry->offsetExists('aggregations')) {
                $components = $container->get('repos:components.component')->fetchSet();

                $dispatcher = $container->get('anahita:event.dispatcher');
                $components->registerEventDispatcher($dispatcher);

                $aggregations = new AnConfig();
                $event = new AnEvent(array('aggregations' => $aggregations));

                $dispatcher->dispatchEvent('onStoryAggregation', $event);
                $registry->offsetSet('aggregations', $aggregations);
            }

            $container->set($config->service_identifier, $registry->offsetGet('aggregations'));
        }

        return $container->get($config->service_identifier);
    }
}
