<?php

/**
 * Comment Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseControllerComment extends ComBaseControllerService
{
    /**
     * Constructor.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->registerCallback(array(
            'after.voteup',
            'after.votedown', ),
            array($this, 'getvoters'));
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'parentable',
                'votable',
                'com:hashtags.controller.behavior.hashtaggable',
                'com:people.controller.behavior.mentionable',
            ),
        ));

        AnHelperArray::unsetValues($config->behaviors, 'verifiable');

        parent::_initialize($config);
    }

    /**
     * Creates a comment.
     *
     * @param AnCommandContext $context
     *
     * @return AnDomainEntityAbstract
     */
    protected function _actionAdd($context)
    {
        $data = $context->data;
        $body = $data->body;

        return $this->setItem($this->parent->addComment($body))->getItem();
    }

    /**
     * Delete a Comment belonging to a node.
     *
     * @param AnCommandContext $context post data
     *
     * @return bool
     */
    protected function _actionDelete($context)
    {
        $this->getItem()->delete();
    }

    /**
     * Edit a comment.
     *
     * @param AnCommandContext $context
     */
    protected function _actionEdit($context)
    {
        $data = $context->data;
        $this->getItem()->body = $data->body;

        return $this->getItem();
    }

    /**
     * Sets the default view to the comment views.
     *
     * @param stirng $view
     *
     * @return ComBaseControllerComment
     */
    public function setView($view)
    {
        parent::setView($view);

        if (!$this->_view instanceof LibBaseViewAbstract) {
            $view = AnInflector::isPlural($view) ? 'comments' : 'comment';
            $defaults[] = 'ComBaseView'.ucfirst($view).ucfirst($this->_view->name);
            register_default(array(
                'identifier' => $this->_view,
                'default' => $defaults, ));
        }

        return $this;
    }

    /**
     * Returns whether a comment can be added.
     *
     * @return bool
     */
    public function canAdd()
    {
        return $this->parent && $this->parent->authorize('access') && $this->parent->authorize('add.comment');
    }

    /**
     * Returns whether a comment can be added.
     *
     * @return bool
     */
    public function canEdit()
    {
        return $this->getItem() && $this->getItem()->authorize('edit');
    }

    /**
     * Returns whether a comment can be added.
     *
     * @return bool
     */
    public function canDelete()
    {
        return  $this->getItem() && $this->getItem()->authorize('delete');
    }
}
