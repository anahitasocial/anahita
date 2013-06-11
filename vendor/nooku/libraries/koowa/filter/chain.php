<?php
/**
 * @version		$Id: chain.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Filter
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Filter Chain
 *
 * The filter chain overrides the run method to implement a seperate
 * validate and santize method
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Filter
 */
class KFilterChain extends KCommandChain
{
    /**
     * Run the commands in the chain
     *
     * @param string  The filter name
     * @param array   The data to be filtered
     * @return  mixed
     */
    final public function run( $name, KCommandContext $context )
    {
        $function = '_'.$name;
        $result =  $this->$function($context);
        return $result;
    }

    /**
     * Validate the data
     *
     * @param   scalar  Value to be validated
     * @return  bool    True when the data is valid
     */
    final protected function _validate( KCommandContext $context )
    {
        foreach($this as $filter)
        {
            if ( $filter->execute( 'validate', $context ) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sanitize the data
     *
     * @param   scalar  Valuae to be sanitized
     * @return  mixed
     */
    final protected function _sanitize( KCommandContext $context )
    {
        foreach($this as $filter) {
            $context->data = $filter->execute( 'sanitize', $context );
        }

        return $context->data;
    }
}