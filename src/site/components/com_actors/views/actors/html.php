<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Default Actors View.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsViewActorsHtml extends ComBaseViewHtml
{
    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'list_item_view' => KInflector::singularize($this->getName()),
            'template_paths' => array(dirname(__FILE__).'/html'),
        ));

        parent::_initialize($config);

        $config->append(array(
            'template_paths' => array(JPATH_THEMES.'/'.JFactory::getApplication()->getTemplate().'/html/com_actors/actors'),
        ));
    }
}
