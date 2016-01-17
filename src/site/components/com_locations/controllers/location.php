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
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if ($this->locatable_id) {
            $this->registerCallback(
                array('before.browse', 'before.read'),
                array($this, 'fetchLocatable')
            );
        }
    }

    /**
     * Browse Service
     * @todo move all queries to the query class
     *
     * @param KCommandContext $context
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        $keyword = ($this->q) ? $this->getService('anahita:filter.term')->sanitize($this->q) : '';

        if ($this->locatable) {

          if (in_array($this->getView()->getLayout(), array('selector', 'list_selector'))) {

              $query = $this->getService('repos:locations.location')->getQuery();
              $excludeIds = AnHelperArray::collect($this->locatable->locations, 'id');

              if (count($excludeIds)) {
                $query->where('location.id', 'NOT IN', $excludeIds);
              }

              if ($keyword != '') {
                  $query->keyword = $keyword;
              }

          } else {
              $query = $this->locatable->locations;
          }

          $query->order('name');

        } elseif ($keyword != '') {
            $query = $this->getService('repos:locations.location')->getQuery();
            $query->keyword = $keyword;
        } else {
          $entities = parent::_actionBrowse($context);
          $query = $entities->getQuery();
          $edgeType = 'ComTagsDomainEntityTag,ComLocationsDomainEntityTag,com:locations.domain.entity.tag';
          $query->where('edge.type', '=', $edgeType)->group('location.id');
        }

        $query->limit($this->limit, $this->offset);

        //print str_replace('#_', 'jos', $query);

        return $this->getState()->setList($query->toEntityset())->getList();
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
