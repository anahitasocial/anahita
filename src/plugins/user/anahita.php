<?php

class plgUserAnahita extends PlgAnahitaDefault
{

    /**
    * Is called before a person is added or edited
    *
    * @param $event->data
    * @return boolean
    */
    public function onBeforeSavePerson(KEvent $event)
    {
        return true;
    }

    /**
    * Is called before a person is added or edited
    *
    * @param $event->person
    * @return boolean
    */
    public function onAfterSavePerson(KEvent $event)
    {
        return true;
    }

    /**
     * Is called before a person id deleted
     *
     * @param $event->data
     * @return boolean
     */
    public function onBeforeDeletePerson(KEvent $event)
    {
        return true;
    }

    /**
     * Is called before a person id deleted
     *
     * @param $event->person
     * @return boolean
     */
    public function onAfterDeletePerson(KEvent $event)
    {
        return true;
    }

    /**
     * Is called when a person logs in
     *
     * @param $event->credentials
     * @return boolean
     */
    public function onLoginPerson(KEvent $event)
    {
        $credentials = $event->credentials;
        $options = $event->options;

        $person = KService::get('repos:people.person')->find(array(
                        'username' => $credentials->username,
                        'enabled' => 1
                    ));

        if (!isset($person)) {
            $msg = "Did not find a user with username: ".$credentials->username;
            throw new AnErrorException($msg, KHttpResponse::UNAUTHORIZED);
            return false;
        }

        $person->visited();
        $session = KService::get('com:sessions');
        $session->set('person', (object) $person->getData());

        return true;
    }

    /**
     * Is called when a person logs out
     *
     * @param $event->person
     * @return boolean
     */
    public function onLogoutPerson(KEvent $event)
    {
        $person = $event->person;

        if ($person->id === 0) {
            return false;
        }

        KService::get('repos:sessions.session')->destroy(array('nodeId' => $person->id));

        return KService::get('com:sessions')->destroy();
    }
}
