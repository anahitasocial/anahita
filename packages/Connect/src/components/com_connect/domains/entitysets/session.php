<?php

/**
 * Session entityset. Provides some nice API to get the correct session.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectDomainEntitysetSession extends AnDomainEntitysetDefault
{
    /**
     * Return a session with a name.
     *
     * @param string $service The service name
     *
     * @return ComConnectDomainEntitySession
     */
    public function getSessionApi($service)
    {
        if ($session = $this->find(array('api' => $service))) {
            return $session->api;
        }
        
        return null;
    }

    /**
     * Forwards a non-property $key to the getSessionApi.
     *
     * @return ComConnectDomainEntitySession
     */
    public function __get($key)
    {
        if (! $this->_repository->getDescription()->getProperty($key)) {
            return $this->getSessionApi($key);
        }

        return parent::__get($key);
    }
}
