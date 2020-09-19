<?php

/**
 * Photo Entity.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
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
            'behaviors' => array(
                'fileable',
            ),
        ));

        parent::_initialize($config);
    }
    
    protected function _toData()
    {     
        $data = array();
        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['mimeType'] = $this->mimeType;
        return $data; 
    }
}
