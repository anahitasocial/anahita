<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnFilterTidy extends AnFilterAbstract
{
    /**
     * A tidy object
     *
     * @var object
     */
    protected $_tidy = null;

    /**
     * The input/output encoding
     *
     * @var string
     */
    protected $_encoding = 'utf8';

    /**
     * The tidy configuration
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Constructor
     *
     * @param  object  An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_encdoing = $config->encoding;
        $this->_config   = AnConfig::unbox($config->config);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional AnConfig object with configuration options
     * @return  void
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'encoding'      => 'utf8',
            'config'        =>  array(
                    'clean'                       => true,
                    'drop-proprietary-attributes' => true,
                    'output-html'                 => true,
                    'show-body-only'              => true,
                    'bare'                        => true,
                    'wrap'                        => 0,
                    'word-2000'                   => true,
                )
            ));

        parent::_initialize($config);
    }

    /**
     * Validate a variable
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the variable is valid
     */
    protected function _validate($value)
    {
        return (is_string($value));
    }

    /**
     * Sanitize a variable
     *
     * @param   scalar  Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        //Tidy is not installed, return the input
        if ($tidy = $this->getTidy($value)) {
            if ($tidy->cleanRepair()) {
                $value = (string) $tidy;
            }
        }

        return $value;
    }

    /**
     * Gets a Tidy object
     *
     * @param string    The data to be parsed.
     */
    public function getTidy($string)
    {
        if (class_exists('Tidy')) {
            if (!$this->_tidy) {
                $this->_tidy = new Tidy();
            }

            $this->_tidy->parseString($string, $this->_config, $this->_encoding);
        }

        return $this->_tidy;
    }
}
