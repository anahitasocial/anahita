<?php

/**
 * Default Actor Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsControllerToolbarDefault extends ComBaseControllerToolbarDefault
{
    /**
     * Method type flag.
     *
     * @var bool
     */
    protected $_use_post = false;

    /**
     * If set to true, the element will be updated.
     *
     * @var bool
     */
    protected $_update = true;

    /**
     * Called before list commands.
     */
    public function addListCommands()
    {
        //the context actor
        $actor1 = $this->getController()->actor;

        //the actor entity that the actions are being evaluated against
        $actor2 = $this->getController()->getItem();

        //if actor is not available, then all the against are with respect to the viewer
        $actor1 = pick($actor1, get_viewer());

        //if context actor is administrable (i.e. groups) then actions are administaration actions
        if ($actor1->isAdministrable()) {
            //if adding new followers is allowed
            if ($actor1->authorize('lead')) {
                if ($command = $this->getLeadCommand($actor1, $actor2)) {
                    $labels = array();
                    $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($command->action);
                    $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($command->action);
                    $command->label = translate($labels);
                    $this->addCommand($command);
                }
            }

            $graphType = AnRequest::get('get.type', 'cmd', 'followers');

            if ($actor1->authorize('administration') && $graphType != 'leadables') {
                $this->_update = false;
                if ($command = $this->getBlockCommand($actor1, $actor2)) {
                    $labels = array();
                    $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($command->action);
                    $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($command->action);
                    $command->label = translate($labels);
                    $this->addCommand($command);
                }
            }
        } else {
            $this->_update = true;

            if ($command = $this->getFollowCommand($actor1, $actor2)) {
                $label = pick($command->label, $command->name);
                $labels = array();
                $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($label);
                $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($label);
                $command->label = translate($labels);
                $this->addCommand($command);
            }

            $this->_update = false;

            if ($command = $this->getBlockCommand($actor1, $actor2)) {
                $labels = array();
                $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($command->name);
                $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($command->name);
                $command->label = translate($labels);
                $this->addCommand($command);
            }
        }
    }

    /**
     * Called before toolbar commands.
     */
    public function addToolbarCommands()
    {
        $actor = $this->getController()->getItem();
        $viewer = get_viewer();
        $this->_use_post = true;
        $this->addListCommands();

        if ($actor->authorize('administration')) {
            $this->addCommand('edit-actor');
        }

        if (get_viewer()->admin()) {
            $action = ($actor->verified) ? 'unverify' : 'verify';
            $this->addCommand($action.'-actor');
        }

        if ($actor->authorize('changeEnabled')) {
            $action = ($actor->enabled) ? 'disable' : 'enable';
            $this->addCommand($action.'-actor');
        }

        if ($actor->authorize('access') && !$viewer->eql($actor) && $viewer->following($actor)) {
            $this->addCommand('notifications-settings');
        }
    }

    /**
     * Return commands follow/unfollow if $actor1 (context) can perform follow/unfollow actions on $actor2.
     *
     * @param ComActorsDomainEntityActor  $actor1 Actor who's performing the action
     * @param ComPeopleDomainEntityPerson $actor2 Actor that actions is being performed on
     *
     * @return LibBaseTemplateObject
     */
    public function getFollowCommand($viewer, $actor)
    {
        if ($viewer->eql($actor)) {
            return;
        }

        if ($viewer->following($actor)) {
            if ($actor->authorize('unfollow', array('viewer' => $viewer))) {
                $command = $this->getCommand('follow', array('receiver' => $actor, 'actor' => $viewer, 'action' => 'unfollow'));
                $command->name = 'unfollow';

                return $command;
            }
        } else {
            if ($actor->authorize('requester', array('viewer' => $viewer))) {
                if ($viewer->requested($actor)) {
                    $command = $this->getCommand('follow', array('receiver' => $actor, 'actor' => $viewer, 'action' => 'deleterequest'));
                    $command->name = 'deleterequest';
                    $command->label = 'deleterequest';
                } else {
                    $command = $this->getCommand('follow', array('receiver' => $actor, 'actor' => $viewer, 'action' => 'addrequest'));
                    $command->name = 'addrequest';
                    $command->label = 'addrequest';
                }

                return $command;
            } elseif ($actor->authorize('follower', array('viewer' => $viewer))) {
                $command = $this->getCommand('follow', array('receiver' => $actor, 'actor' => $viewer, 'action' => 'follow'));
                $command->name = 'follow';

                return $command;
            }
        }
    }

    /**
     * Return commands block/unblock if $actor1 (context) can perform unblock/block commands on $actor2.
     *
     * @param ComActorsDomainEntityActor  $actor1 Actor who's performing the action
     * @param ComPeopleDomainEntityPerson $actor2 Actor that actions is being performed on
     *
     * @return LibBaseTemplateObject
     */
    public function getBlockCommand($actor1, $actor2)
    {
        if (!$actor1->isFollowable() || !$actor2->isLeadable()) {
            return;
        }

        if ($actor1->eql($actor2)) {
            return;
        }

        if ($actor1->blocking($actor2)) {
            $command = $this->getCommand('block', array('receiver' => $actor2, 'actor' => $actor1, 'action' => 'unblock'));
            $command->name = 'unblock';

            return $command;
        } elseif ($actor2->authorize('block', array('viewer' => $actor1))) {
            $command = $this->getCommand('block', array('receiver' => $actor2, 'actor' => $actor1, 'action' => 'block'));
            $command->name = 'block';

            return $command;
        }
    }

    /**
     * Lead a person command.
     *
     * @param ComActorsDomainEntityActor  $actor  that is going to be followed
     * @param ComPeopleDomainEntityPerson $person person that is going to be added as a follower to the $actor
     *
     * @return LibBaseTemplateObject
     */
    public function getLeadCommand($actor, $person)
    {
        if (!$actor->isFollowable() || !$person->isLeadable()) {
            return;
        }

        if ($actor->eql($person)) {
            return;
        }

        if ($actor->blocking($person)) {
            return;
        }

        if ($person->following($actor) && $actor->authorize('administration')) {
            $command = $this->getCommand('lead', array('receiver' => $person, 'actor' => $actor, 'action' => 'unlead'));
            $command->name = 'unlead';
        } elseif (!$person->following($actor) && $actor->authorize('lead')) {
            $command = $this->getCommand('lead', array('receiver' => $person, 'actor' => $actor, 'action' => 'lead'));
            $command->name = 'lead';
        } else {
            return;
        }

        return $command;
    }
}
