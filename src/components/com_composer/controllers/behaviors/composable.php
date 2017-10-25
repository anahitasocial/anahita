<?php

/**
 * Composable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComComposerControllerBehaviorComposable extends AnControllerBehaviorAbstract
{
    /**
     * Convienet method to render a story.
     *
     * @return string
     */
    protected function _renderComposedStory($story)
    {
        $controller = $this->getService('com://site/stories.controller.story')
                            ->layout('list')
                            ->setItem($story);

        //manually set the toolbar
        $controller->toolbar = $controller->getToolbar('story');

        return  $controller->getView()->display();
    }
}
