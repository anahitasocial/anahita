<?php

/**
 * Comment Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseControllerToolbarComment extends ComBaseControllerToolbarDefault
{
    /**
     * Called before list commands.
     */
    public function addListCommands()
    {
        $comment = $this->getController()->getItem();

        if ($comment->authorize('vote')) {
            $this->addCommand('vote');
        }

        if ($comment->authorize('edit')) {
            $this->addCommand('editcomment');
        }

        if ($comment->authorize('delete')) {
            $this->addCommand('deletecomment');
        }
    }
}
