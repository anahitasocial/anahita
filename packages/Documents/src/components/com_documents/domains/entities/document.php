<?php

/**
 * Document Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       https://www.GetAnahita.com
 */
class ComDocumentsDomainEntityDocument extends ComMediumDomainEntityMedium
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
                        'max' => 5000,
                    )
                ),
            ),
            'behaviors' => array(
                'fileable',
            ),
        ));

        parent::_initialize($config);
    }
}
