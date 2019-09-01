<?php

/**
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc.
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
        $this->_validatePassword($person);

        return parent::validateEntity($person);
    }

    protected function _validatePassword($person)
    {
        if ($person->_raw_password) {
            $validations = $person->getValidator()->getValidations('password');
            $this->validateData($person, 'password', trim($person->_raw_password), $validations);
        }
    }
}
