<?php

/**
 * Location Router
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComLocationsRouter extends ComBaseRouterDefault
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

        if (isset($query['alias']) && isset($query['id'])) {
            $query['id'] = $query['id'].'-'.$query['alias'];
            unset($query['alias']);
        }

        $segments = array_merge($segments, parent::build($query));

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
        $path = implode('/', $segments);
        $vars = array();

        $matches = array();

        if (preg_match('/(\d+)-([^\/]+)/', $path, $matches)) {
            $vars['alias'] = $matches[2];
            $path = str_replace($matches[0], $matches[1], $path);
            $segments = array_filter(explode('/', $path));
        }

        $vars = array_merge($vars, parent::parse($segments));

        if (isset($vars['id']) && current($segments)) {
            $vars['alias'] = array_shift($segments);
        }

        return $vars;
    }
}
