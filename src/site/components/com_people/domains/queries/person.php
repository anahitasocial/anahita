<?php
/**
 * Person Query
 *
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComPeopleDomainQueryPerson extends AnDomainQueryDefault
{
    /**
     * Build the filter query
     *
     * @return void
     */
    protected function _beforeQuerySelect()
    {
        /*
        if($this->filter_usertype) {
            $this->where('person.person_usertype', '=', $this->filter_usertype);
        }

        if($this->filter_disabled_accounts) {
            $this->where('person.enabled', '=', 0);
        }
        */
    }
}
