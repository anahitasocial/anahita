<?php

/**
 * Votable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */

class ComBaseControllerBehaviorVotable extends AnControllerBehaviorAbstract
{
    /**
     * Renders ComBaseTemplateHelperUi::vote().
     *
     * @param AnCommandContext $context Context parameter
     *
     * @return string
     */
    protected function _actionGetvoters($context)
    {
        $this->commit();

        $voters = $this->getItem()->voteups->voter;
        $controller = $this->getService('com:actors.controller.actor')
                            ->view('actors')
                            ->format($context->request->getFormat());

        $controller->getState()->setList($voters);

        return $controller->getView()->display();
    }

    /**
     * Subscribe the viewer to the subscribable object.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionVote($context)
    {
        $context->response->status = AnHttpResponse::CREATED;

        $this->getItem()->voteup(get_viewer());

        $notification = $this->_mixer->createNotification(array(
            'name' => 'voteup',
            'object' => $this->getItem(),
            'component' => $this->getItem()->component,
        ));

        $context->response->content = $this->_mixer->execute('getvoters', $context);
    }

    /**
     * Remove the viewer's subscription from the subscribable object.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionUnvote($context)
    {
        $this->getItem()->unvote(get_viewer());

        $context->response->content = $this->_mixer->execute('getvoters', $context);
    }
}
