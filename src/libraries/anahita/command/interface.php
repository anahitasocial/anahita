<?php

/**
 * Command Interface
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @package     AnCommand
 * @link        https://www.GetAnahita.com
 */
interface AnCommandInterface extends AnObjectHandlable
{
    /**
     * Generic Command handler
     *
     * @param 	string 	The command name
     * @param 	object  The command context
     * @return	boolean
     */
    public function execute($name, AnCommandContext $context);

    /**
     * Get the priority of the command
     *
     * @return	integer The command priority
     */
    public function getPriority();
}
