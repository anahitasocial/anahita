<?php

/**
 * Enableable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseDomainBehaviorEnableable extends AnDomainBehaviorAbstract
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
            'attributes' => array(
                'enabled' => array(
                    'default' => false,
                    'write_access' => 'private',
                ),
              ),
        ));

        parent::_initialize($config);
    }

    /**
     * Enables the node.
     */
    public function enable()
    {
        $this->enabled = 1;

        return $this;
    }

    /**
     * Disables the node.
     */
    public function disable()
    {
        $this->enabled = 0;

        return $this;
    }
}
