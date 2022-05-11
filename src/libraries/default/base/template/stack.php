<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseTemplateStack extends AnObjectStack implements AnServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param   object  An optional AnConfig object with configuration options
     * @param   object  A AnServiceServiceInterface object
     * @return LibBaseTemplateStack
     */
    public static function getInstance(AnConfigInterface $config, AnServiceInterface $container)
    {
        // Check if an instance with this identifier already exists or not
        if (! $container->has($config->service_identifier)) {
            //Create the singleton
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }
}
