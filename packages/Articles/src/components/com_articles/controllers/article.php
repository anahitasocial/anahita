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
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        parent::_initialize($config);

        $config->append(array(
            'request' => array(
                'sort' => 'newest',
            ),
            'behaviors' => array(
                'pinnable',
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
    protected function _actionEdit(KCommandContext $context)
    {
        $result = parent::_actionEdit($context);
        $this->registerCallback('after.edit', array($this, 'redirect'));
    }

    /**
     * Article post action.
     *
     * @param KCommandContext $context Context parameter
     */
    public function redirect(KCommandContext $context)
    {
        if ($context->action == 'edit' || $context->action == 'add') {
            $context->response->setRedirect(route($this->getItem()->getURL().'&layout=edit'));
        } else {
            return parent::redirect($context);
        }
    }
}
