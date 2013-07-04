<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Component object
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPhotosDomainEntityComponent extends ComMediumDomainEntityComponent
{
    /**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'story_aggregation' => array('photo_add'=>'target')
        ));

        parent::_initialize($config);
    }   
     
	/**
	 * @{inheritdoc}
	 */
	protected function _setGadgets($actor, $gadgets, $mode)
	{
		if ( $mode == 'profile' )
			$gadgets->insert('photos-gadget', array(
					'title' 		=> JText::_('COM-PHOTOS-GADGET-ACTOR-PROFILE'),
					'url'   	    => 'option=com_photos&view=photos&layout=gadget&oid='.$actor->uniqueAlias,
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'   	=> 'option=com_photos&view=photos&oid='.$actor->id
			));
		else
			$gadgets->insert('photos-gadget', array(
					'title' 	    => JText::_('COM-PHOTOS-GADGET-DASHBOARD'),
					'url'   	    => 'option=com_photos&view=photos&filter=leaders&layout=gadget&oid='.$actor->uniqueAlias,
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url' 	=> 'option=com_photos&view=photos&filter=leaders&oid='.$actor->uniqueAlias,
			));
	}
	
	/**
	 * @{inheritdoc}
	 */
	protected function _setComposers($actor, $composers, $mode)
	{
		if ( $actor->authorize('action','com_photos:photo:add') )
			$composers->insert('photo-composer', array(
					'title'	       => JText::_('COM-PHOTOS-COMPOSER-PHOTO'),
					'placeholder'  => JText::_('COM-PHOTOS-PHOTO-ADD'),
					'url'      => 'option=com_photos&view=photo&layout=composer&oid='.$actor->id,
			));
	}	
}
