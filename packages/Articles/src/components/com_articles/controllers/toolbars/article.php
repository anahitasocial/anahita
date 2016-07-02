<?php

/**
 * Article Toolbar.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComArticlesControllerToolbarArticle extends ComMediumControllerToolbarDefault
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
