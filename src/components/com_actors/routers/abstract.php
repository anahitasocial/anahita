<?php

/**
 * Abstract Actor Router.
 *
 * @category   Anahita
 */
abstract class ComActorsRouterAbstract extends ComBaseRouterDefault
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
        if (isset($query['alias']) && isset($query['id'])) {
            if (!isset($query['get'])) {
                $query['id'] = $query['id'].'-'.$query['alias'];
            }

            unset($query['alias']);
        }

        $has_id = isset($query['id']);
        $segments = parent::build($query);

        if ($has_id) {
            if (isset($query['get'])) {
                $segments[] = $query['get'];

                if ($query['get'] == 'graph') {
                    if (!isset($query['type'])) {
                        $query['type'] = 'followers';
                    }

                    $segments[] = $query['type'];

                    unset($query['type']);
                }

                unset($query['get']);
            }
        } elseif (isset($query['oid'])) {
            if ($query['oid'] == 'viewer') {
                $query['oid'] = get_viewer()->uniqueAlias;
            }

            $segments[] = '@'.$query['oid'];

            unset($query['oid']);
        }

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

        $last = AnHelperArray::getValueAtIndex($segments, AnHelperArray::LAST_INDEX);

        if (preg_match('/@\w+/', $last)) {
            $vars['oid'] = str_replace('@', '', array_pop($segments));
        }

        $vars = array_merge($vars, parent::parse($segments));

        if (isset($vars['get']) && $vars['get'] == 'graph') {
            $vars['type'] = count($segments) ? array_shift($segments) : 'followers';
        }

        return $vars;
    }
}
