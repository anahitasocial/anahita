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
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Parentable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseDomainBehaviorParentable extends AnDomainBehaviorAbstract
{
    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
           'polymorphic' => true,
        ));

        $relationship['polymorphic'] = $config->polymorphic;

        //if parent is set, then set the base parent
        if (isset($config['parent'])) {
            if (strpos($config['parent'], '.') === false) {
                $identifier = clone $config->service_identifier;
                $identifier->path = array('domain','entity');
                $identifier->name = $config['parent'];
            } else {
                $identifier = $this->getService($config['parent']);
            }

            $relationship['parent'] = $identifier;

            unset($config['parent']);

            $config->append(array(
                'aliases' => array($identifier->name => 'parent'),
            ));
        }

        $config->append(array(
            'relationships' => array(
                'parent' => $relationship,
            ),
        ));

        parent::_initialize($config);
    }
}
