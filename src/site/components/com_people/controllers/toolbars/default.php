<?php

/**
 * Default Actor Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerToolbarDefault extends ComActorsControllerToolbarDefault
{
    /**
     * Called after controller browse.
     *
     * @param KEvent $event
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        if (get_viewer()->admin()) {
            $this->addCommand('new');
        }
    }
}
