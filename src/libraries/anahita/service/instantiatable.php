<?php
/**
 * @package     Anahita_Service
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
interface AnServiceInstantiatable
{
    /**
     * Get the object identifier
     *
     * @param 	object 	An optional AnConfig object with configuration options
     * @param 	object	A AnServiceInterface object
     * @return  object
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container);
}
