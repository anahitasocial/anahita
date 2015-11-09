<?php

/**
 * Default Location Controller Toolbar
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsControllerToolbarDefault extends ComBaseControllerToolbarDefault
{
    /**
     * Called after controller browse.
     *
     * @param KEvent $event
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        if ($this->getController()->canAdd()) {
            $this->addCommand('new');
        }
    }

    /**
     * Called before list commands.
     */
    public function addListCommands()
    {
        
    }
}
