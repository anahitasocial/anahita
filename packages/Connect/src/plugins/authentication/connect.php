<?php

/**
 * Anahita Authentication plugin.
 *
 * @category     Anahita
 *
 */
class PlgAuthenticationConnect extends PlgAnahitaDefault
{
    /**
     * Authenticates a user using oauth_token,oauth_secret.
     *
     *
     * @param array  $credentials Array holding the user credentials
     * @param array  $options     Array of extra options
     * @param object $response    Authentication response object
     *
     * @return bool
     *
     * @since 1.5
     */
    public function onAuthenticate(KEvent $event)
    {
        $credentials = $event->credentials;
        $response = $event->response;

        if (isset($credentials->username) && isset($credentials->password)) {
            return;
        }

        if (!isset($credentials->oauth_token) || !isset($credentials->oauth_handler)) {
            return;
        }

        try {

            extract($credentials, EXTR_SKIP);

            //if oatuh secret not set then set it to null
            if (empty($oauth_secret)) {
                $oauth_secret = '';
            }

            //lets get the api
            $api = ComConnectHelperApi::getApi($oauth_handler);
            $api->setToken($oauth_token, $oauth_secret);

            //if we can get the logged in user then
            //the user is authenticated
            if ($profile_id = $api->getUser()->id) {
                //lets find a valid sesison
                //lets be strict and make sure all the values match
                $session = KService::get('repos:connect.session')->find(array(
                            'owner.type' => 'com:people.domain.entity.person',
                            'profileId' => $profile_id,
                            'tokenKey' => $oauth_token,
                            'api' => $oauth_handler,
                        ));

                if (! is_null($session)) {
                    $response->status = ComPeopleAuthentication::STATUS_SUCCESS;
                    $response->username = $session->owner->username;
                    $response->password = '';
                    $reponse->email = '';
                    $response->error_message = '';
                }
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
