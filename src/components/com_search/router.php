<?php

/**
 * Base Router.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSearchRouter extends ComBaseRouterDefault
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
        $segments = array();

        if (isset($query['oid'])) {
            $segments[] = '@'.$query['oid'];
        }

        if (isset($query['q'])) {
            $segments[] = $query['q'];
        }

        //we don't need the view
        unset($query['view']);
        unset($query['oid']);
        unset($query['q']);

        return $segments;
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
        $vars = array();

        if (preg_match('/@\w+/', current($segments))) {
            $vars['oid'] = str_replace('@', '', array_shift($segments));
        }

        if (count($segments)) {
            $vars['term'] = array_pop($segments);
        }

        return $vars;
    }
}
