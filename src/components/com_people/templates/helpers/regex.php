<?php

/**
 * Javascript Regex for forms
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleTemplateHelperRegex extends LibBaseTemplateHelperAbstract
{
    /**
    * email Javascript regex
    *
    * @return string regex
    */
    public function email()
    {
        return "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]*$";
    }

    /**
    * username Javascript regex
    *
    * @return string regex
    */
    public function username()
    {
        return "[A-Za-z][A-Za-z0-9_-]*";
    }
}
