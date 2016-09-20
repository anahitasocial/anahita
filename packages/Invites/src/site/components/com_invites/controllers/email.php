<?php

/**
 * Invite Default Contorller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComInvitesControllerEmail extends ComInvitesControllerDefault
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
            'behaviors' => array('com://site/mailer.controller.behavior.mailer'),
        ));

        parent::_initialize($config);
    }

    /**
     * Calls the invite action.
     *
     * (non-PHPdoc)
     *
     * @see ComInvitesControllerDefault::_actionPost()
     */
    protected function _actionPost($context)
    {
        return $this->execute('invite', $context);
    }

    /**
     * Read.
     *
     * @param KCommandContext $contxt
     */
    protected function _actionInvite($context)
    {
        $data = $context->data;
        $viewer = get_viewer();
        $siteConfig = JFactory::getConfig();

        $emails = $data->email;

        foreach ($emails as $email) {

            if ($this->getService('koowa:filter.email')->validate($email)) {

                $person = $this->getService('repos://site/people')->find(array('email' => $email));

                if (!$person) {

                    $token = $this->getService('repos://site/invites.token')->getEntity(array(
                        'data' => array(
                            'inviter' => $viewer,
                            'serviceName' => 'email',
                        ),
                    ));

                    $token->save();

                    $mail[] = array(
                        'subject' => JText::sprintf('COM-INVITES-MESSAGE-SUBJECT', $siteConfig->getValue('sitename')),
                        'to' => $email,
                        'layout' => false,
                        'template' => 'invite',
                        'data' => array(
                            'invite_url' => $token->getURL(),
                            'site_name' => $siteConfig->getValue('sitename'),
                            'sender' => $viewer,
                        ),
                    );

                    $this->mail($mail);

                }// if person doesn't exists
            }// email validation
        }//foreach
    }
}
