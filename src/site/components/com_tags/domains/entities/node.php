<?php

/**
 * Tag entity
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTagsDomainEntityNode extends ComBaseDomainEntityNode
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
            'inheritance' => array('ignore' => __CLASS__),
            'behaviors' => array(
              'privatable'
            ),
            'attributes' => array(
                'enabled' => array('default' => 1)
             ),
        ));

        parent::_initialize($config);
    }
}
