<?php

/**
 * Enablable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsControllerBehaviorEnablable extends ComBaseControllerBehaviorEnablable
{
    private $_viewer = null;

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        parent::_initialize($config);
        $this->_viewer = get_viewer();
    }

    /**
     * Authorize enable.
     * 
     * @return bool
     */
    public function canEnable()
    {
        return $this->getItem()->authorize('changeenabled');
    }

    /**
     * Authorize disable.
     * 
     * @return bool
     */
    public function canDisable()
    {
        return $this->getItem()->authorize('changeenabled');
    }
}
