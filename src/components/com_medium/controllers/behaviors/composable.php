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
class ComMediumControllerBehaviorComposable extends ComComposerControllerBehaviorComposable
{
    /**
     * Renders a story after an entity is created through composer.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _afterControllerAdd(KCommandContext $context)
    {
        $data = $context->data;

        if ($data->composed && $data->story) {
            if ($context->response->isRedirect()) {
                $context->response->setStatus(KHttpResponse::OK);
                $context->response->removeHeader('Location');
            }

            $content = $this->_renderComposedStory($data->story);
            $context->response->setContent($content);
        }
    }
}
