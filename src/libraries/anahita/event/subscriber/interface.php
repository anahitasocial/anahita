<?php

/**
 * Event Handler Interface
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://www.Anahita.io
 * @package     AnEvent
 * @subpackage 	Subscriber
 */
interface AnEventSubscriberInterface
{
    /**
     * Get the priority of the subscriber
     *
     * @return	integer The event priority
     */
    public function getPriority();
          
    /**
     * Get a list of subscribed events
     *
     * Event handlers always start with 'on' and need to be public methods
     *
     * @return array An array of public methods
     */
    public function getSubscriptions();
}
