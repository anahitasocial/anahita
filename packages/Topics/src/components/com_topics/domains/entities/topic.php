<?php

/**
 * Topic Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComTopicsDomainEntityTopic extends ComMediumDomainEntityMedium
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
            'attributes' => array(
                'name' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'body' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'max' => 40000,
                    )
                ),
            ),
            'behaviors' => array(
                'pinnable',
                'hittable',
            ),
        ));

        return parent::_initialize($config);
    }
}
