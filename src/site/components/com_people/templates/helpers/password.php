<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Password Helper
 *
 * @category   Anahita
 * @package    Com_People
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPeopleTemplateHelperPassword extends KTemplateHelperAbstract
{
    /**
     * Renders a password input with the validation
     * 
     * @param boolean $required A boolean flag whether the password is
     * required or not 
     * 
     * @return void
     */
    public function input($required = true)
    {
        $validators = '';
        $min = ComPeopleFilterPassword::$MIN_LENGTH;
        if ( $required ) {
            $validators .= 'required  minLength:'.$min;
        }
        return '<input class="input-block-level" data-validators="'.$validators.' validate-passwod" type="password" id="password" value="" name="password" />';
    }
}