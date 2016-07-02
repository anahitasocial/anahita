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
 * @link       http://www.GetAnahita.com
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
            $url = $comment->getURL().'&comment[layout]=form';

            if ($this->getController()->editor) {
                $url = $url.'&comment[editor]=1';
            }

            $this->addCommand('editcomment', JText::_('LIB-AN-ACTION-EDIT'))
                ->getCommand('editcomment')
                ->href(JRoute::_($url))
                ->class('action-edit');
        }

        if ($comment->authorize('delete')) {
            $this->addCommand('deletecomment', JText::_('LIB-AN-ACTION-DELETE'))
                ->getCommand('deletecomment')
                ->href(JRoute::_($comment->getURL()))
                ->class('action-delete');
        }
    }
}
