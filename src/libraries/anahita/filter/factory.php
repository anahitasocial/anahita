<?php
/**
* @version		$Id: factory.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Anahita_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Filter Factory
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Anahita_Filter
 */
class AnFilterFactory extends AnObject implements AnServiceInstantiatable
{
	/**
     * Force creation of a singleton
     *
     * @param 	object 	An optional AnConfig object with configuration options
     * @param 	object	A AnServiceInterface object
     * @return AnFilterFactory
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
       // Check if an instance with this identifier already exists or not
        if (!$container->has($config->service_identifier))
        {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

	/**
	 * Factory method for AnFilterInterface classes.
	 *
	 * @param	string 	Filter indentifier
	 * @param 	object 	An optional AnConfig object with configuration options
	 * @return AnFilterAbstract
	 */
	public function instantiate($identifier, $config = array())
	{
		//Get the filter(s) we need to create
		$filters = (array) $identifier;

		//Create the filter chain
		$filter = array_shift($filters);
		$filter = $this->_createFilter($filter, $config);

		foreach($filters as $name) {
			$filter->addFilter(self::_createFilter($name, $config));
		}

		return $filter;
	}

	/**
	 * Create a filter based on it's name
	 *
	 * If the filter is not an identifier this function will create it directly
	 * instead of going through the AnService identification process.
	 *
	 * @param 	string	Filter identifier
	 * @throws	AnFilterException	When the filter could not be found
	 * @return  AnFilterInterface
	 */
	protected function _createFilter($filter, $config)
	{
		try
		{
			if(is_string($filter) && strpos($filter, '.') === false ) {
				$filter = 'com:default.filter.'.trim($filter);
			}

			$filter = $this->getService($filter, $config);

		} catch(AnServiceServiceException $e) {
			throw new AnFilterException('Invalid filter: '.$filter);
		}

	    //Check the filter interface
		if(!($filter instanceof AnFilterInterface))
		{
			$identifier = $filter->getIdentifier();
			throw new AnFilterException("Filter $identifier does not implement AnFilterInterface");
		}

		return $filter;
	}
}