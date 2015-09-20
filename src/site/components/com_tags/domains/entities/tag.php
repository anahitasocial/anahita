<?php

/**
 * Hashtag association
 *
 * @category   Anahita
 * @package    Com_Tags
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComTagsDomainEntityTag extends ComBaseDomainEntityEdge
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
        	'inheritance' => array('abstract'=>$this->getIdentifier()->classname === __CLASS__),
        	'aliases' => array(
                'tag' => 'nodeA',
        		'tagable' => 'nodeB'
            )
        ));

        parent::_initialize($config);
    }
}
