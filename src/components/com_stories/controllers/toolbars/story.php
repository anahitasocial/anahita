<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Stories Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComStoriesControllerToolbarStory extends ComBaseControllerToolbarDefault
{
    /**
     * Set the list commands.
     */
    public function addListCommands()
    {
        $story = $this->getController()->getItem();

        if ($story->authorize('vote')) {
            $this->getController()->setItem($story->object);
            $this->addCommand('vote');
            $this->getController()->setItem($story);
        }

        if ($story->authorize('add.comment')) {
            $this->getController()->setItem($story->object);
            $this->addCommand('comment');
            $this->getController()->setItem($story);
        }

        if ($story->numOfComments > 10) {
            $this->addCommand('view');
        }

        if ($story->authorize('delete')) {
            $this->addCommand('delete');
        }
    }
}
