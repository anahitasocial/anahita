<?php

/**
 * Location Controller
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsControllerLocation extends ComTagsControllerDefault
{
    /**
     * Browse Service
     *
     * @param KCommandContext $context
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        $entities = parent::_actionBrowse($context);
        $edgeType = 'ComTagsDomainEntityTag,ComLocationsDomainEntityTag,com:locations.domain.entity.tag';
        $entities->where('edge.type', '=', $edgeType)->group('location.id');

        return $entities;
    }

    /**
     * Read Service
     *
     * @param KCommandContext $context
     */
    protected function _actionRead(KCommandContext $context)
    {
        $entity = parent::_actionRead($context);

        if ($this->getView()->getLayout() == 'selector') {
            $this->registerCallback('after.read', array($this, 'fetchLocatable'));
        }

        return $entity;
    }

    /**
    *  Method to fetch the locatable object
    *
    *
    */
    public function fetchLocatable(KCommandContext $context)
    {
        $this->locatable = KService::get('repos:nodes.node')
                           ->getQuery()
                           ->disableChain()
                           ->id($this->locatable_id)
                           ->fetch();

        if(!$this->locatable) {
            throw new LibBaseControllerExceptionNotFound('Locatable object does not exist');
        }

        if(!$this->locatable->getRepository()->hasBehavior('geolocatable')) {
            throw new LibBaseControllerExceptionBadRequest('Object does not have locatable behavior');
        }

        return $this->locatable;
    }
}
