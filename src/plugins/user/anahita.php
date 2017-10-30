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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
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
        error_log(__FUNCTION__);
        return true;
    }
}
