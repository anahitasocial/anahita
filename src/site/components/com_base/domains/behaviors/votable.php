<?php


 /**
  * Votable Behavior.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class ComBaseDomainBehaviorVotable extends AnDomainBehaviorAbstract
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
            'attributes' => array(
                'voteUpCount' => array(
                    'default' => 0,
                    'write' => 'private', ),
                'voteDownCount' => array(
                    'default' => 0,
                    'write' => 'private', ),
                'voterUpIds' => array(
                    'type' => 'set',
                    'default' => 'set',
                    'write' => 'private', ),
                'voterDownIds' => array(
                    'type' => 'set',
                    'default' => 'set',
                    'write' => 'private', ),
            ),
            'relationships' => array(
                'voteups' => array(
                    'child' => 'com:base.domain.entity.voteup',
                    'child_key' => 'votee',
                    'parent_delete' => 'ignore', ),
                'votedowns' => array(
                    'child' => 'com:base.domain.entity.votedown',
                    'child_key' => 'votee',
                    'parent_delete' => 'ignore', ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Vote up an entity.
     *
     * @param ComPeopleDomainEntityPerson $voter The voter
     */
    public function voteup($voter)
    {
        //first check if we have voted down if yes then just remove that
        if ($votedown = $this->votedowns->getQuery(true)
                                        ->disableChain()
                                        ->voter($voter)
                                        ->fetch()
        ) {
            $votedown->delete();
        } else {
            //find ore create a voteup
            $voteup = $this->voteups->addNew(array('voter' => $voter, 'votee' => $this));
            //if the votee is suscribable then subscribe the voter
            if ($this->isSubscribable() && !$this->subscribed($voter)) {
                $this->addSubscriber($voter);
            }
            $this->_mixer->execute('after.voteup', new KCommandContext(array('vote' => $voteup)));
        }
    }

    /**
     * Vote down an entity.
     *
     * @param ComPeopleDomainEntityPerson $voter The voter
     */
    public function votedown($voter)
    {
        //first check if we have voted up if yes then just remove that
        if (
            $voteup = $this->voteups->getQuery(true)
                                    ->disableChain()
                                    ->voter($voter)
                                    ->fetch()
        ) {
            $voteup->delete();
        } else {
            //find ore create a voteup
            $votedown = $this->votedowns
                             ->findOrAddNew(array(
                               'voter' => $voter,
                               'votee' => $this, ));
        }
    }

    /**
     * Check if a person voted up.
     *
     * @param ComPeopleDomainEntityPerson $voter The voter
     *
     * @return bool
     */
    public function votedUp($voter)
    {
        return $this->voterUpIds->offsetExists($voter->id);
    }

    /**
     * Check if a person voted up.
     *
     * @param ComPeopleDomainEntityPerson $voter The voter
     *
     * @return bool
     */
    public function votedDown($voter)
    {
        return $this->voterDownIds->offsetExists($voter->id);
    }

    /**
     * Unvote a vote(either a voteup or a votedown).
     *
     * @param ComPeopleDomainEntityPerson $voter The voter
     */
    public function unvote($voter)
    {
        $voteup = $this->voteups->getQuery(true)
                                ->disableChain()
                                ->voter($voter)
                                ->fetch();
        if ($voteup) {
            $voteup->delete();
        }
        $votedown = $this->votedowns->getQuery(true)
                                    ->disableChain()
                                    ->voter($voter)
                                    ->fetch();
        if ($votedown) {
            $votedown->delete();
        }
    }

    /**
     * Reset vote stats.
     *
     * @param array $entities
     */
    public function resetStats(array $entities)
    {
        foreach ($entities as $entity) {
            $ids = $this->getService('repos:base.voteup')
                        ->getQuery()
                        ->votee($entity)
                        ->disableChain()
                        ->fetchValues('voter.id');

            $entity->set('voteUpCount', count($ids));
            $entity->set('voterUpIds', AnDomainAttribute::getInstance('set')->setData($ids));

            $ids = $this->getService('repos:base.votedown')
                        ->getQuery()
                        ->votee($entity)
                        ->disableChain()
                        ->fetchValues('voter.id');

            $entity->set('voteDownCount', count($ids));
            $entity->set('voterDownIds', AnDomainAttribute::getInstance('set')->setData($ids));
        }
    }
 }
