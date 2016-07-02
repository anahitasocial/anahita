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
 * $person = KService::get('repos://site/people.person')->fetch($id); 
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
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'validations' => array(
                'username' => array(
                    'length' => array('max' => 100),
                    'format' => 'username',
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Validates a person object.
     * 
     * @param ComPeopleDomainEntityPerson $person
     * 
     * @return bool
     */
    public function validateEntity($person)
    {
        //if a password is set then validate the password
        if ($password = $person->getPassword()) {
            if (!$this->getFilter('password')->validate($password)) {
                $person->addError(array(
                    'message' => 'Invalid password format',
                    'code' => AnError::INVALID_FORMAT,
                    'key' => 'password',
                ));
            }
        }

        return parent::validateEntity($person);
    }
}
