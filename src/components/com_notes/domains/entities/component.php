<?php

/**
 * Component object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComNotesDomainEntityComponent extends ComMediumDomainEntityComponent
{
    /**
     * Return max.
     *
     * @return int
     */
    public function getPriority()
    {
        return -PHP_INT_MAX;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        if ($mode == 'profile') {
            $gadgets->insert('notes', array(
                    'title' => AnTranslator::_('COM-NOTES-GADGET-ACTOR-PROFILE'),
                    'url' => 'option=com_notes&view=notes&layout=gadget&oid='.$actor->id,
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_notes&view=notes&oid='.$actor->id,
            ));
        } else {
            $gadgets->insert('notes', array(
                    'title' => AnTranslator::_('COM-NOTES-GADGET-ACTOR-DASHBOARD'),
                    'url' => 'option=com_notes&view=notes&layout=gadget&filter=leaders',
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_notes&view=notes&filter=leaders',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setComposers($actor, $composers, $mode)
    {
        if ($actor->authorize('action', 'com_notes:note:add')) {
            $composers->insert('notes', array(
                    'title' => AnTranslator::_('COM-NOTES-COMPOSER-NOTE'),
                    'placeholder' => AnTranslator::_('COM-NOTES-COMPOSER-PLACEHOLDER'),
                    'url' => 'option=com_notes&layout=composer&view=note&oid='.$actor->id,
            ));
        }
    }
}
