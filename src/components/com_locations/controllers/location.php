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
        if ($this->locatable) {

            if (in_array($this->getView()->getLayout(), array('selector', 'list_selector'))) {

                $keyword = ($this->q) ? $this->getService('anahita:filter.term')->sanitize($this->q) : '';

                $query = $this->getService('com:locations.domain.query.selector')
                              ->keyword($keyword)
                              ->excludeIds(AnHelperArray::collect($this->locatable->locations, 'id'))
                              ->locatable($this->locatable)
                              ->nearbyLatitude($this->nearby_latitude)
                              ->nearbyLongitude($this->nearby_longitude);

            } else {
                $query = $this->locatable->locations->order('name');
            }

            $query->limit($this->limit, $this->start);

            return $this->getState()->setList($query->toEntityset())->getList();
        }

        return parent::_actionBrowse($context);
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
