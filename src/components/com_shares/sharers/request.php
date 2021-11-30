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
 * @link       http://www.Anahita.io
 */

/**
 * Share Request. 
 * 
 * A request object for an actor to share an object on another actor's profile  
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComSharesSharerRequest extends AnObject
{
    /**
     * The sharer actor.
     * 
     * @var ComActorsDomainEntityActor
     */
    public $sharer;

    /**
     * The target actor.
     *
     * @var ComActorsDomainEntityActor
     */
    public $target;

    /**
     * The object to share.
     *
     * @var mixed
     */
    public $object;

    /**
     * Creates a share request object.
     * 
     * @param ComActorsDomainEntityActor $sharer The actor who's sharing
     * @param ComActorsDomainEntityActor $target The target actor
     * @param mixed                      $object
     */
    public function __construct(AnConfig $config)
    {
        $this->sharer = $config->sharer;
        $this->target = $config->target;
        $this->object = $config->object;
    }
}
