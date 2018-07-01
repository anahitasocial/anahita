<?php

/**
 * Session object. After an actor has been authenticated, session store its authentication
 * token/value.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectDomainRepositorySession extends AnDomainRepositoryDefault
{
    /**
     * Modify session query to only bring the sessions that are available.
     */
    protected function _beforeRepositoryFetch(AnCommandContext $context)
    {
        $query = $context->query;
        $services = array_keys(ComConnectHelperApi::getServices());
        $query->api($services);
    }
}
