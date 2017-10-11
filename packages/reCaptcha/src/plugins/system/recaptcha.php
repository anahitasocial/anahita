<?php

/**
 * Subscription system plugins. Validates the viewer subscriptions.
 *
 * @category   Anahita
 *
 * @author     Nick Swinford <nick@nicholasjohn16.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class plgSystemRecaptcha extends PlgAnahitaDefault
{
    /**
     * onAfterRender handler.
     */
    public function onAfterRoute(KEvent $event)
    {
        if (KRequest::method() === 'POST' && $this->_hasRecaptcha()) {
            $recaptchaResponse = KRequest::get('post.g-recaptcha-response', 'string', null);
            if (! $this->_verifyResponse($recaptchaResponse)) {
                throw new KException("Unauthorized Request", 403);
                return;
            }
        }
    }

    /**
     * onAfterRender handler.
     */
    public function onAfterDispatch(KEvent $event)
    {

    }

    /**
     * onAfterRender handler.
     */
    public function onAfterRender(KEvent $event)
    {

    }

    /**
    *   Returns true if the request is coming from a form with reCaptcha.
    *   The request is from one of the following forms:
    *   1. login form
    *   2. registration form
    *   3. add group form
    *
    *   @return boolean 
    */
    private function _hasRecaptcha()
    {
        $option = KRequest::get('get.option', 'string', '');
        $view = KRequest::get('get.view', 'cmd', '');
        $id = KRequest::get('get.view', 'int', 0);

        $action = KRequest::get('post.action', 'cmd', 'add');

        if ($option === 'com_people') {
            if ($view === 'session' && $action === 'add') {
                return true;
            }
            if ($view === 'person' && $id === 0) {
                return true;
            }
        }

        if ($option === 'com_groups' && $view === 'group' && $id === 0 && $action === 'add') {
            return true;
        }

        return false;
    }

    /**
    *   Verifies the request reCaptcha token against the reCaptcha API
    *
    *   @return boolean
    */
    private function _verifyResponse($recaptchaResponse)
    {
        $data = http_build_query(array(
                'secret' => $this->_params->get('secret-key'),
                'response' => $recaptchaResponse
            )
        );

        $options = array('http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data
            )
        );

        $context = stream_context_create($options);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);

        return (bool) $result->success;
    }
}
