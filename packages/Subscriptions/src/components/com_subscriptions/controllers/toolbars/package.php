<?php

/**
 * Actorbar.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsControllerToolbarPackage extends ComSubscriptionsControllerToolbarDefault
{
    /**
     * New button toolbar.
     *
     * @param LibBaseTemplateObject $command The action object
     */
    protected function _commandNew($command)
    {
        $name = $this->getController()->getIdentifier()->name;
        $labels = array();
        $labels[] = strtoupper('com-'.$this->getIdentifier()->package.'-toolbar-'.$name.'-new');
        $labels[] = 'New';
        $label = translate($labels);
        $url = 'option=com_'.$this->getIdentifier()->package.'&view='.$name.'&layout=add';

        $command
        ->append(array('label' => $label))
        ->href(route($url));
    }

    /**
     * Edit Command for an entity.
     *
     * @param LibBaseTemplateObject $command The action object
     */
    protected function _commandEdit($command)
    {
        $entity = $this->getController()->getItem();
        $view = $this->getController()->getView()->getName();

        $layout = pick($command->layout, 'edit');

        $command->append(array('label' => AnTranslator::_('LIB-AN-ACTION-EDIT')))
                ->href($entity->getURL().'&layout='.$layout);

        if (AnInflector::isPlural($view)) {
            $command->setAttribute('data-action', 'edit');
        }
    }
}
