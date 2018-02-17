<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Hashtag Router.
 *
 * @category   Anahita
 */
class ComHashtagsRouter extends ComBaseRouterDefault
{
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

        if (empty($segments)) {
            $vars['view'] = $this->getIdentifier()->package;
        } elseif (count($segments) == 1) {
            $identifier = array_pop($segments);

            if (is_numeric($identifier)) {
                $vars['id'] = (int) $identifier;
            } else {
                $vars['alias'] = $identifier;
            }

            $vars['view'] = AnInflector::singularize($this->getIdentifier()->package);
        }

        return $vars;
    }

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

        if (isset($query['view'])) {
            unset($query['view']);
        }

        if (isset($query['id'])) {
            unset($query['id']);
        }

        if (isset($query['alias'])) {
            $segments[] = $query['alias'];
            unset($query['alias']);
        }

        return $segments;
    }
}
