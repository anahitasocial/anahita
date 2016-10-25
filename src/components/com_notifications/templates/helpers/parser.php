<?php

/**
 * @version		$Id$
 *
 * @category	Com_Notifications
 *
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link     	http://www.GetAnahita.com
 *
 * @author	 	Arash Sanieyan
 */

/**
 * Template Helper.
 *
 * @category   Anahita
 */
class ComNotificationsTemplateHelperParser extends ComStoriesTemplateHelperParser
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
            'filters' => array(
                'com:notifications.template.filter.blockquote',
                'com:notifications.template.filter.link',
            ),
        ));

        parent::_initialize($config);
    }
}
