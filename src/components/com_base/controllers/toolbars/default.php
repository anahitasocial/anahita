<?php

/**
 * Default Controller Toolbar.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseControllerToolbarDefault extends ComBaseControllerToolbarAbstract
{
    /**
     * Before Controller _actionRead is executed.
     *
     * @param AnEvent $event
     */
    public function onBeforeControllerGet(AnEvent $event)
    {
        $this->getController()->toolbar = $this;
    }

    /**
     * Vote Command for an entity.
     *
     * @param LibBaseTemplateObject $command The action object
     */
    protected function _commandVote($command)
    {
        $entity = $this->getController()->getItem();
        $voted = $entity->votedUp(get_viewer());

        $action = $voted ? 'unvote' : 'vote';

        if (is($entity, 'ComBaseDomainEntityComment')) {
            $action .= 'comment';
        }

        $command->setName($action);
    }
}
