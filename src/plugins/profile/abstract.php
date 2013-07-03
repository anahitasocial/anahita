<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plg_Profile
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Anahita Actor Profile Plugin 
 * 
 * @category   Anahita
 * @package    Plg_Profile
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class PlgProfileAbstract extends PlgKoowaDefault
{
    /**
     * Called on the saving an actor profile information
     *
     * @param  KEvent $event Event parameter
     * 
     * @return void
     */
    abstract public function onSave(KEvent $event);

    /**
     * Called on displaying profile information
     *
     * @param  KEvent $event Event parameter
     * 
     * @return void
     */
    abstract public function onDisplay(KEvent $event);
    
    /**
     * Called on displaying profile information in a form layout
     *
     * @param  KEvent $event Event parameter
     *
     * @return void
     */
    abstract public function onEdit(KEvent $event);   
}