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
 * @version    SVN: $Id$
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Person object. It's the main actor node that represents the social network users. A person can added
 * applications to their profile.
 *
 * Here's how to get a person object, set a property and save
 * <code>
 * //fetches a peron with $id
 * $person = KService::get('repos:people.person')->fetch($id);
 * $person->name = 'James Bond';
 * $person->save();
 * </code>
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleDomainValidatorPerson extends AnDomainValidatorAbstract
{
    /**
     * Validates a person object.
     *
     * @param ComPeopleDomainEntityPerson $person
     *
     * @return bool
     */
    public function validateEntity($person)
    {
        $this->_validateField($person, 'email');
        $this->_validateField($person, 'username');
        $this->_validateField($person, 'password');

        return parent::validateEntity($person);
    }

    private function _validateField($person, $field)
    {
        if ($person->$field) {
            if (!$this->getFilter($field)->validate($person->$field)) {
                $person->addError(array(
                    'message' => "Invalid $field format",
                    'code' => AnError::INVALID_FORMAT,
                    'key' => $field,
                    'format' => $field
                ));
            }
        }
    }
}
