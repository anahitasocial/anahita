<?php

class plgUserRecaptcha extends PlgAnahitaDefault
{
    public function onBeforeSavePerson(KEvent $event)
    {
        return $this->verifyResponse($event->data->get('g-recaptcha-response'));
    }

    public function verifyResponse($recaptchaResponse) 
    {
        $data = http_build_query(
            array(
                'secret' => $this->_params->get('secret-key'),
                'response' => $recaptchaResponse
            )
        );

        $options = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $data
            )
        );

        $context = stream_context_create($options);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);

        if(!$result->success) {
            error_log("loggin failed");
          throw new LibBaseControllerExceptionUnauthorized('Recaptcha Failed');
        }

        return true;
    }
}
