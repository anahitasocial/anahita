<?php

/**
 * Search controller searches the node or searchable entities and display the
 * result.
 *
 * The search controller searches name and body of the nodes that are searchable (specicifed by app delegate)
 * for the requested keyword. Once the result is returned, it pass the search result
 * to each app to render
 *
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSearchControllerSearch extends ComBaseControllerResource
{
    /**
    * used to geocode an address
    *
    * @param ComLocationsGeocoderAdapterAbstract object
    */
    protected $_geocoder = null;

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'behaviors' => array('ownable'),
            'toolbars' => array($this->getIdentifier()->name, 'menubar', 'actorbar'),
            'request' => array(
                'limit' => 20,
                'sort' => 'relevant',
                'direction' => 'ASC',
                'term' => '',
                'search_nearby' => '',
                'search_comments' => false,
                'scope' => 'all',
            ),
        ));

        $this->_geocoder = KService::get('com://site/locations.geocoder')->getInstance($config);

        parent::_initialize($config);
    }

    /**
     * Search and return the result.
     *
     * @param KCommandContext $context Controller command chain context
     *
     * @return string The result to render
     */
    protected function _actionGet(KCommandContext $context)
    {
        $this->setView('searches');

        if ($this->actor) {
            $this->getToolbar('actorbar')->setTitle($this->actor->name);
            $this->getService()->set('com://site/search.owner', $this->actor);
        }

        $this->_state
             ->insert('term')
             ->insert('scope')
             ->insert('search_comments')
             ->insert('search_nearby')
             ->insert('search_leaders');

        JFactory::getLanguage()->load('com_actors');

        $this->keywords = array_filter(explode(' ', $this->term));

        $this->scopes = $this->getService('com://site/components.domain.entityset.scope');

        $this->current_scope = $this->scopes->find($this->scope);

        $query = $this->getService('com://site/search.domain.query.node')
                      ->searchTerm($this->term)
                      ->limit($this->limit, $this->start);

        if ($this->actor) {
           $query->ownerContext($this->actor);
        }

        if ($this->search_comments) {
            $query->searchComments($this->search_comments);
        }

        if ($this->search_nearby) {
            if ($lnglat = $this->_geocoder->geocode($this->search_nearby)) {
                $query->searchNearby($lnglat);
            }
        }

        if ($this->current_scope) {
            $query->scope($this->current_scope);
        }

        if ($this->sort == 'recent') {
            $query->order('node.created_on', 'DESC');
        } else {
            $query->orderByRelevance();
        }

        $entities = $query->toEntitySet();
        $this->_state->setList($entities);

        parent::_actionGet($context);
    }

    /**
     * Set the request information.
     *
     * @param array An associative array of request information
     *
     * @return LibBaseControllerAbstract
     */
    public function setRequest(array $request)
    {
        parent::setRequest($request);

        if (isset($this->_request->term)) {
            $term = $this->getService('anahita:filter.term')->sanitize($this->_request->term);
            $this->_request->term = $term;
            $this->term = $term;
        }

        return $this;
    }
}
