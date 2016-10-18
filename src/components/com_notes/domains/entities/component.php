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
