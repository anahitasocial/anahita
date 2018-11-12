<?php

class ComLocationsControllerSetting extends ComBaseControllerResource
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
            'behaviors' => array(
                'ownable',
            ),
        ));

        parent::_initialize($config);
    }
}
