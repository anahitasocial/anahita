<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Sharer Adapter Interface.
 * 
 * Any share adapter that ones to share an object must implement this 
 * interface
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
interface ComSharesSharerInterface extends KObjectHandlable, KObjectServiceable
{
    /**
     * Return a boolean value whether it can share a request or not.
     * 
     * @param ComSharesSharerRequest $request
     * 
     * @return bool
     */
    public function canShareRequest(ComSharesSharerRequest $request);

    /**
     * Performs a share for a share request.
     * 
     * @param mixed $object
     */
    public function shareRequest(ComSharesSharerRequest $request);
}
