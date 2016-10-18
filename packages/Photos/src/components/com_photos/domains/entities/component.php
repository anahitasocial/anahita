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
class ComPhotosDomainEntityComponent extends ComMediumDomainEntityComponent
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
        $config->append(array(
            'story_aggregation' => array('photo_add' => 'target'),
            'behaviors' => array(
                    'scopeable' => array('class' => 'ComPhotosDomainEntityPhoto'),
                    'hashtagable' => array('class' => 'ComPhotosDomainEntityPhoto'),
                ),
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
        if ($mode == 'profile') {
            $gadgets->insert('photos', array(
                    'title' => AnTranslator::_('COM-PHOTOS-GADGET-ACTOR-PROFILE'),
                    'url' => 'option=com_photos&view=photos&layout=gadget&oid='.$actor->uniqueAlias,
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_photos&view=photos&oid='.$actor->id,
            ));
        } else {
            $gadgets->insert('photos', array(
                    'title' => AnTranslator::_('COM-PHOTOS-GADGET-DASHBOARD'),
                    'url' => 'option=com_photos&view=photos&layout=gadget&filter=leaders',
                    'action' => AnTranslator::_('LIB-AN-GADGET-VIEW-ALL'),
                    'action_url' => 'option=com_photos&view=photos&filter=leaders',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setComposers($actor, $composers, $mode)
    {
        if ($actor->authorize('action', 'com_photos:photo:add')) {
            $composers->insert('photo-composer', array(
                    'title' => AnTranslator::_('COM-PHOTOS-COMPOSER-PHOTO'),
                    'placeholder' => AnTranslator::_('COM-PHOTOS-PHOTO-ADD'),
                    'url' => 'option=com_photos&view=photo&layout=composer&oid='.$actor->id,
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _setMenuLinks($actor, $menuItems)
    {
        $menuItems->insert('photo-photos', array(
            'title' => AnTranslator::_('COM-PHOTOS-MENU-ITEM-PHOTOS'),
            'url' => 'option=com_photos&view=photos&oid='.$actor->uniqueAlias,
        ));
    }
}
