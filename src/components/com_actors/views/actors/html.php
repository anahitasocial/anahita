<?php

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
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            // 'list_item_view' => AnInflector::singularize($this->getIdentifier()->package),
            'template_paths' => array(
                dirname(__FILE__).'/html',
                // ANPATH_THEMES.'/'.$this->getService('application')->getTemplate().'/html/com_actors/actors'
            ),
        ));

        parent::_initialize($config);
    }
}
