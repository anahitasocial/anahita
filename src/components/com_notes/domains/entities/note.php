<?php

/**
 * Post entity is the most basic form of post. It's a simple text.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComNotesDomainEntityNote extends ComMediumDomainEntityMedium
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
                'body' => array(
                  'required' => AnDomain::VALUE_NOT_EMPTY,
                  'length' => array(
                      'max' => 280,
                  )
                ),
            ),
        ));

        parent::_initialize($config);
    }
}
