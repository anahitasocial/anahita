<?php
/**
 * @version     $Id: component.php 1263 2009-10-15 00:20:35Z johan $
 * @package     Koowa_Loader
 * @subpackage  Adapter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Abstract Loader Adapter
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Loader
 * @subpackage  Adapter
 */
abstract class KLoaderAdapterAbstract implements KLoaderAdapterInterface
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
    public function __construct( $config = array())
    {
        if(isset($config['basepath'])) {
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