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
class ComMediumControllerBehaviorComposable extends ComComposerControllerBehaviorComposable
{
    /**
     * Renders a story after an entity is created through composer.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _afterControllerAdd(AnCommandContext $context)
    {
        $data = $context->data;

        if ($data->composed && $data->story) {
            if ($context->response->isRedirect()) {
                $context->response->setStatus(AnHttpResponse::OK);
                $context->response->removeHeader('Location');
            }

            $content = $this->_renderComposedStory($data->story);
            $context->response->setContent($content);
        }
    }
}
