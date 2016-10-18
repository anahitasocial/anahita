<?php

/**
 * Default Medium Controller Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComMediumControllerToolbarDefault extends ComBaseControllerToolbarDefault
{
    /**
     * Before Controller _actionRead is executed.
     *
     * @param KEvent $event
     */
    public function onBeforeControllerRead(KEvent $event)
    {
        if ($this->getController()->getItem()) {
            $this->addToolbarCommands();
        }
    }

    /**
     * Called after controller browse.
     *
     * @param KEvent $event
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        $filter = $this->getController()->filter;

        if ($this->getController()->canAdd() && $filter != 'leaders') {
            $this->addCommand('new');
        }
    }

    /**
     * Set the toolbar commands.
     */
    public function addToolbarCommands()
    {
        $entity = $this->getController()->getItem();

        if ($entity->authorize('vote')) {
            $this->addCommand('vote');
        }

        if ($entity->authorize('subscribe') || ($entity->isSubscribable() && $entity->subscribed(get_viewer()))) {
            $this->addCommand('subscribe');
        }

        if ($entity->authorize('edit')) {
            $this->addCommand('edit');
        }

        if ($entity->isOwnable() && $entity->owner->authorize('administration')) {
            $this->addAdministrationCommands();
        }

        if ($entity->authorize('delete')) {
            $this->addCommand('delete');
        }
    }

    /**
     * Called before list commands.
     */
     public function addListCommands()
     {
        $entity = $this->getController()->getItem();

        if ($entity->authorize('vote')) {
            $this->addCommand('vote');
        }

        if ($entity->authorize('edit')) {
            $this->addCommand('edit');
        }

        if ($entity->authorize('delete')) {
            $this->addCommand('delete');
        }
    }

    /**
     * Add Admin Commands for an entity.
     */
    public function addAdministrationCommands()
    {
        $entity = $this->getController()->getItem();

        if ($entity->isOwnable() && $entity->owner->authorize('administration')) {
            if ($entity->isEnablable()) {
                $this->addCommand('enable');
            }

            if ($entity->isCommentable()) {
                $this->addCommand('commentstatus');
            }
        }
    }

    /**
     * Delete Command for an entity.
     *
     * @param LibBaseTemplateObject $command The action object
     */
    protected function _commandDelete($command)
    {
        $entity = $this->getController()->getItem();

        $command->append(array('label' => AnTranslator::_('LIB-AN-ACTION-DELETE')))
        ->href(route($entity->getURL()))
        ->setAttribute('data-action', 'delete')
        ->setAttribute('data-redirect', route($entity->owner->getURL()))
        ->class('action-delete');
    }

    /**
     * New button toolbar.
     *
     * @param LibBaseTemplateObject $command The action object
     */
    protected function _commandNew($command)
    {
        $actor = $this->getController()->actor;
        $name = $this->getController()->getIdentifier()->name;
        $labels = array();
        $labels[] = strtoupper('com-'.$this->getIdentifier()->package.'-toolbar-'.$name.'-new');
        $labels[] = 'New';
        $label = translate($labels);
        $url = 'option=com_'.$this->getIdentifier()->package.'&view='.$name.'&oid='.$actor->id.'&layout=add';

        $command
        ->append(array('label' => $label))
        ->href(route($url));
    }

    /**
     * Customize the sticky command.
     *
     * @param LibBaseTemplateObject $command Command Object
     */
    protected function _commandPin($command)
    {
        $entity = $this->getController()->getItem();

        $label = ($entity->pinned) ? AnTranslator::_('LIB-AN-ACTION-UNPIN') : AnTranslator::_('LIB-AN-ACTION-PIN');

        $command
        ->append(array('label' => $label))
        ->href($entity->getURL().'&action='.($entity->pinned ? 'unpin' : 'pin'))
        ->setAttribute('data-trigger', 'PostLink');
    }
}
