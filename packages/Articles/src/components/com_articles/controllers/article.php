<?php

/**
 * Article Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComArticlesControllerArticle extends ComMediumControllerDefault
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'request' => array(
                'sort' => 'newest',
            ),
            'behaviors' => array(
                'pinnable',
                'coverable',
            ),
        ));
    }

    /**
     * Redirect after edit.
     *
     * (non-PHPdoc)
     *
     * @see ComBaseControllerService::_actionEdit()
     */
    protected function _actionEdit(AnCommandContext $context)
    {
        $result = parent::_actionEdit($context);
        $this->registerCallback('after.edit', array($this, 'redirect'));
    }
    
    /**
     * When a article is added, then create a notification.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionAdd($context)
    {
        $entity = parent::_actionAdd($context);

        if ($entity->owner->isSubscribable()) {
            $notification = $this->createNotification(array(
                'name' => 'article_add',
                'object' => $entity,
                'subscribers' => $entity->owner->subscriberIds->toArray(),
            ))->setType('post', array('new_post' => true));
        }

        return $entity;
    }

    /**
     * Article post action.
     *
     * @param AnCommandContext $context Context parameter
     */
    public function redirect(AnCommandContext $context)
    {
        if ($context->action === 'edit' || $context->action === 'add') {
            $context->response->setRedirect(route($this->getItem()->getURL().'&layout=edit'));
        } else {
            return parent::redirect($context);
        }
    }
}
