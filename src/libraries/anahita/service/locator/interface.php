<?php
/**
 * @package     Anahita_Service
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
interface AnServiceLocatorInterface
{
    /**
     * Get the classname based on an identifier
     *
     * @param 	object 			An identifier object - [application::]type.package.[.path].name
     * @return 	string|false 	Returns the class on success, returns FALSE on failure
     */
    public function findClass(AnServiceIdentifier $identifier);

    /**
    * Get the path based on an identifier
    *
    * @param  object   An identifier object - [application::]type.package.[.path].name
    * @return string	Returns the path
    */
    public function findPath(AnServiceIdentifier $identifier);

    /**
     * Get the type
     *
     * @return string	Returns the type
     */
    public function getType();
}
