<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Menu
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Router
 *
 * @category   Anahita
 * @package    Com_Menu
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMenuRouter extends ComBaseRouterDefault
{
    /**
     * The query to use for when a user is logged in and accessing the
     * home page
     * 
     * @var array
     */
    protected $_home_query = array();
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_home_query = $config['home_query'];
    }
        
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'home_query' => array('option'=>'com_dashboard','view'=>'dashboard')
        ));
    
        parent::_initialize($config);
    } 
    
    /**
     * (non-PHPdoc)
     * @see ComBaseRouterAbstract::build()
     */
    public function build(&$query)
    {
        $segments = array();
        
        if ( isset($query['id']) ) 
        {
            $item = JSite::getMenu()->getItem($query['id']);                        
            if ( $item ) {
                if ( $item->home ) {
                    unset($query['option']);                    
                } else {
                    $route = $item->route;
                    $segments[] = $route;                    
                }
            }
            unset($query['id']);
        }
        return $segments;
    }
    
    /**
     * (non-PHPdoc)
     * @see ComBaseRouterAbstract::parse()
     */
    public function parse(&$segments)
    {
        $vars = array();
        $menu = &JSite::getMenu();
        if ( !empty($segments) ) 
        {
            $route  = implode('/', $segments);
            $items	= $menu->getItems('route', $route);
        } else 
        {
            $user = JFactory::getUser();
            if ( $user->id > 0 && 
                    !empty($this->_home_query) ) 
            {
                //tries to find a corresponding menu
                //and set menu id
                $query  = $this->_home_query;
                $link   = 'index.php?'.http_build_query($query);
                $items  = $menu->getItems('link', $link);
                if ( !$items ) {
                    $items = $menu->getItems('home', true);
                }
                if ( $items ) 
                {
                    $item = array_pop($items);
                    $query['Itemid'] = $item->id;
                }
                return $query;
            }
            else { 
                $items  = $menu->getItems('home', true);
            }
        }

        if ( !empty($items) )
        {
            foreach($items as $item) 
            {
                if ( $item->type == 'component') 
                {
                    $vars = $item->query;
                    $vars['Itemid'] = $item->id;
                    break;
                }
            }    
        }
        		
		$vars = array_merge(array('option'=>null), $vars);
		
		return $vars;
    }    
}