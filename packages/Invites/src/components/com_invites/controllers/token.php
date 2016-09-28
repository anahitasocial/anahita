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
class ComInvitesControllerToken extends ComBaseControllerService
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'toolbars' => null,
        ));

        parent::_initialize($config);
    }

    /**
     * Token Read.
     *
     * @param KCommandContext $context
     */
    protected function _actionRead(KCommandContext $context)
    {
        if ($this->invitetoken) {

            $token = $this->getRepository()->find(array('value' => $this->invitetoken));
            $this->getToolbar('menubar')->setTitle(null);

            if (!$token || !isset($token->inviter)) {
                throw new LibBaseControllerExceptionNotFound('Token not found');
                return;
            }

            if ($this->viewer->guest()) {
                KRequest::set('session.invite_token', $token->value);
            }

            $this->setItem($token);

        } else {
            $service = pick($this->service, 'facebook');
            $token = $this->getRepository()->getEntity()->reset();
            KRequest::set('session.invite_token', $token->value);
            $this->getView()->value($token->value);
            return $this->getView()->display();
        }
    }

    /**
     * Store a token for a service.
     *
     * @param KCommandContext $context
     */
    protected function _actionAdd(KCommandContext $context)
    {
        $data = $context->data;
        $value = KRequest::get('session.invite_token', 'string', null);

        if (empty($data->value) || $value != $data->value) {
            throw new LibBaseControllerExceptionBadRequest('Invalid token signature');
            return;
        }

        KRequest::set('session.invite_token', null);

        $token = $this->getRepository()->getEntity(array(
                        'data' => array(
                            'value' => $value,
                            'inviter' => get_viewer(),
                            'serviceName' => 'facebook',
                        ),
                    ));

        if (!$token->save()) {
            throw new LibBaseControllerExceptionInternal();
            return;
        }
    }
}
