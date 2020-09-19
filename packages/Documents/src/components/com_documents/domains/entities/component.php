<?php

/**
 * Component object.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComDocumentsDomainEntityComponent extends ComMediumDomainEntityComponent
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
            'story_aggregation' => array('document_add' => 'target'),
            'behaviors' => array(
                'scopeable' => array('class' => 'ComDocumentsDomainEntityDocument'),
                'hashtaggable' => array('class' => 'ComDocumentsDomainEntityDocument'),
            ),
        ));

        parent::_initialize($config);
    }
}
