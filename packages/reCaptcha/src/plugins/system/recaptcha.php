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
    public function onAfterRoute()
    {
        $option = KRequest::get('get.option', 'string', null);
        $view = KRequest::get('get.option', 'string', null);
        $email = KRequest::get('post.email', 'email', null);
        $username = KRequest::get('post.email', 'username', null);

        if ($option == 'com_people' && $view == 'person' && $mail != null && $username != null) {
            $this->verifyResponse($event->data->get('g-recaptcha-response'));
            die('here');
    	}
    }

    private function verifyResponse($recaptchaResponse)
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

        if(! $result->success) {
          error_log("loggin failed");
          throw new LibBaseControllerExceptionUnauthorized('Recaptcha Failed');
        }

        return true;
    }
}
