<?php

/**
 * Anahita Actor Profile Plugin.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class PlgProfileAbstract extends PlgAnahitaDefault
{
    /**
     * Called on the saving an actor profile information.
     *
     * @param KEvent $event Event parameter
     */
    abstract public function onSave(KEvent $event);

    /**
     * Called on displaying profile information.
     *
     * @param KEvent $event Event parameter
     */
    abstract public function onDisplay(KEvent $event);

    /**
     * Called on displaying profile information in a form layout.
     *
     * @param KEvent $event Event parameter
     */
    abstract public function onEdit(KEvent $event);
}
