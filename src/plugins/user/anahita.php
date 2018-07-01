<?php

class plgUserAnahita extends PlgAnahitaDefault
{

    /**
    * Is called before a person is added
    *
    * @param $event->data
    * @return boolean
    */
    public function onBeforeAddPerson(AnEvent $event)
    {
        return true;
    }

    /**
    * Is called before a person is added
    *
    * @param $event->person
    * @return boolean
    */
    public function onAfterAddPerson(AnEvent $event)
    {
        return true;
    }

    /**
    * Is called before a person is edited
    *
    * @param $event->data
    * @return boolean
    */
    public function onBeforeEditPerson(AnEvent $event)
    {
        return true;
    }

    /**
    * Is called before a person is edited
    *
    * @param $event->person
    * @return boolean
    */
    public function onAfterEditPerson(AnEvent $event)
    {
        return true;
    }

    /**
     * Is called before a person id deleted
     *
     * @param $event->data
     * @return boolean
     */
    public function onBeforeDeletePerson(AnEvent $event)
    {
        return true;
    }

    /**
     * Is called before a person id deleted
     *
     * @param $event->id person id
     * @return boolean
     */
    public function onAfterDeletePerson(AnEvent $event)
    {
        return true;
    }

    /**
     * Is called before a person logs in
     *
     * @param $event->credentials
     * @return boolean
     */
    public function onBeforeLoginPerson(AnEvent $event)
    {
        return true;
    }

    /**
     * Is called after a person logs in
     *
     * @param $event->person
     * @return boolean
     */
    public function onAfterLoginPerson(AnEvent $event)
    {
        return true;
    }

    /**
     * Is called before a person logs out
     *
     * @param $event->person
     * @return boolean
     */
    public function onBeforeLogoutPerson(AnEvent $event)
    {
        return true;
    }

    /**
     * Is called after a person logs out
     *
     * @param $event->id person id
     * @return boolean
     */
    public function onAfterLogoutPerson(AnEvent $event)
    {
        return true;
    }
}
