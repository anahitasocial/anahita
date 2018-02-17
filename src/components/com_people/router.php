<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleRouter extends ComActorsRouterDefault
{
    /**
     * Build the route.
     *
     * @param   array   An array of URL arguments
     *
     * @return array The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
        if (isset($query['uniqueAlias'])) {
            $query['id'] = $query['uniqueAlias'];
            unset($query['uniqueAlias']);
        }

        return parent::build($query);
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array   The segments of the URL to parse.
     * 
     * @return array The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $query = array();
        $path = implode('/', $segments);

        if ($path == 'signup') {
            return array('view' => 'person','layout' => 'signup');
        } elseif (
             count($segments) &&
             !is_numeric($segments[0]) &&
             !in_array(AnInflector::singularize($segments[0]), array('person', 'session', 'token'))
        ) {
            $query['username'] = $segments[0];
            //@TODO the parent::parse wants a numeric ID in order
            //to parse correctly. For now lets hack it
            $segments[0] = 10;
            $query['view'] = 'person';
            $query = array_merge(parent::parse($segments), $query);
            unset($query['id']);
        } else {
            if (preg_match('/tokens\/.*/', $path)) {
                $query['view'] = 'token';
                $query['id'] = array_pop($segments);
            } else {
                $query = parent::parse($segments);
            }
        }

        return $query;
    }
}
