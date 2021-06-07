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
    * used to only use a substring of the passed search term
    *
    * @param int
    */
    const SEARCH_TERM_CHAR_LIMIT = 100;

    /**
     * Constructor
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_geocoder = $this->getService('com:locations.geocoder', array('config' => $config));
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'behaviors' => array('ownable'),
            'toolbars' => array($this->getIdentifier()->name, 'menubar', 'actorbar'),
            'request' => array(
                'limit' => 20,
                'sort' => 'relevant',
                'direction' => 'ASC',
                'term' => '',
                'coord_lng' => 0.0,
                'coord_lat' => 0.0,
                'search_range' => 100,
                'search_comments' => false,
                'scope' => 'all',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Search and return the result.
     *
     * @param AnCommandContext $context Controller command chain context
     *
     * @return string The result to render
     */
    protected function _actionGet(AnCommandContext $context)
    {
        $this->getService('repos:search.node')
        ->addBehavior('privatable')
        ->addBehavior('com:locations.domain.behavior.geolocatable');
        
        $this->setView('searches');

        if ($this->actor) {
            $this->getToolbar('actorbar')->setTitle($this->actor->name);
            $this->getService()->set('com:search.owner', $this->actor);
        }

        $this->_state
             ->insert('term')
             ->insert('scope')
             ->insert('search_comments')
             ->insert('search_distance')
             ->insert('search_range')
             ->insert('search_leaders');

        $this->keywords = array_filter(explode(' ', $this->term));

        $this->scopes = $this->getService('com:components.domain.entityset.scope');

        $this->current_scope = $this->scopes->find($this->scope);

        $query = $this->getService('com:search.domain.query.node')
                      ->searchTerm($this->term)
                      ->limit($this->limit, $this->start);                         

        if ($this->actor) {
           $query->ownerContext($this->actor);
        }

        if ($this->search_comments) {
            $query->searchComments($this->search_comments);
        }

        if ($this->current_scope) {
            $query->scope($this->current_scope);
        }

        if ($this->sort == 'recent') {
            $query->order('node.created_on', 'DESC');
        } else {
            $query->orderByRelevance();
        }

        if ($this->coord_lng && $this->coord_lat) {
            $lnglat['longitude'] = $this->coord_lng;
            $lnglat['latitude'] = $this->coord_lat;
            
            $query->searchDistance($lnglat);
            $query->searchRange($this->search_range);
            
            $query->order('distance', 'ASC');
        }

        // error_log(str_replace('#_', 'jos', $query));

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
            $substr = AnHelperString::substr($this->_request->term, 0, self::SEARCH_TERM_CHAR_LIMIT);
            $value = $this->getService('anahita:filter.term')->sanitize($substr);
            
            $this->_request->term = $value;
            $this->term = $value;
        }

        $this->search_range = (int) $this->_request->search_range;
        
        if (isset($this->_request->coord_long) && isset($this->_request->coord_lat)) {
            $this->coord_lng = floatval($this->_request->coord_lng);
            $this->coord_lat = floatval($this->_request->coord_lat);
        }
        
        $value = $this->_request->search_comments == 1 || $this->_request->search_comments == 'true';

        $this->_request->search_comments = $value;
        $this->search_comments = $value;

        return $this;
    }
}
