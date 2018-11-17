<?php
/**
 * @package     Anahita_Filter
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.GetAnahita.com
 */
 
class AnFilterChain extends AnCommandChain
{
    /**
     * Run the commands in the chain
     *
     * @param string  The filter name
     * @param array   The data to be filtered
     * @return  mixed
     */
    final public function run( $name, AnCommandContext $context )
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
    final protected function _validate( AnCommandContext $context )
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
    final protected function _sanitize( AnCommandContext $context )
    {
        foreach($this as $filter) {
            $context->data = $filter->execute( 'sanitize', $context );
        }

        return $context->data;
    }
}