<?php

/**
 * Revision Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPagesControllerToolbarRevision extends ComMediumControllerToolbarDefault
{
    /**
     * Set the toolbar commands.
     */
    public function addToolbarCommands()
    {
        $entity = $this->getController()->getItem();

        $this->addCommand('view');

        if ($entity->owner->authorize('administration')) {
            $this->addCommand('restore');
        }
    }

    /**
     * View command.
     *
     * @param LibBaseTemplateObject $command Command Object
     */
    protected function _commandView($command)
    {
        $entity = $this->getController()->getItem();
        $command->append(array('label' => JText::_('COM-PAGES-PAGE-CURRENT-VERSION')));
        $command->href('option=com_pages&view=page&id='.$entity->parent->id);
    }

    /**
     * Restore command.
     *
     * @param LibBaseTemplateObject $command Command object
     */
    protected function _commandRestore($command)
    {
        $entity = $this->getController()->getItem();
        $command->append(array('label' => JText::_('COM-PAGES-PAGE-REVISION-RESTORE')));
        $command->href('option=com_pages&view=revision&action=restore&id='.$entity->id)
        ->setAttribute('data-trigger', 'Submit');
    }
}
