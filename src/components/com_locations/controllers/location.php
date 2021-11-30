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
 * @link       http://www.Anahita.io
 */
class ComLocationsControllerLocation extends ComTagsControllerDefault
{
    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        if ($this->taggable_id) {
            $this->registerCallback(
                array('before.browse', 'before.read'),
                array($this, 'fetchTaggable')
            );
        }
    }

    /**
     * Browse Service
     * @todo move all queries to the query class
     *
     * @param AnCommandContext $context
     */
    protected function _actionBrowse(AnCommandContext $context)
    {
        if ($this->taggable) {
            if (in_array($this->getView()->getLayout(), array('selector', 'list_selector'))) {
                $keyword = ($this->q) ? $this->getService('anahita:filter.term')->sanitize($this->q) : '';
                $query = $this->getService('com:locations.domain.query.selector')
                              ->keyword($keyword)
                              ->excludeIds(AnHelperArray::collect($this->taggable->locations, 'id'))
                              ->taggable($this->taggable)
                              ->nearbyLatitude($this->nearby_latitude)
                              ->nearbyLongitude($this->nearby_longitude);

            } else {
                $query = $this->taggable->locations->order('name');
            }

            $query->limit($this->limit, $this->start);

            return $this->getState()->setList($query->toEntityset())->getList();
        }

        return parent::_actionBrowse($context);
    }
    
    public function fetchTaggable(AnCommandContext $context)
    {
        parent::fetchTaggable($context);

        if(!$this->taggable->getRepository()->hasBehavior('geolocatable')) {
            throw new LibBaseControllerExceptionBadRequest('Object does not have locatable behavior');
        }

        return $this->taggable;
    }
}
