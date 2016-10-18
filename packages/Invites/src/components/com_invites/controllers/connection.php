<?php

/**
 * Invite Default Contorller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComInvitesControllerConnection extends ComInvitesControllerDefault
{
    /**
     * Context.
     *
     * @param KCommandContext $context
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        $serviceType = pick($this->service, 'facebook');

        if (!ComConnectHelperApi::enabled($serviceType)) {
            throw new LibBaseControllerExceptionBadRequest('Service is not enabled');
            return;
        }

        $this->getService('repos:connect.session');
        $service = $this->viewer->sessions->$serviceType;

        if (!empty($service)) {

            try {
                $this->_state->setList($service->getFriends());
            } catch (Exception $e) {
                $session = $this->viewer->sessions->find(array('api' => 'facebook'));

                if (isset($session)) {
                    $session->delete()->save();
                }

                $service = null;
            }

        } else {
            $service = null;
        }

        $this->service = $service;
    }
}
