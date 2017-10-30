<?php

class plgUserAnahita extends PlgAnahitaDefault
{

    /**
    * Is called before a person is added
    *
    * @param $event->data
    * @return boolean
    */
    public function onBeforeAddPerson(KEvent $event)
    {
        return true;
    }

    /**
    * Is called before a person is added
    *
    * @param $event->person
    * @return boolean
    */
    public function onAfterAddPerson(KEvent $event)
    {
        return true;
    }

    /**
    * Is called before a person is edited
    *
    * @param $event->data
    * @return boolean
    */
    public function onBeforeEditPerson(KEvent $event)
    {
        return true;
    }

    /**
    * Is called before a person is edited
    *
    * @param $event->person
    * @return boolean
    */
    public function onAfterEditPerson(KEvent $event)
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
     * @param $event->id person id
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
     * Is called before a person logs out
     *
     * @param $event->person
     * @return boolean
     */
    public function onBeforeLogoutPerson(KEvent $event)
    {
        return true;
    }

    /**
     * Is called after a person logs out
     *
     * @param $event->id person id
     * @return boolean
     */
    public function onAfterLogoutPerson(KEvent $event)
    {
        return true;
    }
}
