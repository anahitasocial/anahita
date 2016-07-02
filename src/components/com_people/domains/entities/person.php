<?php

/**
 * Person object. It's the main actor node that represents the social network users. A person can added
 * applications to their profile.
 *
 * Here's how to get a person object, set a property and save
 * <code>
 * //fetches a peron with $id
 * $person = KService::get('repos://site/people.person')->fetch($id);
 * $person->name = 'Doctor Who';
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
final class ComPeopleDomainEntityPerson extends ComActorsDomainEntityActor
{
    /*
     * Clear string passwrod.
     *
     * @var string
     */
    protected $_password;

    /*
    * Allowed user types array
    */
    protected $_allowed_user_types;

    /*
     * Mention regex pattern
     */
    const PATTERN_MENTION = '/@([A-Za-z][A-Za-z0-9_-]{3,})/';

    /*
     * User types
     */
    const USERTYPE_GUEST = 'guest';
    const USERTYPE_REGISTERED = 'registered';
    const USERTYPE_ADMINISTRATOR = 'administrator';
    const USERTYPE_SUPER_ADMINISTRATOR = 'super-administrator';

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
            'attributes' => array(
                'administratingIds' => array(
                    'type' => 'set',
                    'default' => 'set',
                ),
                'username' => array(
                    'column' => 'person_username',
                    'key' => true,
                    'format' => 'username',
                ),
                'userType' => array(
                    'column' => 'person_usertype',
                    'default' => self::USERTYPE_REGISTERED,
                    'write_access' => 'private',
                ),
                'email' => array(
                    'column' => 'person_useremail',
                    'key' => true,
                    'format' => 'email',
                ),
                'givenName' => array(
                    'column' => 'person_given_name',
                    'format' => 'string',
                ),
                'familyName' => array(
                    'column' => 'person_family_name',
                    'format' => 'string',
                ),
                'lastVisitDate' => array(
                    'type' => 'date',
                    'column' => 'person_lastvisitdate',
                ),
                'language' => array('column' => 'person_language'),
                'timezone' => array('column' => 'person_time_zone'),
                'gender' => array('column' => 'actor_gender'),
            ),
            'aliases' => array(
                'registrationDate' => 'creationTime',
                'aboutMe' => 'description',
            ),
            'behaviors' => to_hash(array(
                'describable' => array('searchable_properties' => array('username')),
                'administrator',
                'notifiable',
                'leadable',
                'user',
            )),
        ));

        $config->behaviors->append(array('followable' => array('subscribe_after_follow' => false)));

        parent::_initialize($config);

        AnHelperArray::unsetValues($config->behaviors, array('administrable'));

        $this->_password = array(
            'clear' => null,
            'hashed' => null,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterEntityInstantiate(KConfig $config)
    {
        $config->append(array('data' => array(
            'author' => $this,
            'component' => 'com_people',
        )));

        parent::_afterEntityInstantiate($config);
    }

    /**
     * Set the name, given name and family name of the person.
     *
     * @param string $name The name of the person
     */
    public function setName($name)
    {
        $familyName = $givenName = '';

        if (strpos($name, ' ')) {
            list($givenName, $familyName) = explode(' ', $name, 2);
        } else {
            $givenName = $name;
        }

        $this->set('givenName', $givenName);
        $this->set('familyName', $familyName);
        $this->set('name', $name);
    }

    /**
     * Set the name, given name and family name of the person.
     *
     * @param string $name The name of the person
     */
    public function setFamilyName($name)
    {
        $this->set('familyName', $name);
        $this->set('name', $this->givenName.' '.$this->familyName);
    }

    /**
     * Set the name, given name and family name of the person.
     *
     * @param string $name The name of the person
     */
    public function setGivenName($name)
    {
        $this->set('givenName', $name);
        $this->set('name', $this->givenName.' '.$this->familyName);
    }

    /**
     * Return the username as unique alias.
     *
     * (non-PHPdoc)
     *
     * @see AnDomainEntityAbstract::__get()
     */
    public function __get($key)
    {
        if ($key == 'uniqueAlias') {
            return $this->username;
        }

        return parent::__get($key);
    }

    /**
     * Captures the password value when password is set through
     * magic methods.
     *
     * {@inheritdoc}
     */
    public function __set($key, $value)
    {
        if ($key == 'password' && !empty($value)) {
            return $this->setPassword($value);
        } else {
            return parent::__set($key, $value);
        }
    }

    /**
     * Set a person account passwrod. This password is not stored in the database
     * and only used for validation. @see
     * <code>
     * $person->setPassword('somepassowrd')->validate() //will validate the password
     * </code>.
     *
     * @param string $password Clear password
     *
     * @return ComPeopleDomainEntityPerson
     */
    public function setPassword($password)
    {
        //make sure the passowrd is set to an empty string
        if (empty($password)) {
            $password = null;
        }

        $this->_password['clear'] = $password;

        return $this;
    }

    /**
     * Return a person URL.
     *
     * @param bool $use_username A flag whether to use the username in the URL or not
     *
     * @return string
     */
    public function getURL($use_username = true)
    {
        $url = 'option=com_people&view=person&id='.$this->id;

        if ($use_username) {
            $url .= '&uniqueAlias='.$this->username;
        }

        return $url;
    }

    /**
     * Return the clear password set for validation. If a hash is set to true
     * then it first hash the password and then return it.
     *
     * @param bool $hash.
     *
     * @return string
     */
    public function getPassword($hash = false)
    {
        if ($hash && $this->_password['clear']) {
            if (!$this->_password['hashed']) {
                jimport('joomla.user.helper');
                $salt = JUserHelper::genRandomPassword(32);
                $crypt = JUserHelper::getCryptedPassword($this->_password['clear'], $salt);
                $this->_password['hashed'] = $crypt.':'.$salt;
            }

            return $this->_password['hashed'];
        } else {
            return $this->_password['clear'];
        }
    }

    /**
     * Return whether this person is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return $this->userType === self::USERTYPE_GUEST;
    }

    /**
     * Return if the person user role is Administrator or Super Administrator.
     *
     * @return bool
     */
    public function admin()
    {
        return $this->userType === self::USERTYPE_ADMINISTRATOR ||
               $this->userType === self::USERTYPE_SUPER_ADMINISTRATOR;
    }

    /**
     * return true if the person's role is super admin.
     *
     * @return bool
     */
    public function superadmin()
    {
        return $this->userType === self::USERTYPE_SUPER_ADMINISTRATOR;
    }
}
