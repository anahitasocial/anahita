<?php

/**
 * Component object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTopicsDomainEntityComponent extends ComMediumDomainEntityComponent
{
    /**
     * {@inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        if ($mode == 'profile') {
            $gadgets->insert('topics-gadget', array(
                    'title' => AnTranslator::_('COM-TOPICS-GADGET-ACTOR-PROFILE'),
                    'url' => 'option=com_topics&view=topics&layout=gadget&oid='.$actor->id,
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_topics&view=topics&oid='.$actor->id,
            ));
        } else {
            $gadgets->insert('topics-gadget', array(
                    'title' => AnTranslator::_('COM-TOPICS-GADGET-ACTOR-DASHBOARD'),
                    'url' => 'option=com_topics&view=topics&layout=gadget&filter=leaders',
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_topics&view=topics&filter=leaders',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setComposers($actor, $composers, $mode)
    {
        if ($actor->authorize('action', 'com_topics:topic:add')) {
            $composers->insert('photos-composer', array(
                    'title' => AnTranslator::_('COM-TOPICS-COMPOSER-TOPIC'),
                    'placeholder' => AnTranslator::_('COM-TOPICS-TOPIC-ADD'),
                    'url' => 'option=com_topics&view=topic&layout=composer&oid='.$actor->id,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setMenuLinks($actor, $menuItems)
    {
        $menuItems->insert('topics-topics', array(
            'title' => AnTranslator::_('COM-TOPICS-MENU-ITEM-TOPICS'),
            'url' => 'option=com_topics&view=topics&oid='.$actor->uniqueAlias,
        ));
    }
}
