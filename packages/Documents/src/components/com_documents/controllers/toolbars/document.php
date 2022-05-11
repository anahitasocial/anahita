<?php

/**
 * Document Toolbar.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComDocumentsControllerToolbarDocument extends ComMediumControllerToolbarDefault
{
    /**
     * Add Admin Commands for an entity.
     */
    public function addAdministrationCommands()
    {
        $this->addCommand('pin');
        parent::addAdministrationCommands();
    }
}
