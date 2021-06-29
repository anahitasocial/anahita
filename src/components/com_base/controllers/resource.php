<?php

/**
 * Restful Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerResource extends LibBaseControllerResource
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->getService('anahita:language')->load($config->language);

        $this->_state->viewer = $config->viewer;
    }

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
            'viewer' => $this->getService('com:people.viewer'),
            'language' => 'com_'.$this->getIdentifier()->package,
            'behaviors' => to_hash('com:application.controller.behavior.message'),
        ));

        parent::_initialize($config);
    }

    /**
     * Sets the context response.
     *
     * @return AnCommandContext
     */
    public function getCommandContext()
    {
        $context = parent::getCommandContext();
        $context->viewer = $this->_state->viewer;

        return $context;
    }
}
