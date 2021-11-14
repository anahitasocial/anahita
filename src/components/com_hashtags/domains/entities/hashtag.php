<?php

/**
 * A hashtag.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
final class ComHashtagsDomainEntityHashtag extends ComTagsDomainEntityNode
{
    /*
     * hashtag regex pattern
     */
    const PATTERN_HASHTAG = '/(?![^<]*>)(?<=\W|^)#([^\d_\s\W][\p{L}\d\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}]{2,})/u';

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
                    'format' => 'string',
                    'length' => array(
                        'max' => 100,
                    ),
                ),
                'alias' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'format' => 'slug'
                ),
            ),
            'aliases' => array(
                'title' => 'name',
            ),
            'behaviors' => to_hash(array(
                'modifiable',
            )),
        ));

        parent::_initialize($config);
    }
    
    /**
     * Override the name setter to set the alias at the same time.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->set('name', $name);
        $this->alias = $name;
    }
    
    /**
     * Returns the node URL.
     *
     * @return string
     */
    public function getURL()
    {
        if (! isset($this->_url)) {
            $this->_url = 'option='.$this->component.'&view='.$this->getIdentifier()->name;

            if ($this->id) {
                $this->_url .= '&id='.$this->id;
            }

            if ($this->alias) {
                $this->_url .= '&alias='.strtolower($this->alias);
            }
        }

        return $this->_url;
    }

    /**
     * Update stats.
     */
    public function resetStats(array $hashtags)
    {
        foreach ($hashtags as $hashtag) {
            $hashtag->timestamp();
        }
    }
}
