<?php

/**
 * Mentionable Behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComPeopleControllerBehaviorMentionable extends AnControllerBehaviorAbstract
{
    /*
     * contains the list of newly added mentions so they can be notified
     *
     */
    protected $_newly_mentioned = array();

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->registerCallback('after.add', array($this, 'addMentionsFromBody'));
        $this->registerCallback('after.edit', array($this, 'updateMentionsFromBody'));
        $this->registerCallback(array(
            'after.add',
            'after.edit',
        ), array($this, 'notifyMentioned'));
    }

    /**
     * Extracts mention usernames from the entity body and add them to the item.
     */
    public function addMentionsFromBody()
    {
        $entity = $this->getItem();
        $usernames = $this->extractMentions($entity->body);

        foreach ($usernames as $username) {
            $entity->addMention(trim($username));
        }

        $this->_newly_mentioned = $usernames;
    }

    /**
     * Extracts mention usernames from the entity body and updates the entity.
     *
     * @param KCommandContext $context
     *
     * @return bool
     */
    public function updateMentionsFromBody(KCommandContext $context)
    {
        $entity = $this->getItem();
        $body_mentions = $this->extractMentions($entity->body);
        $body_mentions = array_map('strtolower', $body_mentions);

        // @todo we should be accessing username, but for now we are using alias
        // until the design in the domain entities is improved
        $entity_mentions = KConfig::unbox($entity->mentions->alias);

        if (is_array($entity_mentions)) {
            $entity_mentions = array_map('strtolower', $entity_mentions);
        }

        //update removed mentions
      if (is_array($body_mentions)) {
          foreach ($entity_mentions as $mention) {
              if (! in_array($mention, $body_mentions)) {
                  $entity->removeMention($mention);
              }
          }
      }

       //remove the body mentions that already exists in the entity mentions
      if (is_array($entity_mentions)) {
          foreach ($body_mentions as $index => $mention) {
              if (in_array($mention, $entity_mentions)) {
                  unset($body_mentions[$index]);
              }
          }
      }

       //what's left are new mentions. Add them to the entity.
       foreach ($body_mentions as $mention) {
           $entity->addMention(trim($mention));
       }

        //keep the list of new mentions so you can notify them later.
        $this->_newly_mentioned = $body_mentions;
    }

    /**
     * extracts a list of mention usernames from a given text.
     *
     * @return array
     */
    public function extractMentions($text)
    {
        $matches = array();

        if (preg_match_all(ComPeopleDomainEntityPerson::PATTERN_MENTION, $text, $matches)) {
            return array_unique($matches[1]);
        } else {
            return array();
        }
    }

    /**
     * Applies the hashtag filtering to the browse query.
     *
     * @param KCommandContext $context
     */
    protected function _beforeControllerBrowse(KCommandContext $context)
    {
        if (! $context->query) {
            $context->query = $this->_mixer->getRepository()->getQuery();
        }

        if ($this->mention) {
            $query = $context->query;
            $usernames = array();
            $entityType = KInflector::singularize($this->_mixer->getIdentifier()->name);
            $this->mention = (is_string($this->mention)) ? array($this->mention) : $this->mention;

            $edgeType = 'ComTagsDomainEntityTag,ComPeopleDomainEntityMention,com:people.domain.entity.mention';

            $query
            ->join('left', 'edges AS mention_edge', '('.$entityType.'.id = mention_edge.node_b_id AND mention_edge.type=\''.$edgeType.'\')')
            ->join('left', 'nodes AS mention', 'mention_edge.node_a_id = mention.id')
            ->join('left', 'people_people AS person', 'person.node_id = mention.id');

            foreach ($this->mention as $mention) {
                $username = $this->getService('com:people.filter.username')->sanitize($mention);

                if ($username != '') {
                    $usernames[] = $username;
                }
            }

            $query
            ->where('person.username', 'IN', $usernames)
            ->group($entityType.'.id');
        }
    }

    /**
     * Notify the people who have been mentioned.
     *
     * @param KCommandContext $context
     */
    public function notifyMentioned(KCommandContext $context)
    {
        $entity = $this->getItem();
        $subscribers = array();

        foreach ($this->_newly_mentioned as $username) {
            $person = $this->getService('repos:people.person')->find(array('username' => $username));

            if ($person && $person->authorize('mention')) {
                $subscribers[] = $person->id;
            }
        }

        if (count($subscribers) == 0) {
            return;
        }

        if ($entity instanceof ComBaseDomainEntityComment) {
            $parentIdentifier = $entity->parent->getIdentifier()->name;
            $parentController = $this->getService('com:'.KInflector::pluralize($parentIdentifier).'.controller.'.$parentIdentifier);

            if ($parentController->isNotifier() && $entity->parent->isSubscribable()) {
                $data = array(
                    'name' => 'actor_mention_comment',
                    'object' => $entity,
                    'comment' => $entity,
                    'component' => $entity->parent->component,
                    'subscribers' => $subscribers,
                );

                $parentController->createNotification($data);
            }
        } else {
            $data = array(
                'name' => 'actor_mention',
                'object' => $entity,
                'component' => $entity->component,
                'subscribers' => $subscribers,
            );

            $notification = $this->_mixer->createNotification($data);
        }
    }
}
