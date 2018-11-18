<?php
/**
 * @package     Anahita_Loader
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
abstract class AnLoaderAdapterAbstract implements AnLoaderAdapterInterface
{
    /**
     * The adapter type
     *
     * @var string
     */
    protected $_type = '';

    /**
     * The basepath
     *
     * @var string
     */
    protected $_basepath = '';

    /**
     * The class prefiex
     *
     * @var string
     */
    protected $_prefix = '';

    /**
     * Constructor.
     *
     * @param  array  An optional array with configuration options.
     */
    public function __construct($config = array())
    {
        if (isset($config['basepath'])) {
            $this->_basepath = $config['basepath'];
        }
    }

    /**
     * Get the type
     *
     * @return string	Returns the type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get the base path
     *
     * @return string	Returns the base path
     */
    public function getBasepath()
    {
        return $this->_basepath;
    }

    /**
     * Get the class prefix
     *
     * @return string	Returns the class prefix
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }
}
