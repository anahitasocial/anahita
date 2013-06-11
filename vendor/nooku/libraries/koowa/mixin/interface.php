<?php
/**
 * @version     $Id: interface.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Mixin
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Mixes a chain of command behaviour into a class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Mixin
 */
interface KMixinInterface extends KObjectHandlable
{
    /**
     * Get the methods that are available for mixin.
     *
     * @return array An array of methods
     */
    public function getMixableMethods();

	/**
     * Get the mixer object
     *
     * @return object 	The mixer object
     */
    public function getMixer();

    /**
     * Set the mixer object
     *
     * @param object The mixer object
     * @return KMixinInterface
     */
    public function setMixer($mixer);
}