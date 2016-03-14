<?php

/**
* Enablable Behavior.
*
* @category   Anahita
*
* @author     Rastin Mehr <rastin@anahitapolis.com>
* @copyright  2008 - 2016 rmd Studio Inc.
* @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
*
* @link       http://www.GetAnahita.com
*/
class ComBaseControllerBehaviorVerifiable extends KControllerBehaviorAbstract
{
    /**
    * set verify to true
    *
    * @return void
    */
    protected function _actionVerify($context)
    {
        $context->response->status = KHttpResponse::RESET_CONTENT;
        $this->getItem()->verify();

        return;
    }

    /**
    * set verify to false
    *
    * @return void
    */
    protected function _actionDeverify($context)
    {
        $context->response->status = KHttpResponse::RESET_CONTENT;
        $this->getItem()->deverify();

        return;
    }

    /**
     * Authorize verify
     *
     * @return bool
     */
    public function canVerify()
    {
        $viewer = get_viewer();
        return $viewer->admin();
    }

    /**
     * Authorize deverify
     *
     * @return bool
     */
    public function canDeverify()
    {
        $viewer = get_viewer();
        return $viewer->admin();
    }
}
