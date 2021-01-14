<?php

/**
 * Component object.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComDocumentsDomainEntityComponent extends ComMediumDomainEntityComponent
{
    /**
     * {@inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        if ($mode == 'profile') {
            $gadgets->insert('documents', array(
                    'title' => AnTranslator::_('COM-DOCUMENTS-GADGET-ACTOR-PROFILE'),
                    'url' => 'option=com_documents&view=documents&layout=gadget&oid='.$actor->id,
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_documents&view=documents&oid='.$actor->id,
            ));
        } else {
            $gadgets->insert('documents', array(
                    'title' => AnTranslator::_('COM-DOCUMENTS-GADGET-ACTOR-DASHBOARD'),
                    'url' => 'option=com_documents&view=documents&layout=gadget&filter=leaders',
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_documents&view=documents&filter=leaders',
            ));
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _setComposers($actor, $composers, $mode)
    {
        if ($actor->authorize('action', 'com_documents:document:add')) {
            $composers->insert('documents', array(
                    'title' => AnTranslator::_('COM-DOCUMENTS-COMPOSER-DOCUMENT'),
                    'placeholder' => AnTranslator::_('COM-DOCUMENTS-DOCUMENT-ADD'),
                    'url' => 'option=com_documents&view=document&layout=composer&oid='.$actor->id,
            ));
        }
    }
}
