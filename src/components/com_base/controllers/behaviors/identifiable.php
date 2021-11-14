<?php

/**
 * Identifiable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.Anahita.io
 */
class ComBaseControllerBehaviorIdentifiable extends LibBaseControllerBehaviorIdentifiable
{
    /**
     * Fetches an entity.
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return AnDomainEntityAbstract The identified entity
     */
    public function fetchEntity(AnCommandContext $context)
    {
        $entity = parent::fetchEntity($context);

        //set the entity owner as the context actor of the controller
        if ($entity && $this->getRepository()->isOwnable() && $this->isOwnable()) {
            $this->setActor($entity->owner);
        }

        return $entity;
    }
}
