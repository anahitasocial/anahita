<?php

/**
 * Connect Echo plugin.
 *
 * @category     Anahita
 */
class PlgConnectEcho extends PlgAnahitaDefault
{
    /**
     * Called for getting share adapters.
     *
     * @param KEvent $event
     */
    public function onGetShareAdapters(KEvent $event)
    {
        $adapters = $event->adapters;
        $request = $event->request;
        $this->getService('repos:connect.session');
        $sessions = $request->target->sessions;
        foreach ($sessions as $session) {
            $identifier = $this->getIdentifier('com:connect.sharer.'.$session->get('api'));
            $sharer = $this->getService($identifier, array('session' => $session->api));
            $adapters[] = $sharer;
        }
    }
}
