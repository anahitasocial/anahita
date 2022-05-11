<?php

/**
 * Composable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
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

        $controller->toolbar = $controller->getToolbar('story');
        
        $format = $this->_mixer->getRequest()->getFormat();
        $controller->getRequest()->setFormat($format);

        return  $controller->getView()->display();
    }
}
