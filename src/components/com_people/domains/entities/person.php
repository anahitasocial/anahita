<?php

/**
 * Person object. It's the main actor node that represents the social network users. A person can added
 * applications to their profile.
 *
 * Here's how to get a person object, set a property and save
 * <code>
 * //fetches a peron with $id
 * $person = AnService::get('repos:people.person')->fetch($id);
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
    * Allowed user types array
    */
    protected $_allowed_user_types;
    
    /*
    * Holds Password value in plain text
    */
    public $_raw_password;

    /*
     * Mention regex pattern
     */
    const PATTERN_MENTION = '/(?<=\s|^)@([A-Za-z][A-Za-z0-9_-]{3,})/u';

    /*
     * User types
     */
    const USERTYPE_GUEST = 'guest';
    const USERTYPE_REGISTERED = 'registered';
    const USERTYPE_ADMINISTRATOR = 'administrator';
    const USERTYPE_SUPER_ADMINISTRATOR = 'super-administrator';
    
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    CONST GENDER_NEUTRAL = 'neutral';

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'resources' => array('people_people'),
            'attributes' => array(
                'administratingIds' => array(
                    'type' => 'set',
                    'default' => 'set'
                ),
                'alias' => array(
                    'key' => true,
                ),
                'givenName' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'min' => 2,
                        'max' => 20,
                    ),
                ),
                'familyName' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'min' => 2,
                        'max' => 20,
                    ),
                ),
                'email' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'unique' => true,
                    'format' => 'email',
                    'length' => array(
                        'min' => 8,
                        'max' => 80,
                    ),
                ),
                'username' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'key' => true,
                    'unique' => true,
                    'format' => 'username',
                    'length' => array(
                        'min' => 3,
                        'max' => 30,
                    ),
                ),
                'password' => array(
                    'read' => 'protected',
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'length' => array(
                        'min' => 8,
                        'max' => 80,
                    ),
                ),
                'usertype' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'default' => self::USERTYPE_GUEST,
                ),
                'gender' => array(
                    'required' => AnDomain::VALUE_NOT_EMPTY,
                    'default' => self::GENDER_NEUTRAL,
                ),
                'lastVisitDate' => array(
                    'default' => 'date'
                ),
                'activationCode'
            ),
            'aliases' => array(
                'registrationDate' => 'creationTime'
            ),
            'behaviors' => to_hash(array(
                //@todo if viewer is admin, then make email searchable too
                'describable' => array(
                    'searchable_properties' => array(
                        'givenName',
                        'familyName',
                        'username',
                        'email'
                    )
                ),
                'administrator',
                'notifiable',
                'leadable',
            )),
        ));

        $config->behaviors->append(array(
            'followable' => array('subscribe_after_follow' => false)
        ));

        parent::_initialize($config);

        AnHelperArray::unsetValues($config->behaviors, array('administrable'));
    }
    
    /**
     * Set the username and alias in the same time
     *
     * @param string $username The username of the person
     */
    public function setUsername($username) 
    {
        $this->set('alias', $username);
        $this->set('username', $username);
        
        return $this;
    }
    
    /**
     * Set the raw and encyrpted passwords in the same time
     *
     * @param string $password The password of the person
     */
    public function setPassword($password) {
        $this->_raw_password = trim($password);
        $encrypted = password_hash($this->_raw_password, PASSWORD_DEFAULT);
        $this->set('password', $encrypted);
        
        return $this;
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

        return $this;
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

        return $this;
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
            $url .= '&uniqueAlias='.strtolower($this->alias);
        }

        return $url;
    }

    /**
     * Return whether this person is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return $this->usertype === self::USERTYPE_GUEST;
    }

    /**
     * Return if the person user role is Administrator or Super Administrator.
     *
     * @return bool
     */
    public function admin()
    {
        return $this->usertype === self::USERTYPE_ADMINISTRATOR ||
               $this->usertype === self::USERTYPE_SUPER_ADMINISTRATOR;
    }

    /**
     * return true if the person's role is super admin.
     *
     * @return bool
     */
    public function superadmin()
    {
        return $this->usertype === self::USERTYPE_SUPER_ADMINISTRATOR;
    }

    public function visited()
    {
        $this->lastVisitDate = AnDomainAttributeDate::getInstance();
    }
    
    protected function _createRandomHash()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * Automatically sets the activation token for the user.
     *
     * @return LibUsersDomainEntityUser
     */
    public function requiresActivation()
    {
        $this->activationCode = $this->_createRandomHash();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterEntityInstantiate(AnConfig $config)
    {
        $config->append(array('data' => array(
            'author' => $this,
            'component' => 'com_people',
            'enabled' => false,
        )));

        parent::_afterEntityInstantiate($config);
    }

    protected function _beforeEntityInsert(AnCommandContext $context)
    {        
        $this->lastVisitDate = AnDomainAttributeDate::getInstance(new AnConfig(array(
            'date' => array(
                'hour' => 0,
                'minute' => 0,
                'second' => 0,
                'partsecond' => 0,
                'year' => '1000',
                'month' => '01',
                'day' => '01'
            )
        )));
    }
}
