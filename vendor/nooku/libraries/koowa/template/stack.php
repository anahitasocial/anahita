<?php
/**
 * @version     $Id: template.php 2026 2010-05-14 16:47:03Z johanjanssens $
 * @package     Koowa_Template
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Stack Class
 *
 * The stack is implemented as a signleton. After instantiation the object can
 * be accessed using koowa:template.stack identifier.
 *
 * @author     Johan Janssens <johan@nooku.org>
 * @package    Koowa_Template
 */
class KTemplateStack extends KObjectStack implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param   object  An optional KConfig object with configuration options
     * @param   object  A KServiceServiceInterface object
     * @return KTemplateStack
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
}
