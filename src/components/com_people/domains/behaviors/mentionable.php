<?php



 /**
  * Mentionable Behavior.
  *
  * @category   Anahita
  *
  * @author     Rastin Mehr <rastin@anahita.io>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.Anahita.io
  */
 class ComPeopleDomainBehaviorMentionable extends AnDomainBehaviorAbstract
 {
     /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $repoIdentifier = $config->mixer->getIdentifier()->identifier;
        $config->append(array(
            'relationships' => array(
                'mentions' => array(
                    'through' => 'com:people.domain.entity.mention',
                    'target' => str_replace('repository', 'entity', $repoIdentifier),
                    'child_key' => 'taggable',
                    'target_child_key' => 'mention',
                    'inverse' => true,
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Adds a person to a mentionable mixer entity.
     *
     * @param a person username
     *
     * @return mixer entity if a valid username is given
     */
    public function addMention($username)
    {
        if (
            $mentioned = $this->getService('repos:people.person')->find(array('username' => $username))
        ) {
            $this->mentions->insert($mentioned);

            if ($this->_mixer->isSubscribable() && !$this->_mixer->subscribed($mentioned)) {
                $this->_mixer->addSubscriber($mentioned);
            }

            return $this;
        } else {
            $this->invalidateUsername($username);

            return;
        }
    }

    /**
     * Removes a person from a mentionable mixer entity.
     *
     * @param a word
     *
     * @return mixer entity if a valid username is given
     */
    public function removeMention($username)
    {
        if (
            $mentioned = $this->getService('repos:people.person')->find(array('username' => $username))
        ) {
            $this->mentions->extract($mentioned);

            if ($this->_mixer->isSubscribable() && $this->_mixer->subscribed($mentioned)) {
                $this->_mixer->removeSubscriber($mentioned);
            }

            return $this;
        } else {
            $this->invalidateUsername($username);

            return;
        }
    }

    /**
     * Removes the @ symbol from a username in the body of the node.
     *
     * @param string username
     */
    protected function invalidateUsername($username)
    {
        $this->_mixer->set('body', AnHelperString::str_ireplace('@'.$username, $username, $this->_mixer->body));
    }

    /**
     * Change the query to include name.
     *
     * Since the target is a simple node. The name field is not included. By ovewriting the
     * tags method we can change the query to include name in the $taggable->tags query
     *
     * @return AnDomainEntitySet
     */
    public function getMentions()
    {
        $this->get('mentions')
        ->getQuery()
        ->select('person.username')
        ->join('left', 'people_people AS person', 'person.node_id = @col(id)');

        return $this->get('mentions');
    }
 }
