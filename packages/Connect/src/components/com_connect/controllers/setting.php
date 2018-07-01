<?php

/**
 * Connect Setting Contorller.
 *
 * This is not a dispatchable controller, but it's called as HMVC from an actor
 * setting page
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectControllerSetting extends ComBaseControllerResource
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'oauthorizable',
                'ownable' => array(
                    'default' => $this->getService('com:people.viewer'),
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Removes a token.
     *
     * @param AnCommandContext $context Context parameter
     * @param void
     */
    protected function _actionDelete(AnCommandContext $context)
    {
        $this->getResponse()->status = AnHttpResponse::NO_CONTENT;

        $token = $this->getService('repos:connect.session')->fetchSet(array(
                        'owner' => $this->actor,
                        'api' => $this->getAPI()->getName(),
                    ));

        $token->delete()->save();
    }

    /**
     * After getting the access token store the token in the session and redirect.
     *
     * @param AnCommandContext $context Context parameter
     * @param void
     */
    protected function _actionGetaccesstoken(AnCommandContext $context)
    {
        $data = $context->data;

        $this->getBehavior('oauthorizable')->execute('action.getaccesstoken', $context);
        $user = $this->getAPI()->getUser();
        $session = $this->getService('repos:connect.session')
                        ->findOrAddNew(array(
                            'profileId' => $user->id,
                            'api' => $this->getAPI()->getName(),
                          ));

        $token = $this->getAPI()->getToken();

        if (!empty($token)) {
            $session->setData(array(
                'component' => 'com_connect',
                'owner' => $this->actor,
            ))->setToken($token)->save();
        }

        $route = route($this->actor->getURL().'&get=settings&edit=connect');

        if ($data->return) {
            $route = base64_decode($data->return);
        }

        $context->response->setRedirect($route);
    }

    /**
     * Get action.
     *
     * Renders the actor setting for connect
     *
     * @param AnCommandContext $context Context parameter
     * @param void
     */
    protected function _actionRead(AnCommandContext $context)
    {
        $apis = ComConnectHelperApi::getServices();

        $this->getService('repos:connect.session');

        $sessions = $this->actor->sessions;

        foreach ($apis as $key => $api) {
            if (!$api->canAddService($this->actor)) {
                unset($apis[$key]);
            }
        }

        $this->apis = $apis;
        $this->sessions = $sessions;
    }
}
