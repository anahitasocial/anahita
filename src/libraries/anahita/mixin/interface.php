<?php

/**
 * Mixes a chain of command behaviour into a class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://www.GetAnahita.com
 * @package     AnMixin
 */
interface AnMixinInterface extends KObjectHandlable
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
     * @return AnMixinInterface
     */
    public function setMixer($mixer);
}