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
     * Is called before a person logs in
     *
     * @param $event->credentials
     * @return boolean
     */
    public function onBeforeLoginPerson(KEvent $event)
    {
        return true;
    }

    /**
     * Is called after a person logs in
     *
     * @param $event->person
     * @return boolean
     */
    public function onAfterLoginPerson(KEvent $event)
    {
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
        return true;
    }
}
