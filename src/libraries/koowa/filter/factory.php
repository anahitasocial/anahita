<?php
/**
* @version		$Id: factory.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Filter Factory
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterFactory extends KObject implements KServiceInstantiatable
{
	/**
     * Force creation of a singleton
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @param 	object	A KServiceInterface object
     * @return KFilterFactory
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
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
	 * Factory method for KFilterInterface classes.
	 *
	 * @param	string 	Filter indentifier
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return KFilterAbstract
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
	 * instead of going through the KService identification process.
	 *
	 * @param 	string	Filter identifier
	 * @throws	KFilterException	When the filter could not be found
	 * @return  KFilterInterface
	 */
	protected function _createFilter($filter, $config)
	{
		try
		{
			if(is_string($filter) && strpos($filter, '.') === false ) {
				$filter = 'com:default.filter.'.trim($filter);
			}

			$filter = $this->getService($filter, $config);

		} catch(KServiceServiceException $e) {
			throw new KFilterException('Invalid filter: '.$filter);
		}

	    //Check the filter interface
		if(!($filter instanceof KFilterInterface))
		{
			$identifier = $filter->getIdentifier();
			throw new KFilterException("Filter $identifier does not implement KFilterInterface");
		}

		return $filter;
	}
}